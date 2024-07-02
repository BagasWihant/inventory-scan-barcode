<?php

namespace App\Livewire;

use App\Exports\ExportReportStockTaking;
use Livewire\Component;
use App\Models\MenuOptions;
use App\Models\RealStockTaking;
use Livewire\Attributes\On;
use Maatwebsite\Excel\Facades\Excel;

class ReportStockTaking extends Component
{
    public $listSto,$data=[],$stoId;
    public function mount()
    {
        $this->listSto = MenuOptions::where('status', '0')->get();
    }

    public function showData() {
        if($this->stoId){
           return $this->data = RealStockTaking::where('sto_id', $this->stoId)->get();
        }
        $this->data = [];
    }
    
    function export($type) {
        if($type == 'pdf') return Excel::download(new ExportReportStockTaking($this->data), "Export Stock_".$this->stoId."_" . date('YmdHis') . ".pdf", \Maatwebsite\Excel\Excel::MPDF);

        return Excel::download(new ExportReportStockTaking($this->data), "Export Stock_".$this->stoId."_" . date('YmdHis') . ".xlsx", \Maatwebsite\Excel\Excel::XLSX);
    }
    public function render()
    {
        return view('livewire.report-stock-taking');
    }
}
