<?php

namespace App\Livewire;

use App\Models\itemIn;
use Livewire\Component;
use App\Exports\ExportStockTaking;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class PrintStockTaking extends Component
{
    public $data;
    public function mount()
    {
        // dd($this->listData);
    }
    public function exportPdf()
    {
        $name = auth()->user()->username . date('d-m-Y H:i');
        return Excel::download(new ExportStockTaking($this->data), "Stock Taking - $name.pdf", \Maatwebsite\Excel\Excel::MPDF);
    }
    public function render()
    {
        // $qry = itemIn::where('user_id', auth()->user()->id)->selectRaw('material_no, sum(picking_qty) as qty')->groupBy('material_no');
        $qry = DB::table('material_in_stock')->where('user_id', auth()->user()->id)->selectRaw('material_no, sum(picking_qty) as qty')->groupBy('material_no');
        $data= $qry->get();
        $this->data = $data;
        return view('livewire.print-stock-taking',['listData'=>$data]);
    }
}
