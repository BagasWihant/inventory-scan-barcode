<?php

namespace App\Livewire;

use Livewire\Component;
use Carbon\Carbon;
use App\Models\itemIn;
use App\Models\tempCounterSiws;
use Livewire\Attributes\On;
use Livewire\WithPagination;
use App\Exports\ScannedExport;
use App\Models\abnormalMaterial;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class ReceivingSIWS extends Component
{
    use WithPagination;

    protected $listeners = ['editQty'];
    private $tableTemp = 'temp_counter_siws';
    private $tableSetupMst = 'material_setup_mst';
    // private $table = ''

    public $userId, $products, $produkBarcode, $paletBarcode, $previousPaletBarcode, $sws_code, $qtyPerPax, $trucking_id, $paletInput = false;

    public $scannedCounter = [];
    public $scanned = [];
    public $productsInPalet = [];
    public $props = [0, 'No Data'];

    public function mount()
    {
        $this->userId = auth()->user()->id;
    }

    #[On('insertNew')]
    public function insertNew(int $qty = 0, $save = true, $update = false)
    {
        if ($save) {
            $arr = [];
            array_push($arr, $qty);
            tempCounterSiws::create([
                'material' => $this->sws_code,
                'palet' => $this->paletBarcode,
                'userID' => $this->userId,
                'sisa' => "-$qty",
                'total' => $qty,
                'counter' => $qty,
                'pax' => 1,
                'qty_more' => 1,
                'prop_scan' => json_encode($arr),
            ]);

            DB::table($this->tableSetupMst)->insert([
                'pallet_no' => $this->paletBarcode,
                'serial_no' => '00000',
                "material_no" => $this->sws_code,
                'picking_qty' => $qty,
                'line_c' => 'NewItem',
                'setup_by' => 'dev',
                'setup_date' => Carbon::now(),
            ]);
        }
        if ($update) {
            $tempCount = DB::table($this->tableTemp)
                ->where('material', $this->sws_code)
                ->where('userID', $this->userId)
                ->where('palet', $this->paletBarcode);
            $data = $tempCount->first();
            $counter = $data->counter + $qty;

            $new_prop_scan = isset($data->prop_scan) ? json_decode($data->prop_scan) : [];
            array_push($new_prop_scan, $qty);

            $sisa = $data->sisa - $qty;
            if ($data->total < $data->counter || $data->sisa <= 0) {

                $this->produkBarcode = null;
                $more = $data->qty_more + 1;
                $tempCount->update(['counter' => $counter, 'sisa' => $sisa, 'qty_more' => $more, 'prop_scan' => json_encode($new_prop_scan)]);
                $this->refreshTemp();
                return;
            } else {

                $tempCount->update([
                    'counter' => $counter,
                    'sisa' => $sisa,
                    'prop_scan' => json_encode($new_prop_scan),
                ]);
            }
        }

        $this->refreshTemp();
        $this->produkBarcode = null;
    }

    public function paletBarcodeScan()
    {
        $this->paletBarcode = substr($this->paletBarcode, 0, 10);
        // dump($this->paletBarcode !== $this->previousPaletBarcode);
        if (strlen($this->paletBarcode) > 2) {
            DB::table($this->tableTemp)->where('userID', $this->userId)->where('flag', 0)->delete();
            $truk = DB::table($this->tableSetupMst)->where('pallet_no', $this->paletBarcode)->select('kit_no')->first();
            if ($truk) {
                $this->dispatch('produkFocus');

                $this->trucking_id = $truk->kit_no;
                $this->paletInput = true;
                $this->previousPaletBarcode = $this->paletBarcode;
                $this->refreshData();
            } else {
                $this->paletBarcode = null;
            }
        } else {
            // $this->paletInput = true;
            $this->dispatch('paletFocus');
        }
    }

    public function productBarcodeScan()
    {
        if ($this->products->count() == 0) {
            $this->produkBarcode = null;
            return;
        }

        if (strlen($this->produkBarcode) > 2) {
            $mode = env('KIAS_MODE', true); // mode buat lokal ku
            $lineCode = null;

            $allowText = ['m', 'c'];
            $pattern = '/^\d{2}-\d{4}$/';

            $firstText = strtolower(substr($this->paletBarcode, 0, 1));

            if ($mode) {
                if (in_array($firstText, $allowText)) {

                    // CTI000400ASSY250304XMR1382000830080S32107-3K6-K502   000002
                    if ($firstText == "c") {
                        $lineCode            = substr($this->produkBarcode, 19, 4);
                        $produkBarcode       = substr($this->produkBarcode, 23, 13);


                        // cek jika ada T itu termasuk barcode
                        $this->produkBarcode = substr($produkBarcode, -1) === "T"
                            ? $produkBarcode
                            : substr($produkBarcode, 0, 12);
                    }

                    // K21759769242168XHN32702250001
                    if ($firstText == "m") {
                        $lineCode            = substr($this->produkBarcode, 15, 4);
                        $this->produkBarcode = substr($this->produkBarcode, 7, 8);
                    }

                    $key = 'conversion_' . $this->produkBarcode;
                    $supplierCode = Cache::remember($key, 30, function () {
                        return DB::table('material_conversion_mst')->where('supplier_code', $this->produkBarcode)->select('sws_code')->first();
                    });
                }

                // jika nomor saja masuk sini
                if (preg_match($pattern, $this->paletBarcode)) {
                    $key = 'serialno_' . $this->produkBarcode;
                    $supplierCode = Cache::remember($key, 30, function () {
                        return DB::table($this->tableSetupMst)->where('serial_no', $this->produkBarcode)->select('serial_no as sws_code')->first();
                    });
                }
            }

            if ($supplierCode) {
                $this->sws_code = $supplierCode->sws_code;

                $column = preg_match($pattern, $this->paletBarcode) ? 'serial_no' : (in_array($firstText, $allowText) ? 'material' : null);
                $tempCount = DB::table($this->tableTemp)
                    ->where($column, $supplierCode->sws_code)
                    ->where('userID', $this->userId)
                    ->where('palet', $this->paletBarcode);
                $lineCode != null ? $tempCount->where('line_c', $lineCode) : null;
                $data = $tempCount->first();

                $key = 'count_' . $supplierCode->sws_code . '_' . $this->paletBarcode . '_' . $this->userId . '_' . $lineCode;
                $count = Cache::remember($key, 30, function () use ($supplierCode, $column, $lineCode) {
                    $sql = DB::table($this->tableTemp)
                        ->where($column, $supplierCode->sws_code)
                        ->where('userID', $this->userId)
                        ->where('palet', $this->paletBarcode);
                    $lineCode != null ? $sql->where('line_c', $lineCode) : null;
                    return $sql->count();
                });


                if ($count > 0) {
                    // tambahi cache 30s
                    $key = 'material_setup_' . $supplierCode->sws_code . '_' . $this->paletBarcode . '_' . $this->userId . '_' . $lineCode;
                    $firstText = strtolower(substr($this->paletBarcode, 0, 1));
                    $allowText = ['m', 'c'];
                    $pattern = '/^\d{2}-\d{4}$/';

                    $column = preg_match($pattern, $this->paletBarcode) ? 'serial_no' : (in_array($firstText, $allowText) ? 'material_no' : null);
                    $qry = Cache::remember($key, 20, function () use ($supplierCode, $column, $lineCode) {
                        $sql = DB::table($this->tableSetupMst)
                            ->selectRaw('sum(picking_qty) as picking_qty')
                            ->where($column, 'like', $supplierCode->sws_code . '%')
                            ->where('pallet_no', $this->paletBarcode);
                        $lineCode != null ? $sql->where('line_c', $lineCode)->groupBy($column,'line_c') : $sql->groupBy($column);
                        return $sql->get();
                    });

                    $productDetail = $qry->first();

                    $counter = $data->counter + $productDetail->picking_qty;
                    $sisa = $data->sisa - $productDetail->picking_qty;

                    $new_prop_scan = isset($data->prop_scan) ? json_decode($data->prop_scan) : [];
                    array_push($new_prop_scan, $productDetail->picking_qty);


                    if ($data->total < $data->counter || $data->sisa <= 0) {

                        $this->produkBarcode = null;
                        $more = $data->qty_more + 1;
                        $tempCount->update(['counter' => $counter, 'sisa' => $sisa, 'qty_more' => $more, 'prop_scan' => json_encode($new_prop_scan)]);
                        $this->refreshTemp();
                        return;
                    }

                    $tempCount->update(['counter' => $counter, 'sisa' => $sisa, 'prop_scan' => json_encode($new_prop_scan)]);
                    $this->refreshTemp();
                } else {

                    $cek = DB::table($this->tableSetupMst)
                        ->selectRaw('max(picking_qty) as qty')
                        ->where('serial_no', $supplierCode->sws_code)->first();

                    $this->dispatch('newItem2', ['qty' => $cek->qty, 'title' => 'New Item Detected']);
                    return;
                }
            }
        }
        $this->produkBarcode = null;
    }


    private function refreshTemp()
    {
        $this->scanned = DB::table($this->tableTemp . ' as a')
            ->leftJoin('matloc_temp_CNCKIAS2 as b', function ($join) {
                $join->on('a.material', '=', 'b.material_no')
                    ->orOn('a.line_c', '=', 'b.location_cd');
            })
            // ->leftJoin('matloc_temp_CNCKIAS2 as b', 'a.material', '=', 'b.material_no')
            // ->leftJoin('matloc_temp_CNCKIAS2 as b', 'a.line_c', '=', 'b.location_cd')
            ->where('palet', $this->paletBarcode)
            ->select('a.*', 'b.location_cd')
            ->where('userID', $this->userId)
            ->orderByDesc('pax')
            ->orderByDesc('material')
            ->get();
    }

    private function refreshData()
    {
        $getScanned = DB::table('material_in_stock')->select('material_no')
            ->where('pallet_no', $this->paletBarcode)
            ->union(DB::table('abnormal_materials')->select('material_no')->where('pallet_no', $this->paletBarcode))
            ->pluck('material_no')
            ->all();


        $getScannedString = implode(',', $getScanned);

        // $firstText = strtolower(substr($this->paletBarcode, 0, 1));
        // $allowText = ['m', 'c'];
        $pattern = '/^\d{2}-\d{4}$/';

        if (preg_match($pattern, $this->paletBarcode)) {

            // tambah cache
            $key = '0picking_' . $this->paletBarcode . '_' . $this->userId;
            $collection = Cache::remember($key, 30, function () {
                return DB::table($this->tableSetupMst . ' as a')
                    ->selectRaw('picking_qty,r.material_no,count(picking_qty) as jml_pick')
                    ->leftJoin('master_wire_register as r', 'a.material_no', '=', 'r.id')
                    ->where('pallet_no', $this->paletBarcode)
                    ->groupBy('picking_qty', 'r.material_no')->get();
            });

            $key = '0getallcnc_' . $this->paletBarcode . '_' . $this->userId . '_' . md5($getScannedString);
            $getall = Cache::remember($key, 30, function () use ($getScanned) {
                return DB::table($this->tableSetupMst . ' as  a')
                    ->selectRaw('pallet_no, r.material_no,count(a.material_no) as pax, sum(a.picking_qty) as picking_qty, min(a.serial_no) as serial_no,line_c,serial_no')
                    ->leftJoin('material_mst as b', 'a.serial_no', '=', 'b.matl_no')
                    ->leftJoin('master_wire_register as r', 'a.material_no', '=', 'r.id')
                    ->where('a.pallet_no', $this->paletBarcode)
                    ->whereNotIn('a.serial_no', $getScanned)
                    ->groupBy('a.pallet_no', 'r.material_no', 'line_c', 'r.material_no', 'serial_no')
                    ->orderByDesc('pax')
                    ->orderByDesc('r.material_no')->get();
            });
        } else {
            // tambah cache
            $key = '1picking_' . $this->paletBarcode . '_' . $this->userId;
            $collection = Cache::remember($key, 30, function () {
                return DB::table($this->tableSetupMst . ' as a')
                    ->selectRaw('picking_qty,material_no,count(picking_qty) as jml_pick')
                    ->where('pallet_no', $this->paletBarcode)
                    ->groupBy('picking_qty', 'material_no')->get();
            });

            $key = '1getallcnc_' . $this->paletBarcode . '_' . $this->userId . '_' . md5($getScannedString);
            $getall = Cache::remember($key, 30, function () use ($getScanned) {
                return DB::table($this->tableSetupMst . ' as  a')
                    ->selectRaw('pallet_no, a.material_no ,count(a.material_no) as pax, sum(a.picking_qty) as picking_qty, min(a.serial_no) as serial_no,line_c ')
                    ->leftJoin('material_mst as b', 'a.serial_no', '=', 'b.matl_no')
                    ->where('a.pallet_no', $this->paletBarcode)
                    ->whereNotIn('a.serial_no', $getScanned)
                    ->groupBy('a.pallet_no', 'a.material_no', 'line_c')
                    ->orderByDesc('pax')
                    ->orderByDesc('a.material_no')->get();
            });
        }

        $this->products = $getall;
        $materialNos = $getall->pluck('material_no')->all();

        $existingCounters = DB::table($this->tableTemp)
            ->where('palet', $this->paletBarcode)
            ->whereIn('material', $materialNos)
            ->pluck('material')
            ->all();

        // grouping and remove material no
        $group = $collection->groupBy(function ($item) {
            return trim($item->material_no);
        });

        $group->map(function ($item) {
            foreach ($item as $i) {
                unset($i->material_no);
            }
        });
        foreach ($getall as $value) {
            $counterExists = in_array($value->material_no, $existingCounters);
            if (!$counterExists) {
                try {
                    DB::beginTransaction();
                    $insert = [
                        'material' => $value->material_no,
                        'serial_no' => $value->serial_no,
                        'palet' => $this->paletBarcode,
                        'userID' => $this->userId,
                        'line_c' => $value->line_c,
                        'sisa' => $value->picking_qty,
                        'total' => $value->picking_qty,
                        'pax' => $value->pax,
                    ];

                    if (count($group[trim($value->material_no)]) > 1) {
                        $insert['scan_count'] = $value->pax;
                    }

                    tempCounterSiws::create($insert);
                    DB::commit();
                } catch (\Exception $th) {
                    DB::rollBack();
                    throw $th;
                }
            }
        }

        $this->refreshTemp();


        if ($getall->count() == 0 && count($getScanned) > 0) {
            $this->props = [1, 'Scan Confirmed'];
        } elseif ($this->paletBarcode != null) {
            $this->props = [1, 'No Data'];
        }
        $this->dispatch('paletFocus');

        $this->productsInPalet = $getall;
        // $this->scanned = $scannedCounter;

    }

    public function resetPage()
    {
        try {
            DB::enableQueryLog();
            DB::table($this->tableTemp)->where('userID', $this->userId)->delete();
        } catch (\Exception $th) {
            throw $th;
        }

        DB::table($this->tableSetupMst)
            ->where('serial_no', '00000')
            ->where('pallet_no', $this->paletBarcode)
            ->where('line_c', 'NewItem')->delete();

        $this->paletBarcode = null;
        $this->produkBarcode = null;
        $this->trucking_id = null;
        $this->paletInput = false;

        $this->refreshData();
        $this->dispatch('paletFocus');
    }

    public function resetItem($req)
    {
        // $qryUPdate = tempCounterSiws::where('palet', $req[1])->where('material', $req[0]);
        // ganti ke id soale nek dobel2 sulit
        $qryUPdate = tempCounterSiws::where('id', $req[2]);
        $data = $qryUPdate->first();

        $materialSetup = DB::table($this->tableSetupMst)
            ->where('pallet_no', $req[1])
            ->where('serial_no', '00000')
            ->where('material_no', $req[0]);


        if ($materialSetup->count() > 0) {
            $materialSetup->delete();
            $qryUPdate->delete();
        } else {
            $qryUPdate->update([
                'sisa' => $data->total,
                'counter' => 0,
                'qty_more' => 0,
                'prop_scan' => null,
            ]);
        }

        $this->refreshTemp();
        $this->dispatch('produkFocus');
    }


    public function editQty($qty, $material, $palet)
    {
        $qryUPdate = tempCounterSiws::where('palet', $palet)->where('material', $material);
        $data = $qryUPdate->first();

        $sisa = $data->total > $qty ? 0 : $data->total - $qty;
        $more = $sisa < 0 ? 1 : 0;
        $qryUPdate->update([
            'sisa' => $sisa,
            'counter' => $qty,
            'qty_more' => $more,
            'prop_scan' => json_encode([$qty]),
        ]);


        $this->refreshTemp();
        $this->dispatch('produkFocus');
    }

    public function confirm()
    {

        $fixProduct = DB::table($this->tableTemp . ' as temp_counters')
            ->leftJoin('delivery_mst as d', 'temp_counters.palet', '=', 'd.pallet_no')
            ->leftJoin('matloc_temp_CNCKIAS2 as m', 'temp_counters.material', '=', 'm.material_no')
            ->select('temp_counters.*', 'd.trucking_id', 'm.location_cd')
            ->where('userID', $this->userId)
            ->where('palet', $this->paletBarcode);

        $dataMaterial = $fixProduct->get();
        foreach ($dataMaterial as $data) {
            $pax = $data->pax;
            $qty = $data->total / $pax;
            $kelebihan = $data->qty_more;
            if ($data->prop_scan != null) {

                $prop_scan = json_decode($data->prop_scan, true);
                $totalScan = count($prop_scan);
                $totalScanMasuk = $totalScan - $data->qty_more;

                $scanke = 0;
                $totalQtyScanned = 0;
                foreach ($prop_scan as $value) {
                    $scanke++;

                    $totalQtyScanned += $value;
                    // jika qty more lebih dari 0
                    if ($data->qty_more > 0) {
                        if ($scanke > $totalScanMasuk && $data->sisa < 0) {
                            abnormalMaterial::create([
                                'pallet_no' => $this->paletBarcode,
                                'material_no' => $data->material,
                                'picking_qty' => $value,
                                'locate' => $data->location_cd,
                                'trucking_id' => $data->trucking_id,
                                'user_id' => $this->userId,
                                'line_c' => $data->line_c,
                                'status' => 1
                            ]);
                            // dump('lebih 1 => '.$data->material);
                        } else {
                            itemIn::create([
                                'pallet_no' => $this->paletBarcode,
                                'material_no' => $data->material,
                                'picking_qty' => $value,
                                'locate' => $data->location_cd,
                                'trucking_id' => $data->trucking_id,
                                'line_c' => $data->line_c,
                                'user_id' => $this->userId
                            ]);
                            // dump('in ada scan lebih=> '.$data->material);
                        }

                        // kondisi jika qty more 0 dan scan kurang dari/sama dengan pax
                    } else if ($scanke <= $data->pax && $data->qty_more == 0 && $data->sisa == 0) {
                        itemIn::create([
                            'pallet_no' => $this->paletBarcode,
                            'material_no' => $data->material,
                            'picking_qty' => $value,
                            'locate' => $data->location_cd,
                            'trucking_id' => $data->trucking_id,
                            'line_c' => $data->line_c,
                            'user_id' => $this->userId
                        ]);
                        // dump('in => '.$data->material);

                        // dan jika qty more 0 tapi sisa nya minus(material lebih )
                    } else {
                        // jika ini sekali scan sudah lebih
                        if ($value > $data->total) {
                            // yang masuk ke instock
                            itemIn::create([
                                'pallet_no' => $this->paletBarcode,
                                'material_no' => $data->material,
                                'picking_qty' => $data->total,
                                'locate' => $data->location_cd,
                                'trucking_id' => $data->trucking_id,
                                'line_c' => $data->line_c,
                                'user_id' => $this->userId
                            ]);

                            // yang masuk ke abnormal
                            $qtyLebih = $value - $data->total;

                            abnormalMaterial::create([
                                'pallet_no' => $this->paletBarcode,
                                'material_no' => $data->material,
                                'picking_qty' => $qtyLebih,
                                'locate' => $data->location_cd,
                                'trucking_id' => $data->trucking_id,
                                'user_id' => $this->userId,
                                'line_c' => $data->line_c,
                                'status' => 1
                            ]);
                        } else {
                            if ($scanke == $totalScan) {
                                $qtyLebih = $data->counter - $data->total;
                                // jika ini scan pertama kurang tapi kedua malah lebih
                                // contoh qty 300, scan 1 = 200, scan 2 = 200 (sisa -100)
                                if ($qtyLebih > 0 && $data->sisa < 0) {
                                    abnormalMaterial::create([
                                        'pallet_no' => $this->paletBarcode,
                                        'material_no' => $data->material,
                                        'picking_qty' => $qtyLebih,
                                        'locate' => $data->location_cd,
                                        'trucking_id' => $data->trucking_id,
                                        'user_id' => $this->userId,
                                        'line_c' => $data->line_c,
                                        'status' => 1
                                    ]);
                                    $qtyInstok = $value - $qtyLebih;
                                    itemIn::create([
                                        'pallet_no' => $this->paletBarcode,
                                        'material_no' => $data->material,
                                        'picking_qty' => $qtyInstok,
                                        'locate' => $data->location_cd,
                                        'trucking_id' => $data->trucking_id,
                                        'line_c' => $data->line_c,
                                        'user_id' => $this->userId
                                    ]);
                                }
                                // contoh qty 150, scan 1 = 100 (sisa 50)
                                else {
                                    itemIn::create([
                                        'pallet_no' => $this->paletBarcode,
                                        'material_no' => $data->material,
                                        'picking_qty' => $value,
                                        'locate' => $data->location_cd,
                                        'trucking_id' => $data->trucking_id,
                                        'line_c' => $data->line_c,
                                        'user_id' => $this->userId
                                    ]);
                                    abnormalMaterial::create([
                                        'pallet_no' => $this->paletBarcode,
                                        'material_no' => $data->material,
                                        'picking_qty' => $data->sisa,
                                        'locate' => $data->location_cd,
                                        'trucking_id' => $data->trucking_id,
                                        'user_id' => $this->userId,
                                        'line_c' => $data->line_c,
                                        'status' => 0
                                    ]);
                                }
                            } else {

                                itemIn::create([
                                    'pallet_no' => $this->paletBarcode,
                                    'material_no' => $data->material,
                                    'picking_qty' => $value,
                                    'locate' => $data->location_cd,
                                    'trucking_id' => $data->trucking_id,
                                    'line_c' => $data->line_c,
                                    'user_id' => $this->userId
                                ]);
                            }
                        }
                    }
                }
                // kurang
                // if ($data->total > $data->counter) {
                //     // $count = $data->pax - $masuk;
                //     $kurangnya = $data->total - $data->counter;
                //     abnormalMaterial::create([
                //         'pallet_no' => $this->paletBarcode,
                //         'material_no' => $data->material,
                //         'picking_qty' => $kurangnya,
                //         'locate' => $data->location_cd,
                //         'trucking_id' => $data->trucking_id,
                //         'user_id' => $this->userId,
                //         'status' => 0
                //     ]);
                //     // dump('kurang1 => '.$data->material);
                // }
            } else {
                $belumIsiSamaSekali = $data->sisa / $data->pax;
                for ($i = 0; $i < $data->pax; $i++) {
                    # code...
                    // dump('kurang 2=> '.$data->material);
                    abnormalMaterial::create([
                        'pallet_no' => $this->paletBarcode,
                        'material_no' => $data->material,
                        'picking_qty' => $belumIsiSamaSekali,
                        'locate' => $data->location_cd,
                        'trucking_id' => $data->trucking_id,
                        'user_id' => $this->userId,
                        'line_c' => $data->line_c,
                        'status' => 0
                    ]);
                }
            }
        }
        $this->paletInput = false;
        $paletBarcode = $this->paletBarcode;

        $this->resetPage();
        $this->refreshTemp();
        return Excel::download(new ScannedExport($dataMaterial), "Scanned Items_" . $paletBarcode . "_" . date('YmdHis') . ".pdf", \Maatwebsite\Excel\Excel::MPDF);
    }
    public function render()
    {
        return view('livewire.receiving-s-i-w-s');
    }
}
