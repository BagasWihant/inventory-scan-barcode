<?php

namespace App\Livewire;

use Illuminate\Support\Facades\DB;
use Livewire\Component;

class MaterialStock extends Component
{
    public function render()
    {
        $data = DB::table('material_in_stock')
        ->selectRaw('pallet_no,material_no,sum(picking_qty) as qty, count(pallet_no) as pax')
        ->groupBy(['material_no','pallet_no'])
        ->get();
        return view('livewire.material-stock',compact('data'));
    }
}
