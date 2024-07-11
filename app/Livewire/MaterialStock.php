<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Exports\InStockExport;
use Illuminate\Support\Facades\DB;
use Livewire\WithoutUrlPagination;
use Maatwebsite\Excel\Facades\Excel;

class MaterialStock extends Component
{
    use WithPagination,WithoutUrlPagination;
    public $searchKey,$perPage=10;

    public function setPerPage() {
        $this->resetPage();
    }

    public function render()
    {
        // $query = DB::table('material_in_stock')
        // ->selectRaw('material_no,sum(picking_qty) as qty, locate')
        // ->groupBy(['material_no','locate']);
        // $query->where('material_no','like',"%$this->searchKey%");
        // $data= $query->get();

        
        $query = DB::table('material_mst')
        ->selectRaw('matl_no as material_no,qty,loc_cd as locate')
        ->where('qty','>',0)
        ->groupBy(['matl_no','loc_cd','qty']);
        $query->where('matl_no','like',"%$this->searchKey%");
        $data= $query->paginate($this->perPage);
        
        if($this->searchKey) $this->dispatch('searchFocus');


        return view('livewire.material-stock',[
            'datas'=>$data,
        ]);
    }
    

    // public function exportPdf()  {
    //     $dataCetak = $this->getData();
    //     if($this->searchKey) $name = "InStock_".$this->searchKey."-".date('Ymd').".pdf";
    //     else $name = "InStock-".date('Ymd').".pdf";
        
    //     return Excel::download(new InStockExport($dataCetak), $name, \Maatwebsite\Excel\Excel::MPDF);
        
    // }
    
    public function exportExcel()  {
        $dataCetak = $this->getData();

        if($this->searchKey) $name = "InStock_".$this->searchKey."-".date('Ymd').".xlsx";
        else $name = "InStock-".date('Ymd').".xlsx";
        return Excel::download(new InStockExport($dataCetak), $name, \Maatwebsite\Excel\Excel::XLSX);
        
    }

    private function getData() {
        $query = DB::table('material_mst')
        ->selectRaw('matl_no as material_no,qty,loc_cd as locate')
        ->where('qty','>',0)
        ->groupBy(['matl_no','loc_cd','qty']);
        $query->where('matl_no','like',"%$this->searchKey%");
        return $query->get();
        
    }
}
