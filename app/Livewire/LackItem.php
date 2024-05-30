<?php

namespace App\Livewire;

use Livewire\Component;
use App\Exports\InStockExport;
use App\Exports\InStockExportExcel;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class LackItem extends Component
{
    public $d;
    public function render()
    {
        $data = DB::table('material_kurang')
        ->selectRaw('pallet_no,material_no,sum(picking_qty) as qty, count(pallet_no) as pax')
        ->groupBy(['material_no','pallet_no'])
        ->get();
        $this->d = $data;
        return view('livewire.lack-item',compact('data'));
    }

    public function exportPdf()  {
        return Excel::download(new InStockExport($this->d), 'invoices.pdf', \Maatwebsite\Excel\Excel::MPDF);
        
    }
    
    public function exportExcel()  {
        return Excel::download(new InStockExportExcel($this->d), 'invoices.xlsx', \Maatwebsite\Excel\Excel::XLSX);
        
    }
}
