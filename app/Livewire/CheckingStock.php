<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\Attributes\On;
use Illuminate\Support\Facades\DB;

class CheckingStock extends Component
{
    public $paletBarcode = "", $materialCode = "", $dateStart = "", $dateEnd = "";
    public $receivingData = [], $inStock = [], $listMaterial = [];

    #[On('paletChange')]
    public function paletChange()
    {
        $this->paletBarcode = substr($this->paletBarcode, 0, 10);
        $distinc = DB::table('material_setup_mst_CNC_KIAS2')->select('material_no')->where('pallet_no', $this->paletBarcode)->distinct();
        $this->listMaterial = $distinc->pluck('material_no')->all();
    }

    public function materialChange()
    {
        $this->materialCode =  $this->materialCode == "" ? null : $this->materialCode;
    }

    public function searching()
    {
        if (!$this->dateStart && !$this->dateEnd) {
            $this->dateStart = '2023-01-01';
            $this->dateEnd = date('Y-m-d');
        }

        $this->receivingData = DB::select('EXEC sp_Receiving_report ?,?,?,?,?,?,?', [
            'detail',
            $this->paletBarcode ?? "",
            $this->dateStart ?? '',
            $this->dateEnd ?? "",
            '',
            $this->materialCode ?? "",
            '',
        ]);
        
    }


    public function render()
    {
        $distinc = DB::table('material_setup_mst_CNC_KIAS2')->select('pallet_no')->distinct();
        // $listMaterial = $distinc->pluck('material_no')->all();
        $listPalet = $distinc->pluck('pallet_no')->all();



        return view('livewire.checking-stock', [
            'listPalet' => $listPalet,
            // 'listMaterial' => $listMaterial
        ]);
    }
}
