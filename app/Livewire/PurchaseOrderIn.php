<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\tempCounter;
use Livewire\Attributes\On;
use Illuminate\Support\Facades\DB;

class PurchaseOrderIn extends Component
{
    public $userId, $po, $listMaterial = [], $listMaterialScan, $listKitNo,$sws_code;
    public $surat_jalan;
    public $palet;
    public $material_no;

    public function mount()
    {
        $this->userId = auth()->user()->id;
        $this->listKitNo = DB::table('material_setup_mst_supplier')->selectRaw('distinct(kit_no)')->get();
    }

    public function updated($n, $v)
    {
        if ($n === 'po') {
            DB::table('temp_counters')->where('userID', $this->userId)->where('flag', 1)->delete();
        }
    }
    public function updating($property, $value)
    {
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
        dump('aa');
        if ($update) {
            $tempCount = DB::table('temp_counters')
                ->where('material', $this->sws_code)
                ->where('userID', $this->userId)
                ->where('palet', $this->po);
            $data = $tempCount->first();
            dump($tempCount->toRawSql());
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
