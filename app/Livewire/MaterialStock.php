<?php

namespace App\Livewire;

use Illuminate\Support\Facades\DB;
use Livewire\Component;

class MaterialStock extends Component
{
    public $searchKey;

    public function render()
    {
        $query = DB::table('material_in_stock')
        ->selectRaw('pallet_no,material_no,sum(picking_qty) as qty, count(pallet_no) as pax')
        ->groupBy(['material_no','pallet_no']);
        if($this->searchKey) $query->where('pallet_no','like',"%$this->searchKey%");
        $data= $query->get();

        return view('livewire.material-stock',compact('data'));
    }
}
