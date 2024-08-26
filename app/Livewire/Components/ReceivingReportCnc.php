<?php

namespace App\Livewire\Components;

use Illuminate\Support\Facades\DB;
use Livewire\Component;

class ReceivingReportCnc extends Component
{
    public $searchPalet, $listPalet = [], $listMaterial = [], $receivingData = [], $materialCode, $dateEnd, $dateStart,$inputDisable = false;

    public function paletChange()
    {
        $this->listPalet =  DB::table('material_setup_mst_CNC_KIAS2')->distinct()->select('pallet_no')->where('pallet_no', 'like', "%$this->searchPalet%")->limit(10)->get();
    }
    public function choosePalet($palet)
    {
        $this->inputDisable = true;
        $this->searchPalet = $palet;
        $this->listPalet = 'kosong';
        $this->listMaterial = DB::table('material_setup_mst_CNC_KIAS2')->select('material_no')->where('pallet_no', $this->searchPalet)->distinct()->get();
        //  = $distinc->pluck('material_no')->all();
    }
    public function resetData()
    {
        $this->inputDisable = false;
        $this->receivingData = [];
        $this->searchPalet = "";
        $this->materialCode = "";
        $this->dateStart = "";
        $this->dateEnd = "";
    }
    public function showData()
    {
        if (!$this->dateStart && !$this->dateEnd) {
            $this->dateStart = '2023-01-01';
            $this->dateEnd = date('Y-m-d');
        }

        $this->receivingData = DB::select('EXEC sp_Receiving_report ?,?,?,?,?,?,?', [
            'detail',
            $this->searchPalet ?? "",
            $this->dateStart ?? '',
            $this->dateEnd ?? "",
            '',
            $this->materialCode ?? "",
            '',
        ]);
    }
    public function render()
    {
        return view('livewire.components.receiving-report-cnc');
    }
}
