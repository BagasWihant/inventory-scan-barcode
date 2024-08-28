<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\Attributes\On;
use Illuminate\Support\Facades\DB;

class CheckingStock extends Component
{
    public $paletBarcode = "", $materialCode = "", $dateStart = "", $dateEnd = "", $kitNo = "", $paletNo="",$materialCodeSupp="";
    public $receivingData = [], $inStock = [], $listMaterial = [], $listPalet = [], $listPaletNoSup = [];
    public $mode = null;


    public function changeReceivingMode($mode)
    {
        $this->mode = $mode;
    }

   


    public function render()
    {

        return view('livewire.checking-stock', [
            // 'listPalet' => $listPalet,
            // 'listMaterial' => $listMaterial
        ]);
    }
}
