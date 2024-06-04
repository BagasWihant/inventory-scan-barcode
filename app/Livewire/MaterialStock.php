<?php

namespace App\Livewire;

use Livewire\Component;
use App\Exports\InStockExport;
use Illuminate\Support\Facades\DB;
use App\Exports\InStockExportExcel;
use Maatwebsite\Excel\Facades\Excel;

class MaterialStock extends Component
{
    public $searchKey,$dataCetak;

    public function render()
    {
        $query = DB::table('material_in_stock')
        ->selectRaw('pallet_no,material_no,sum(picking_qty) as qty, count(pallet_no) as pax')
        ->where('stat',0)
        ->groupBy(['material_no','pallet_no']);
        $query->where('pallet_no','like',"%$this->searchKey%");
        $data= $query->get();
        
        if($this->searchKey) $this->dispatch('searchFocus');

        $this->dataCetak = $data;
        return view('livewire.material-stock',compact('data'));
    }
    

    public function exportPdf()  {
        if($this->searchKey) $name = "InStock_".$this->searchKey."-".date('Ymd').".pdf";
        else $name = "InStock-".date('Ymd').".pdf";
        
        return Excel::download(new InStockExport($this->dataCetak), $name, \Maatwebsite\Excel\Excel::MPDF);
        
    }
    
    public function exportExcel()  {
        if($this->searchKey) $name = "InStock_".$this->searchKey."-".date('Ymd').".xlsx";
        else $name = "InStock-".date('Ymd').".xlsx";
        // dd($this->searchKey);

        return Excel::download(new InStockExport($this->dataCetak), $name, \Maatwebsite\Excel\Excel::XLSX);
        
    }
}
