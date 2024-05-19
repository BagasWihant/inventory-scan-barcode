<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\DB;

class ListPalet extends Component
{
    public $search;
    public function render()
    {
        $palets = [];
        if($this->search){
            $palets= DB::table('pallets')->select(['pallet_name','pallet_barcode'])->where('pallet_barcode','=',$this->search)->get();
        } 
        return view('livewire.list-palet',compact('palets'));
    }

}
