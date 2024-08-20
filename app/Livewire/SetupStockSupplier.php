<?php

namespace App\Livewire;

use App\Models\itemIn;
use App\Models\PaletRegister;
use App\Models\PaletRegisterDetail;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class SetupStockSupplier extends Component
{
    public $searchPalet, $listPallet, $input_setup_by,$scan_date_modal,$no_palet_modal;
    public $listMaterial = [],$listDetail=[];
    public $paletDisable = false;
    

    public function detail($palet) {
        $this->listDetail = PaletRegisterDetail::where('palet_no',$palet)->selectRaw('material_no,sum(qty) as total_qty,count(*) as pack')->groupBy(['material_no','material_name'])->get(); 
        $this->no_palet_modal = $palet;
        $palet = PaletRegister::where('palet_no',$palet)->first();
        $this->scan_date_modal = date('d-m-Y H:i:s',strtotime($palet->created_at));
    }
    public function render()
    {
        $this->listMaterial = PaletRegister::where('is_done',1)->get();

        return view('livewire.setup-stock-supplier');
    }
}
