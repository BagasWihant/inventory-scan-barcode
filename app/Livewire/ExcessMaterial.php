<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\Attributes\On;
use Livewire\WithFileUploads;
use App\Imports\KelebihanImport;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Storage;

class ExcessMaterial extends Component
{
    use WithFileUploads;
    public $fileExcel,$searchKey,$dataCetak;
    public function render()
    {
        $query = DB::table('material_kelebihans')
        ->selectRaw('pallet_no,material_no,sum(picking_qty) as qty, count(pallet_no) as pax')
        ->groupBy(['material_no','pallet_no']);
       
        $query->where('pallet_no','like',"%$this->searchKey%");

        if($this->searchKey) $this->dispatch('searchFocus');
        $data= $query->get();
        
        
        $this->dataCetak = $data;
        return view('livewire.excess-material',compact('data'));
    }
    
    public function sampleDownload() {
        $this->dispatch('modalHide');

        return Storage::download('Sample.xlsx');
    }
    public function upload() {
        dd($this->fileExcel);
    }
}
