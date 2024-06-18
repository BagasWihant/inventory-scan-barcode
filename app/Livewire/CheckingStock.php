<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\Attributes\On;
use Illuminate\Support\Facades\DB;

class CheckingStock extends Component
{
    public $paletBarcode, $materialCode, $dateStart, $dateEnd;
    public $shipped = [], $inStock = [], $listMaterial = [];

    #[On('paletChange')]
    public function paletChange()
    {
        $this->paletBarcode = substr($this->paletBarcode, 0, 10);
        $distinc = DB::table('material_setup_mst_CNC_KIAS2')->select('material_no')->where('pallet_no', $this->paletBarcode)->distinct();
        $this->listMaterial = $distinc->pluck('material_no')->all();
    }

    public function materialChange()
    {
        $this->materialCode =  $this->materialCode == "" ? null : $this->materialCode;
    }

    public function searching()
    {

        // ITEM YANG DIKIRIM
        $queryShippedRaw = DB::table('material_setup_mst_CNC_KIAS2 as a')
            ->selectRaw("pallet_no, a.material_no,count(a.material_no) as pax, sum(picking_qty) as picking_qty, min(serial_no) as serial_no,location_cd, FORMAT(setup_date,'dd-MM-yyyy') as date")
            ->leftJoin('matloc_temp_CNCKIAS2 as b', 'a.material_no', '=', 'b.material_no');

        if ($this->paletBarcode) {
            $queryShippedRaw->where('pallet_no', $this->paletBarcode);
        }

        if ($this->materialCode) {
            $queryShippedRaw->where('a.material_no', $this->materialCode);
        } else {
            if ($this->listMaterial) {
                $queryShippedRaw->whereIn('a.material_no', $this->listMaterial);
            }
        }

        if ($this->dateStart && $this->dateEnd) {
            $queryShippedRaw->whereRaw('FORMAT(setup_date,"yyyy-MM-dd") >= ?', $this->dateStart);
            $queryShippedRaw->whereRaw('FORMAT(setup_date,"yyyy-MM-dd") <= ?', $this->dateEnd);
        }

        $productsQuery = $queryShippedRaw->groupBy('pallet_no', 'a.material_no', 'location_cd', 'setup_date')
            ->orderByDesc('pax')
            ->orderByDesc('a.material_no');
        $tempList = $productsQuery->pluck('material_no')->all();

        $this->shipped = $productsQuery->get();

        // ITEM YANG DITERIMa / STOCK
        $queryStockRaw = DB::table('material_in_stock as a')
            ->selectRaw('a.pallet_no, a.material_no,count(a.material_no) as pax, sum(a.picking_qty) as picking_qty,a.locate,b.status')
            ->leftJoin('abnormal_materials as b', function ($join) {
                $join->on('a.pallet_no', '=', 'b.pallet_no')
                    ->on('a.material_no', '=', 'b.material_no');
            });
        if ($this->materialCode) {
            $queryStockRaw->where('a.material_no', $this->materialCode);
        } else {
            $queryStockRaw->whereIn('a.material_no', $tempList);
        }
        if ($this->paletBarcode) {
            $queryShippedRaw->where('pallet_no', $this->paletBarcode);
        }


        $getScanned = $queryStockRaw
            ->groupBy('a.pallet_no', 'a.material_no', 'a.locate', 'b.status')->get();
        // dd($productsQuery->toRawSql(), $getScanned);
        $this->inStock = $getScanned;
    }


    public function render()
    {
        $distinc = DB::table('material_setup_mst_CNC_KIAS2')->select('pallet_no')->distinct();
        // $listMaterial = $distinc->pluck('material_no')->all();
        $listPalet = $distinc->pluck('pallet_no')->all();



        return view('livewire.checking-stock', [
            'listPalet' => $listPalet,
            // 'listMaterial' => $listMaterial
        ]);
    }
}
