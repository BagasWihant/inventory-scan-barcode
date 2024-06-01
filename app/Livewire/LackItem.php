<?php

namespace App\Livewire;

use Livewire\Component;
use App\Exports\InStockExport;
use App\Exports\InStockExportExcel;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class LackItem extends Component
{
    public $dataCetak,$searchKey;
    public function render()
    {
        $query = DB::table('material_kurang')
        ->selectRaw('pallet_no,material_no,sum(picking_qty) as qty, count(pallet_no) as pax')
        ->groupBy(['material_no','pallet_no']);

        $query->where('pallet_no','like',"%$this->searchKey%");
        $data= $query->get();
        
        if($this->searchKey) $this->dispatch('searchFocus');
        
        $this->dataCetak = $data;
        return view('livewire.lack-item',compact('data'));
    }

    public function exportPdf()  {
        if($this->searchKey) $name = "Kurang_".$this->searchKey."-".date('Ymd').".pdf";
        else $name = "InStock-".date('Ymd').".pdf";

        return Excel::download(new InStockExport($this->dataCetak), $name, \Maatwebsite\Excel\Excel::MPDF);
        
    }
    
    public function exportExcel()  {
        if($this->searchKey) $name = "Kurang_".$this->searchKey."-".date('Ymd').".xlsx";
        else $name = "InStock-".date('Ymd').".xlsx";

        return Excel::download(new InStockExportExcel($this->dataCetak), $name, \Maatwebsite\Excel\Excel::XLSX);
        
    }
}
