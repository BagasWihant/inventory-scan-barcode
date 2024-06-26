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
                return;
            } else {

                $tempCount->update([
                    'counter' => $counter,
                    'sisa' => $sisa,
                    'prop_scan' => json_encode($new_prop_scan),
                ]);
            }
        }
        $this->produkBarcode = null;
    }

    public function paletBarcodeScan()
    {
        $this->paletBarcode = substr($this->paletBarcode, 0, 10);
        // dump($this->paletBarcode !== $this->previousPaletBarcode);
        if (strlen($this->paletBarcode) > 2) {
            DB::table('temp_counters')->where('userID', $this->userId)->delete();
            $truk = DB::table('delivery_mst')->where('pallet_no', $this->paletBarcode)->select('trucking_id')->first();
            if ($truk) {
                $this->dispatch('produkFocus');

                $this->trucking_id = $truk->trucking_id;
                $this->paletInput = true;
                $this->previousPaletBarcode = $this->paletBarcode;
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
            $supplierCode = DB::table('material_conversion_mst')->where('supplier_code', $this->produkBarcode)->select('sws_code')->first();
            if ($supplierCode) {
                $this->sws_code = $supplierCode->sws_code;

                $tempCount = DB::table('temp_counters')
                    ->where('material', $supplierCode->sws_code)
                    ->where('userID', $this->userId)
                    ->where('palet', $this->paletBarcode);
                $data = $tempCount->first();

                if ($tempCount->count() > 0) {

                    $mat_mst = DB::table('material_mst')
                        ->select('iss_min_lot')
                        ->where('matl_no', $this->sws_code)->first();

                    if ($mat_mst->iss_min_lot == 1) {
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

                            $this->produkBarcode = null;
                            $more = $data->qty_more + 1;
                            $tempCount->update(['counter' => $counter, 'sisa' => $sisa, 'qty_more' => $more, 'prop_scan' => json_encode($new_prop_scan)]);
                            return;
                        }

                        $tempCount->update(['counter' => $counter, 'sisa' => $sisa, 'prop_scan' => json_encode($new_prop_scan)]);
                    }
                } else {

                    $cek = DB::table('material_setup_mst_CNC_KIAS2')
                        ->selectRaw('COUNT(DISTINCT picking_qty) as jml, max(picking_qty) as qty')
                        ->where('material_no', $supplierCode->sws_code);
                    if ($cek->count() > 0) {
                        $data = $cek->first();
                        if ($data->jml > 1) {
                            $this->dispatch('newItem', ['qty' => 0, 'title' => 'Item Duplicate']);
                            return;
                        }
                        $this->dispatch('newItem', ['qty' => $data->qty, 'title' => 'New Item but in Database']);
                        return;
                    }
                    // insert barcode tidak terecord
                    $this->dispatch('newItem', ['title' => 'New Item']);
                }
            }
        }
        $this->produkBarcode = null;
    }

    public function render()
    {
        $getScanned = DB::table('material_in_stock')->select('material_no')
            ->where('pallet_no', $this->paletBarcode)
            ->union(DB::table('abnormal_materials')->select('material_no')->where('pallet_no', $this->paletBarcode))
            ->pluck('material_no')
            ->all();

        $productsQuery = DB::table('material_setup_mst_CNC_KIAS2 as a')
            ->selectRaw('pallet_no, a.material_no,count(a.material_no) as pax, sum(picking_qty) as picking_qty, min(serial_no) as serial_no,location_cd')
            ->leftJoin('matloc_temp_CNCKIAS2 as b', 'a.material_no', '=', 'b.material_no')
            ->where('pallet_no', $this->paletBarcode)
            ->whereNotIn('a.material_no', $getScanned)
            ->groupBy('pallet_no', 'a.material_no', 'location_cd')
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


        $scannedCounter = DB::table('temp_counters as a')
            ->leftJoin('matloc_temp_CNCKIAS2 as b', 'a.material', '=', 'b.material_no')
            ->where('palet', $this->paletBarcode)
            ->select('a.*', 'b.location_cd')
            ->where('userID', $this->userId)
            ->orderByDesc('pax')
            ->orderByDesc('material')
            ->get();

        $props = [0, 'No Data'];
        if ($getall->count() == 0 && count($getScanned) > 0) {
            $props = [1, 'Scan Confirmed'];
        }
        $this->dispatch('paletFocus');

        return view('livewire.list-product', [
            'productsInPalet' => $getall,
            'scanned' => $scannedCounter,
            'props' => $props
        ]);
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

        $this->dispatch('paletFocus');
    }

    public function resetItem($req){
        $qryUPdate = tempCounter::where('palet', $req[1])->where('material', $req[0]);
        $data = $qryUPdate->first();

        $qryUPdate->update([
            'sisa' => $data->total,
            'counter'=>0,
            'qty_more' =>0,
            'prop_scan' => null,
        ]);
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
                $masuk = 1;
                foreach ($prop_scan as $value) {
                    if ($masuk <= $data->pax || $data->total > $data->counter) {
                        itemIn::create([
                            'pallet_no' => $this->paletBarcode,
                            'material_no' => $data->material,
                            'picking_qty' => $value,
                            'locate' => $data->location_cd,
                            'trucking_id' => $data->trucking_id,
                            'user_id' => $this->userId
                        ]);
                    } else {
                        abnormalMaterial::create([
                            'pallet_no' => $this->paletBarcode,
                            'material_no' => $data->material,
                            'picking_qty' => $value,
                            'locate' => $data->location_cd,
                            'trucking_id' => $data->trucking_id,
                            'user_id' => $this->userId,
                            'status' => 1
                        ]);
                    }
                    $masuk++;
                }
                // kurang
                if ($data->total > $data->counter) {
                    $count = $data->pax - $masuk;
                    $kurangnya = $data->total - $data->counter;
                    abnormalMaterial::create([
                        'pallet_no' => $this->paletBarcode,
                        'material_no' => $data->material,
                        'picking_qty' => $kurangnya,
                        'locate' => $data->location_cd,
                        'trucking_id' => $data->trucking_id,
                        'user_id' => $this->userId,
                        'status' => 0
                    ]);
                }
            } else {
                $belumIsiSamaSekali = $data->sisa / $data->pax;
                for ($i = 0; $i < $data->pax; $i++) {
                    # code...
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

        return Excel::download(new ScannedExport($b), "Scanned Items_" . $paletBarcode . "_" . date('YmdHis') . ".pdf", \Maatwebsite\Excel\Excel::MPDF);
    }
}
