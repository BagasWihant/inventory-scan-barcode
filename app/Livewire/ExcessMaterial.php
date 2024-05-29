<?php

namespace App\Livewire;

use App\Imports\KelebihanImport;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithFileUploads;
use Maatwebsite\Excel\Facades\Excel;

class ExcessMaterial extends Component
{
    use WithFileUploads;
    public $fileExcel;
    public function render()
    {
        $data = DB::table('material_kelebihans')
        ->selectRaw('pallet_no,material_no,sum(picking_qty) as qty, count(pallet_no) as pax')
        ->groupBy(['material_no','pallet_no'])
        ->get();
        return view('livewire.excess-material',compact('data'));
    }

    public function import() {
        $this->validate([
            'fileExcel' => 'required|mimes:xlsx,xls',
        ],[
            'fileExcel.mimes' => 'File Excel Harus Berformat Excel',
        ]);

        Excel::import(new KelebihanImport, $this->fileExcel);
    }
}
