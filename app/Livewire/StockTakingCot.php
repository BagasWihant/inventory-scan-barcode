<?php

namespace App\Livewire;

use Illuminate\Support\Facades\DB;
use Livewire\Component;

class StockTakingCot extends Component
{
    public $tglSto;
    public $noSto;
    public $btnSetupDone;
    public $partial;
    public $dataTable =[];

    public function mount(){
        $qryNoSto =DB::table('WH_config')->where('config','noStockCot')->select('config','value')->get()->keyBy('config');
        $today = date('dmy');
        $noSto = substr((int)$qryNoSto['noStockCot']->value,0,6);
        if($noSto == $today){
            $urut = (int)substr($qryNoSto['noStockCot']->value,6,3);
            $this->noSto = 'STO-COT-'.$noSto . str_pad($urut+1, 3, '0', STR_PAD_LEFT);
        }else{
            $this->noSto = 'STO-COT-'.$today . '001';
        }
    }

    public function clearInput(){

    }

    public function saveSetup(){

    }
    
    public function render()
    {
        return view('livewire.stock-taking-cot');
    }
}
