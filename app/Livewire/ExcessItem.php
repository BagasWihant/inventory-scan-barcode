<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\DB;

class ExcessItem extends Component
{
    public function render()
    {
        $data = DB::table('item_sisas')
        ->selectRaw('pallet_no,material_no,sum(picking_qty) as qty, count(pallet_no) as pax')
        ->groupBy(['material_no','pallet_no'])
        ->get();
        return view('livewire.excess-item',compact('data'));
    }
}
