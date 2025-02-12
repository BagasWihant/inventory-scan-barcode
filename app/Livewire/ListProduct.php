<?php

namespace App\Livewire;

use Carbon\Carbon;
use App\Models\itemIn;
use Livewire\Component;
use App\Models\itemKurang;
use App\Models\tempCounter;
use Livewire\Attributes\On;
use Livewire\WithPagination;
use App\Exports\InStockExport;
use App\Exports\ScannedExport;
use App\Models\abnormalMaterial;
use App\Models\MaterialKelebihan;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class ListProduct extends Component
{
    use WithPagination;
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
            tempCounter::create([
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

            DB::table('material_setup_mst_cnc_kias2')->insert([
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
            $tempCount = DB::table('temp_counters')
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
            DB::table('temp_counters')->where('userID', $this->userId)->where('flag', 0)->delete();
            $truk = DB::table('delivery_mst')->where('pallet_no', $this->paletBarcode)->select('trucking_id')->first();
            if ($truk) {
                $this->dispatch('produkFocus');

                $this->trucking_id = $truk->trucking_id;
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

            if (strtolower(substr($this->paletBarcode, 0, 1)) == "c") {
                $tempSplit = explode(' ', $this->produkBarcode);

                if(strtolower(substr($this->produkBarcode, 0, 1)) == "p") {
                    $this->produkBarcode = substr($this->produkBarcode, 1, 15);
                }else{
                    $this->produkBarcode = substr($tempSplit[0], 23, 15);
                }
                // dd($this->produkBarcode);
            }

            $supplierCode = DB::table('material_conversion_mst')->where('supplier_code', $this->produkBarcode)->select('sws_code')->first();
            if ($supplierCode) {
                $this->sws_code = $supplierCode->sws_code;

                $tempCount = DB::table('temp_counters')
                    ->where('material', $supplierCode->sws_code)
                    ->where('userID', $this->userId)
                    ->where('palet', $this->paletBarcode);
                $data = $tempCount->first();

                $mat_regis = DB::table('material_registrasis')
                    ->select('material_no')
                    ->where('material_no', $this->sws_code)->exists();

                if ($tempCount->count() > 0) {
                    if ($mat_regis) {
                        $this->dispatch('newItem', ['qty' => 0, 'title' => 'Material with manual Qty', 'update' => true]);
                    } else {
                        $qry = DB::table('material_setup_mst_CNC_KIAS2')
                            ->selectRaw('picking_qty')
                            ->where('material_no', $supplierCode->sws_code)
                            ->where('pallet_no', $this->paletBarcode)
                            ->groupBy('picking_qty');
                        $productDetail = $qry->first();

                        $counter = $data->counter + $productDetail->picking_qty;
                        $sisa = $data->sisa - $productDetail->picking_qty;

                        $new_prop_scan = isset($data->prop_scan) ? json_decode($data->prop_scan) : [];
                        array_push($new_prop_scan, $productDetail->picking_qty);


                        if ($data->total < $data->counter || $data->sisa <= 0) {
                            dump($data->total, $data->counter, $data->sisa);

                            $this->produkBarcode = null;
                            $more = $data->qty_more + 1;
                            $tempCount->update(['counter' => $counter, 'sisa' => $sisa, 'qty_more' => $more, 'prop_scan' => json_encode($new_prop_scan)]);
                            $this->refreshTemp();
                            return;
                        }

                        $tempCount->update(['counter' => $counter, 'sisa' => $sisa, 'prop_scan' => json_encode($new_prop_scan)]);
                        $this->refreshTemp();
                    }
                } else {

                    if ($mat_regis) {
                        $this->dispatch('newItem', ['qty' => 0, 'title' => 'Item with Duplicate Qty']);
                        return;
                    } else {
                        $cek = DB::table('material_setup_mst_CNC_KIAS2')
                            ->selectRaw('max(picking_qty) as qty')
                            ->where('material_no', $supplierCode->sws_code)->first();

                        $this->dispatch('newItem2', ['qty' => $cek->qty, 'title' => 'New Item Detected']);
                        return;
                    }
                    // insert barcode tidak terecord
                    $this->dispatch('newItem', ['title' => 'New Item not in Database']);
                }
            }
        }
        $this->produkBarcode = null;
    }

    public function render()
    {
        return view('livewire.list-product');
    }

    private function refreshTemp()
    {
        $this->scanned = DB::table('temp_counters as a')
            ->leftJoin('matloc_temp_CNCKIAS2 as b', 'a.material', '=', 'b.material_no')
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

        $productsQuery = DB::table('material_setup_mst_CNC_KIAS2 as a')
            ->selectRaw('pallet_no, a.material_no,count(a.material_no) as pax, sum(a.picking_qty) as picking_qty, min(a.serial_no) as serial_no,loc_cd as location_cd')
            ->leftJoin('material_mst as b', 'a.material_no', '=', 'b.matl_no')
            ->where('a.pallet_no', $this->paletBarcode)
            ->whereNotIn('a.material_no', $getScanned)
            ->groupBy('a.pallet_no', 'a.material_no', 'b.loc_cd')
            ->orderByDesc('pax')
            ->orderByDesc('a.material_no');


        $picking = DB::table('material_setup_mst_CNC_KIAS2')
            ->selectRaw('picking_qty,material_no,count(picking_qty) as jml_pick')
            ->where('pallet_no', $this->paletBarcode)
            ->groupBy('picking_qty', 'material_no');
        $collection = $picking->get();

        $getall = $productsQuery->get();
        $this->products = $getall;
        $materialNos = $getall->pluck('material_no')->all();

        $existingCounters = DB::table('temp_counters')
            ->where('palet', $this->paletBarcode)
            ->whereIn('material', $materialNos)
            ->pluck('material')
            ->all();

        // grouping and remove material no
        $group = $collection->groupBy('material_no');
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
                        'palet' => $this->paletBarcode,
                        'userID' => $this->userId,
                        'sisa' => $value->picking_qty,
                        'total' => $value->picking_qty,
                        'pax' => $value->pax,
                    ];

                    if (count($group[$value->material_no]) > 1) {
                        $insert['scan_count'] = $value->pax;
                    }

                    tempCounter::create($insert);
                    DB::commit();
                } catch (\Throwable $th) {
                    DB::rollBack();
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
        DB::table('temp_counters')->where('userID', $this->userId)->delete();

        DB::table('material_setup_mst_cnc_kias2')
            ->where('serial_no', '00000')
            ->where('pallet_no', $this->paletBarcode)
            ->where('line_c', 'NewItem')->delete();

        $this->paletBarcode = null;
        $this->produkBarcode = null;
        $this->paletInput = false;

        $this->refreshData();
        $this->dispatch('paletFocus');
    }

    public function resetItem($req)
    {
        $qryUPdate = tempCounter::where('palet', $req[1])->where('material', $req[0]);
        $data = $qryUPdate->first();

        $materialSetup = DB::table('material_setup_mst_cnc_kias2')
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
        $this->dispatch('paletFocus');
    }

    public function confirm()
    {

        $fixProduct = DB::table('temp_counters')
            ->leftJoin('delivery_mst as d', 'temp_counters.palet', '=', 'd.pallet_no')
            ->leftJoin('matloc_temp_CNCKIAS2 as m', 'temp_counters.material', '=', 'm.material_no')
            ->select('temp_counters.*', 'd.trucking_id', 'm.location_cd')
            ->where('userID', $this->userId)
            ->where('palet', $this->paletBarcode);

        $b = $fixProduct->get();
        foreach ($b as $data) {
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
                                'status' => 1
                            ]);
                        }
                        else {
                            if ($scanke == $totalScan) {
                                $qtyLebih = $data->counter - $data->total;
                                // jika ini scan pertama kurang tapi kedua malah lebih
                                // contoh qty 300, scan 1 = 200, scan 2 = 200 (sisa -100)
                                if($qtyLebih > 0 && $data->sisa < 0){
                                    abnormalMaterial::create([
                                        'pallet_no' => $this->paletBarcode,
                                        'material_no' => $data->material,
                                        'picking_qty' => $qtyLebih,
                                        'locate' => $data->location_cd,
                                        'trucking_id' => $data->trucking_id,
                                        'user_id' => $this->userId,
                                        'status' => 1
                                    ]);
                                    $qtyInstok = $value - $qtyLebih;
                                    itemIn::create([
                                        'pallet_no' => $this->paletBarcode,
                                        'material_no' => $data->material,
                                        'picking_qty' => $qtyInstok,
                                        'locate' => $data->location_cd,
                                        'trucking_id' => $data->trucking_id,
                                        'user_id' => $this->userId
                                    ]);
                                }
                                // contoh qty 150, scan 1 = 100 (sisa 50)
                                else{
                                    itemIn::create([
                                        'pallet_no' => $this->paletBarcode,
                                        'material_no' => $data->material,
                                        'picking_qty' => $value,
                                        'locate' => $data->location_cd,
                                        'trucking_id' => $data->trucking_id,
                                        'user_id' => $this->userId
                                    ]);
                                    abnormalMaterial::create([
                                        'pallet_no' => $this->paletBarcode,
                                        'material_no' => $data->material,
                                        'picking_qty' => $data->sisa,
                                        'locate' => $data->location_cd,
                                        'trucking_id' => $data->trucking_id,
                                        'user_id' => $this->userId,
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
                        'status' => 0
                    ]);
                }
            }
        }
        $this->paletInput = false;
        $paletBarcode = $this->paletBarcode;

        $this->resetPage();
        $this->refreshTemp();
        return Excel::download(new ScannedExport($b), "Scanned Items_" . $paletBarcode . "_" . date('YmdHis') . ".pdf", \Maatwebsite\Excel\Excel::MPDF);
    }
}
