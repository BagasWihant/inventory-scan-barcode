<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\tempCounter;
use Illuminate\Support\Facades\DB;

class PurchaseOrderIn extends Component
{
    public $userId, $po, $listMaterial = [], $listMaterialScan, $listKitNo;
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
        $getTempCounterData = DB::table('temp_counters')->where('palet', $this->po)->where('material', $this->material_no);
        $dataTempCount = $getTempCounterData->first();
        if ($dataTempCount) {

            if ($dataTempCount->prop_ori === null) {
                $productsQuery = DB::table('material_setup_mst_supplier')->where('kit_no', $this->po)
                    ->where('material_no', $this->material_no)
                    ->selectRaw('material_no,picking_qty,kit_no');

                $mergedQty = $productsQuery->get()->pluck('picking_qty')->all();

                $getTempCounterData->update(['prop_ori' => json_encode($mergedQty)]);
            }

            $decodePropOri = json_decode($getTempCounterData->first()->prop_ori);
            $dataTempCounter = $getTempCounterData->first();

            $materialData = DB::table('material_setup_mst_supplier')
                ->where('kit_no', $this->po)->where('material_no', $this->material_no)
                ->selectRaw('material_no,sum(picking_qty) as picking_qty,kit_no,count(picking_qty) as pax')
                ->groupBy(['material_no', 'kit_no'])
                ->orderByDesc('pax')
                ->orderBy('material_no')->first();


            $newPropScan = isset($dataTempCounter->prop_scan) ? json_decode($dataTempCounter->prop_scan) : [];

            $counter = $dataTempCounter->counter + $decodePropOri[0];
            $sisa = $dataTempCounter->sisa - $decodePropOri[0];
            
            array_push($newPropScan, $decodePropOri[0]);
            array_splice($decodePropOri, 0, 1);

            $getTempCounterData->update([
                'prop_ori' => json_encode($decodePropOri),
                'prop_scan' => json_encode($newPropScan),
                'counter' => $counter, 'sisa' => $sisa
            ]);
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
