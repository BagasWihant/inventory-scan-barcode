<?php

namespace App\Livewire;

use App\Models\User;
use Livewire\Component;
use App\Models\MenuOptions;
use App\Exports\ExportStockTaking;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class PrepareTaking extends Component
{
    public $statusActive, $date, $id,$canOpen,$user_id, $dataStatus,$dataStock;

    
   
    public function changeStatusActive()
    {
    }

    public function open()
    {
        sleep(0.5);
        $data = MenuOptions::find($this->dataStatus->id);
        $this->date = $this->date ?? now();
        $data->update([
            'status' => 0,
            'date_end' => date('Y-m-d H:i:s', strtotime($this->date))
        ]);

    }
    public function lock()
    {
        sleep(0.5);
        $this->date = $this->date ?? now();
        MenuOptions::create([
            'status' => 1,
            'user_id' => $this->user_id,            
            'date_start' => date('Y-m-d H:i:s', strtotime($this->date))
        ]);
    }

    public function exportPdf(){
        $name = auth()->user()->username . date('d-m-Y H:i');
        return Excel::download(new ExportStockTaking($this->dataStock), "Stock Taking - $name.pdf", \Maatwebsite\Excel\Excel::MPDF);
    }

    public function render()
    {
        $this->user_id = auth()->user()->id;

        $data = MenuOptions::where('status', 1);
        $this->canOpen =$data->exists();
        
        if ($this->canOpen) {
            $this->dataStatus = $data->first();
        }

        $qry = DB::table('material_in_stock')->where('user_id', auth()->user()->id)->selectRaw('material_no, sum(picking_qty) as qty')->groupBy('material_no');
        $this->dataStock= $qry->get();
        
        return view('livewire.prepare-taking');
    }
}
