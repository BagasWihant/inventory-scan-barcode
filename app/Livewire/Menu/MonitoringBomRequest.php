<?php

namespace App\Livewire\Menu;

use App\Models\MaterialRequest;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithoutUrlPagination;
use Livewire\WithPagination;

class MonitoringBomRequest extends Component
{
    use WithPagination, WithoutUrlPagination;
    public $time, $totalCount;
    public $material;

    public function refreshTable()
    {
        $materialQuery = DB::table('mps');

        
        $data = $materialQuery->get();
        $this->material = $data;
        $this->totalCount = $data->count();
        

        // if ($data->whereIn('status', [0, 9])->isNotEmpty()) {
        //     $this->dispatch('playSound');
        // }else{
        //     $this->dispatch('stopSound');
        // }
    }
    public function render()
    {
        $this->time =  now();
        $this->refreshTable();
        return view('livewire.menu.monitoring-bom-request');
    }
}
