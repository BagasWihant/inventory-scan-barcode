<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\Attributes\On;
use Livewire\WithPagination;
use App\Exports\InStockExport;
use App\Exports\ExportDetailKartuStok;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Livewire\WithoutUrlPagination;
use Maatwebsite\Excel\Facades\Excel;

class MaterialStock extends Component
{
    use WithPagination,WithoutUrlPagination;
    public $searchKey,$perPage=10,$modal=false,$modalName;
    public $detailMaterial = [],$dataCetak=[];

    public function setPerPage() {
        $this->resetPage();
    }

    public function editLokasi($mat,$locate) {
        DB::table('material_mst')->where('matl_no', "$mat")->update(['loc_cd' => "$locate"]);
    }

    public function printMaterial($mat){
        $data = DB::select('EXEC sp_WH_inv_kartustok ?,?,?,?',['detail',$mat,'','']);
        // $this->dispatch('showModalDetail',$data);
        $this->modal = true;
        $this->detailMaterial = $data;
        $this->modalName = $mat;
    }
    public function closeModal() {
        $this->detailMaterial = [];
        $this->modalName = null;
        $this->modal = false;
    }

    public function exportDetailExcel() {
        return Excel::download(new ExportDetailKartuStok(collect($this->detailMaterial),$this->modalName), $this->modalName."-".date('Ymd').".xlsx", \Maatwebsite\Excel\Excel::XLSX);
          
    }


    public function render()
    {
        // $query = DB::table('material_in_stock')
        // ->selectRaw('material_no,sum(picking_qty) as qty, locate')
        // ->groupBy(['material_no','locate']);
        // $query->where('material_no','like',"%$this->searchKey%");
        // $data= $query->get();

        $query = DB::select("EXEC sp_WH_inv_mst ?",[$this->searchKey??'']);
        $data = $query;
        $this->dataCetak  = $query;

        return view('livewire.material-stock',[
            'datas'=>$data,
        ]);
    }
    

    
    public function exportExcel()  {
        // dd($this->dataCetak);
        if($this->searchKey) $name = "InStock_".$this->searchKey."-".date('Ymd').".xlsx";
        else $name = "InStock-".date('Ymd').".xlsx";
        return Excel::download(new InStockExport(collect($this->dataCetak)), $name, \Maatwebsite\Excel\Excel::XLSX);
        
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
