<?php

namespace App\Livewire;

use Illuminate\Support\Facades\DB;
use Livewire\Component;

class ReturProsesAssy extends Component
{
    public function loadData(){
        $data = DB::table('retur_assy')
        ->selectRaw('no_retur, min(issue_date) as issue_date, line_c, status ')
        ->groupByRaw('no_retur, line_c, status')->get();
        return $data;
    }

    public function getDetail($no_retur){
        $data = DB::table('retur_assy')
        ->where('no_retur', $no_retur)->get();
        
        return $data;    
    }

    public function saveDetailScanned($no_retur){
        $data = DB::table('retur_assy')
        ->where('no_retur', $no_retur)->update(['status' => '1']);

        return 'success';
    }

    public function render()
    {
        return view('livewire.retur-proses-assy');
    }
}
