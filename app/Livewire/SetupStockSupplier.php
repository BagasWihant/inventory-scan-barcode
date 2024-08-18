<?php

namespace App\Livewire;

use App\Models\itemIn;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class SetupStockSupplier extends Component
{
    public $searchPalet,$listPallet,$input_setup_by;
    public $listMaterial=[];
    public $paletDisable = false;

    public function updated($r){
        // dump($r);
    }
    public function paletChange()
    {
        if (strlen($this->searchPalet) >= 2) {
            $this->listPallet = DB::table('material_in_stock')
                ->selectRaw('pallet_no')
                ->where('pallet_no', 'like', "%$this->searchPalet%")
                ->groupBy('pallet_no')
                ->limit(10)
                ->get();
            
        }
    }
    public function choosePalet($pallet = null)
    {
            $this->input_setup_by = itemIn::where('pallet_no',$pallet)->select('setup_by')->first()->setup_by;
            
            $this->searchPalet = $pallet;
            $this->listPallet = [];
        $this->paletDisable = true;
            $this->listMaterial = itemIn::where('pallet_no',$this->searchPalet)->get();
            // dd($this->listMaterial);


            return $this->dispatch('materialFocus');
    }

    public function resetPage() {
        $this->paletDisable = false;
        $this->searchPalet = null;
        $this->listPallet = [];
        $this->listMaterial = [];

    }
    public function render()
    {
        return view('livewire.setup-stock-supplier');
    }
}
