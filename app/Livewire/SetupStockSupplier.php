<?php

namespace App\Livewire;

use App\Models\itemIn;
use App\Models\PaletRegister;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class SetupStockSupplier extends Component
{
    public $searchPalet, $listPallet, $input_setup_by;
    public $listMaterial = [];
    public $paletDisable = false;
    
    public function render()
    {
        $this->listMaterial = PaletRegister::where('is_done',1)->get();

        return view('livewire.setup-stock-supplier');
    }
}
