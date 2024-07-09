<?php

namespace App\Livewire;

use App\Models\itemIn;
use Livewire\Component;
use App\Models\tempCounter;
use Livewire\Attributes\On;
use App\Models\abnormalMaterial;
use Illuminate\Support\Facades\DB;

class PurchaseOrderIn extends Component
{
    public $userId, $po, $listMaterial = [], $listMaterialScan, $listKitNo, $sws_code, $statusLoading;
    public $paletCode, $palet, $noPalet;
    public $surat_jalan;
    public $material_no;
    public $suratJalanDisable = false, $paletDisable = false, $poDisable = false;

    public function mount()
    {
        $this->userId = auth()->user()->id;
        $this->listKitNo = DB::table('material_setup_mst_supplier')->selectRaw('distinct(kit_no)')->get();
    }
    public function updated($n, $v)
    {
        if ($n === 'po') {
            $this->suratJalanDisable = true;
            $this->paletDisable = true;
            $this->poDisable = true;
        }
    }
    public function updating($prop, $v)
    {

        if ($prop === 'po') {
            DB::table('temp_counters')->where('userID', $this->userId)->where('flag', 1)->delete();
        }
    }


    public function materialNoScan()
    {
        $supplierCode = DB::table('material_conversion_mst')->where('supplier_code', $this->material_no)->select('sws_code')->first();

        if ($supplierCode) {
            $this->sws_code = $supplierCode->sws_code;
            $getTempCounterData = DB::table('temp_counters')->where('palet', $this->po)->where('material', $this->material_no);


            $mat_mst = DB::table('material_mst')
                ->select('iss_min_lot')
                ->where('matl_no', $this->sws_code)->first();

            if ($mat_mst->iss_min_lot == 1) {
                $this->dispatch('newItem', ['qty' => 0, 'title' => 'Material with manual Qty', 'update' => true]);
            }
            // $dataTempCount = $getTempCounterData->first();
            // if ($dataTempCount) {

            //     if ($dataTempCount->prop_ori === null) {
            //         $productsQuery = DB::table('material_setup_mst_supplier')->where('kit_no', $this->po)
            //             ->where('material_no', $this->material_no)
            //             ->selectRaw('material_no,picking_qty,kit_no');

            //         $mergedQty = $productsQuery->get()->pluck('picking_qty')->all();

            //         $getTempCounterData->update(['prop_ori' => json_encode($mergedQty)]);
            //     }

            //     $decodePropOri = json_decode($getTempCounterData->first()->prop_ori);
            //     $dataTempCounter = $getTempCounterData->first();

            //     $materialData = DB::table('material_setup_mst_supplier')
            //         ->where('kit_no', $this->po)->where('material_no', $this->material_no)
            //         ->selectRaw('material_no,sum(picking_qty) as picking_qty,kit_no,count(picking_qty) as pax')
            //         ->groupBy(['material_no', 'kit_no'])
            //         ->orderByDesc('pax')
            //         ->orderBy('material_no')->first();


            //     $newPropScan = isset($dataTempCounter->prop_scan) ? json_decode($dataTempCounter->prop_scan) : [];

            //     $counter = $dataTempCounter->counter + $decodePropOri[0];
            //     $sisa = $dataTempCounter->sisa - $decodePropOri[0];

            //     array_push($newPropScan, $decodePropOri[0]);
            //     array_splice($decodePropOri, 0, 1);

            //     $getTempCounterData->update([
            //         'prop_ori' => json_encode($decodePropOri),
            //         'prop_scan' => json_encode($newPropScan),
            //         'counter' => $counter, 'sisa' => $sisa
            //     ]);
            // }
        }
        $this->material_no = null;
    }

    #[On('insertNew')]
    public function insertNew(int $qty = 0, $save = true, $update = false)
    {
        if ($update) {
            $tempCount = DB::table('temp_counters')
                ->where('material', $this->sws_code)
                ->where('userID', $this->userId)
                ->where('palet', $this->po);
            $data = $tempCount->first();

            $counter = $data->counter + $qty;

            $new_prop_scan = isset($data->prop_scan) ? json_decode($data->prop_scan) : [];
            array_push($new_prop_scan, $qty);

            $sisa = $data->sisa - $qty;
            if ($data->total < $data->counter || $data->sisa <= 0) {

                $this->material_no = null;
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
        $this->material_no = null;
    }

    public function resetItem($data)
    {
        $qryUPdate = tempCounter::where('palet', $data[1])->where('material', $data[0]);
        $data = $qryUPdate->first();

        $qryUPdate->update([
            'sisa' => $data->total,
            'counter' => 0,
            'qty_more' => 0,
            'prop_scan' => null,
        ]);
        $this->dispatch('SJFocus');
    }
    public function confirm()
    {
        $paletCode = "$this->palet-$this->noPalet";
        $fixProduct = DB::table('temp_counters')
            ->leftJoin('delivery_mst as d', 'temp_counters.palet', '=', 'd.pallet_no')
            ->leftJoin('matloc_temp_CNCKIAS2 as m', 'temp_counters.material', '=', 'm.material_no')
            ->select('temp_counters.*', 'd.trucking_id', 'm.location_cd')
            ->where('userID', $this->userId)
            ->where('flag', 1)
            ->where('palet', $this->po);

        $loopData = $fixProduct->get();
        foreach ($loopData as $data) {
            $pax = $data->pax;
            $qty = $data->total / $pax;
            $kelebihan = $data->qty_more;
            if ($data->prop_scan != null) {

                $prop_scan = json_decode($data->prop_scan, true);
                $masuk = 1;
                foreach ($prop_scan as $value) {
                    if ($masuk <= $data->pax || $data->total > $data->counter) {
                        itemIn::create([
                            'pallet_no' => $paletCode,
                            'material_no' => $data->material,
                            'picking_qty' => $value,
                            'locate' => $data->location_cd,
                            'trucking_id' => $data->trucking_id,
                            'kit_no' => $this->po,
                            'user_id' => $this->userId
                        ]);
                    } else {
                        abnormalMaterial::create([
                            'kit_no' => $this->po,
                            'pallet_no' => $paletCode,
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
                        'pallet_no' => $paletCode,
                        'kit_no' => $this->po,
                        'material_no' => $data->material,
                        'picking_qty' => $kurangnya,
                        'locate' => $data->location_cd,
                        'trucking_id' => $data->trucking_id,
                        'user_id' => $this->userId,
                        'status' => 0
                    ]);
                }
            } else {
                $sisa = $data->sisa;
                for ($i = 1; $i <= $data->pax; $i++) {
                    $qty = floor($data->sisa / $data->pax);
                    $sisa = $sisa - $qty;
                    if($i > $data->pax) $qty = $sisa;
                    # code...
                    abnormalMaterial::create([
                        'kit_no' => $this->po,
                        'pallet_no' => $paletCode,
                        'material_no' => $data->material,
                        'picking_qty' => $qty,
                        'locate' => $data->location_cd,
                        'trucking_id' => $data->trucking_id,
                        'user_id' => $this->userId,
                        'status' => 0
                    ]);
                }
            }
        }
        $this->resetPage();
    }

    public function resetPage()
    {
        $this->suratJalanDisable = false;
        $this->paletDisable = false;
        $this->poDisable = false;
        $this->po = null;
        $this->surat_jalan = null;
        $this->palet = null;
        $this->noPalet = null;
        DB::table('temp_counters')->where('userID', $this->userId)->where('flag', 1)->delete();
        $this->dispatch('SJFocus');
    }

    public function render()
    {
        $getScanned = DB::table('material_in_stock')->select('material_no')
            ->where('pallet_no', $this->po)
            ->union(DB::table('abnormal_materials')->select('material_no')->where('pallet_no', $this->po))
            ->pluck('material_no')
            ->all();

        $productsQuery = DB::table('material_setup_mst_supplier')->where('kit_no', $this->po)
            ->selectRaw('material_no,sum(picking_qty) as picking_qty,kit_no,count(picking_qty) as pax')
            ->groupBy(['material_no', 'kit_no'])
            ->orderByDesc('pax')
            ->orderBy('material_no');

        $getall = $productsQuery->get();
        $materialNos = $getall->pluck('material_no')->all();

        $existingCounters = DB::table('temp_counters')
            ->where('palet', $this->po)
            ->whereIn('material', $materialNos)
            ->pluck('material')
            ->all();

        foreach ($getall as $value) {
            $counterExists = in_array($value->material_no, $existingCounters);
            if (!$counterExists) {
                try {
                    DB::beginTransaction();
                    $insert = [
                        'material' => $value->material_no,
                        'palet' => $this->po,
                        'userID' => $this->userId,
                        'sisa' => $value->picking_qty,
                        'total' => $value->picking_qty,
                        'pax' => $value->pax,
                        'flag' => 1
                    ];


                    tempCounter::create($insert);
                    DB::commit();
                } catch (\Throwable $th) {
                    DB::rollBack();
                }
            }
        }


        $scannedCounter = DB::table('temp_counters as a')
            ->leftJoin('matloc_temp_CNCKIAS2 as b', 'a.material', '=', 'b.material_no')
            ->where('palet', $this->po)
            ->select('a.*', 'b.location_cd')
            ->where('userID', $this->userId)
            ->orderByDesc('pax')
            ->orderBy('material')
            ->get();


        $props = [0, 'No Data'];
        if ($getall->count() == 0 && count($getScanned) > 0) {
            $props = [1, 'Scan Confirmed'];
        }
        $this->dispatch('paletFocus');

        $this->listMaterial = $getall;
        $this->listMaterialScan = $scannedCounter;

        return view('livewire.purchase-order-in');
    }
}
