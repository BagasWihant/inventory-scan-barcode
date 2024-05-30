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
        ->groupBy(['material_no','pallet_no']);
        if($this->searchKey) $query->where('pallet_no','like',"%$this->searchKey%");
        $data= $query->get();

        $this->dataCetak = $data;
        return view('livewire.material-stock',compact('data'));
    }

    public function exportPdf()  {
        sleep(30);
        return Excel::download(new InStockExport($this->dataCetak), 'invoices.pdf', \Maatwebsite\Excel\Excel::MPDF);
        
    }
    
    public function exportExcel()  {
        return Excel::download(new InStockExportExcel($this->dataCetak), 'invoices.xlsx', \Maatwebsite\Excel\Excel::XLSX);
        
    }
}
