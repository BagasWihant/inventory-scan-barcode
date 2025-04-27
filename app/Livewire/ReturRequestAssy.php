<?php

namespace App\Livewire;

use App\Models\MaterialInStockAssy;
use App\Models\ReturAssy;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class ReturRequestAssy extends Component
{
    public $materialRequest = [];
    public $listLine = [];
    public $line_c;
    public $date;

    private function getListLine()
    {
        $listLine = DB::table('material_in_stock_assy')->select('line_c')
            ->where('issue_date', $this->date)
            ->distinct();

        return $listLine->get();
    }

    public function mount()
    {
        $this->date = Carbon::now()->format('Y-m-d');

        $this->listLine = $this->getListLine($this->date);
    }

    public function dateDebounce()
    {
        $this->listLine = $this->getListLine($this->date);
        return $this->listLine;
    }
    public function lineChange()
    {
        // cek data di request assy 
        $returnData = MaterialInStockAssy::where('user_id', auth()->user()->id)
            ->where('line_c', $this->line_c)
            ->where('issue_date', $this->date)
            ->get();

        $this->materialRequest = $returnData;

        $this->dispatch('materialsUpdated', $this->materialRequest);
    }


    public function submitRequest($data)
    {
        // ambil data yang sudah di edit
        foreach ($data as $v) {
            if(isset($v['retur_qty'])){
                ReturAssy::create([
                    'material_no' => $v['material_no'],
                    'material_name' => $v['material_name'],
                    'qty' => $v['retur_qty'],
                    'surat_jalan' => $v['surat_jalan'],
                    'line_c' => $v['line_c'],
                    'issue_date' => $v['issue_date'],
                    'status' => '-',
                ]);
            }
        }

        $this->resetField();
        
    }

    public function resetField()
    {
        $this->materialRequest = [];
        $this->line_c = null;
        $this->date = Carbon::now()->format('Y-m-d');
        $this->dispatch('materialsUpdated', $this->materialRequest);
    }

    public function render()
    {
        return view('livewire.retur-request-assy');
    }
}
