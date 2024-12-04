<?php

namespace App\Livewire;

use Livewire\Component;

class MaterialRequest extends Component
{
    public $type;
    public $requestQty;
    public $searchMaterialNo;
    public $totalQtyRequest=0;

    public function saveRequest(){

    }

    public function render()
    {
        return view('livewire.material-request');
    }
}
