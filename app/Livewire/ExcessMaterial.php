<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\Attributes\On;
use Livewire\WithFileUploads;
use App\Exports\InStockExport;
use App\Imports\KelebihanImport;
use Illuminate\Support\Facades\DB;
use App\Exports\InStockExportExcel;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Storage;

class ExcessMaterial extends Component
{
    use WithFileUploads;
    public $fileExcel,$searchKey,$dataCetak;
    public function render()
    {
        $query = DB::table('abnormal_materials')
        ->selectRaw('pallet_no,material_no,sum(picking_qty) as qty, count(pallet_no) as pax')
        ->where('status',1)
        ->groupBy(['material_no','pallet_no']);
       
        $query->where('pallet_no','like',"%$this->searchKey%");

        if($this->searchKey) $this->dispatch('searchFocus');
        $data= $query->get();
        
        
        $this->dataCetak = $data;
        return view('livewire.excess-material',compact('data'));
    }
    
    
    public function exportPdf()  {
        if($this->searchKey) $name = "Kelebihan_".$this->searchKey."-".date('Ymd').".pdf";
        else $name = "Kelebihan-".date('Ymd').".pdf";

        return Excel::download(new InStockExport($this->dataCetak), $name, \Maatwebsite\Excel\Excel::MPDF);
        
    }
    
    public function exportExcel()  {
        if($this->searchKey) $name = "Kelebihan_".$this->searchKey."-".date('Ymd').".xlsx";
        else $name = "Kelebihan-".date('Ymd').".xlsx";

        return Excel::download(new InStockExportExcel($this->dataCetak), $name, \Maatwebsite\Excel\Excel::XLSX);
        
    }
}
