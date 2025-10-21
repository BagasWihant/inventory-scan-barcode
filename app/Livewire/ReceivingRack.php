<?php

namespace App\Livewire;

use Illuminate\Support\Facades\DB;
use Livewire\Component;

class ReceivingRack extends Component
{
    public function searchPalet(string $s): object
    {
        $data = DB::table("material_in_stock")
            ->selectRaw("pallet_no, pallet_no as id")
            ->where("pallet_no", "like", "%$s%")
            ->groupBy("pallet_no")
            ->limit(10)
            ->get();
        return $data;
    }

    public function selectPalet(array $v)
    {
        $data = DB::table("material_in_stock as m")
            ->where("pallet_no", $v['pallet_no'])
            ->selectRaw("material_no,
                (
                    SELECT STRING_AGG(CAST(x.picking_qty AS varchar(10)), ', ')
                    FROM (
                        SELECT DISTINCT i.picking_qty
                        FROM material_in_stock i
                        WHERE i.material_no = m.material_no
                        AND i.pallet_no  = m.pallet_no
                    ) AS x
                ) AS picking_qty,
                pallet_no,
                count(*) as pack")
            ->groupByRaw("material_no,  pallet_no")
            ->get();
        $this->dispatch(
            'data-load',
            data: $data
        );
    }

    public function render()
    {
        return view('livewire.receiving-rack');
    }
}
