<?php

namespace App\Livewire;

use Illuminate\Support\Facades\DB;
use Livewire\Component;

class LogHistoryStock extends Component
{
    public function render()
    {
        return view('livewire.log-history-stock');
    }

    public function getData(
        $materialNo,
        $lineCode,
        $date
    ) {
        $data = DB::table('material_in_stock')
            ->selectRaw("material_no, picking_qty as qty, created_at, 'in' as status")
            ->where('material_no', $materialNo)
            ->when($lineCode, function ($sub) use ($lineCode) {
                return $sub->where('line_c', $lineCode);
            })
            ->when($date, function ($sub) use ($date) {
                return $sub->whereDate('created_at', $date);
            })
            ->unionAll(
                DB::table('Setup_dtl')
                    ->selectRaw("material_no, qty, setup_mst.created_at, 'out' as status")
                    ->leftJoin('setup_mst', 'setup_dtl.setup_id', '=', 'setup_mst.id')
                    ->where('material_no', $materialNo)
                    ->when($lineCode, function ($sub) use ($lineCode) {
                        return $sub->where('line_cd', $lineCode);
                    })
                    ->when($date, function ($sub) use ($date) {
                        return $sub->whereDate('setup_mst.created_at', $date);
                    })
            )
            ->get();
            
        return $data;
    }
}
