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
    public $dateFilter;

    public function mount()
    {
        $this->dateFilter = date('Y-m-d');
    }
    public function refreshTable()
    {
        $materialQuery = DB::table('mps')->when($this->dateFilter, function ($query) {
            $query->whereDate('plan_issue_dt', $this->dateFilter);
        });

        $rows = $materialQuery->get();

        $kitNos = $rows->pluck('kit_no')->unique()->values();

        $details = DB::table('mps_detail')
            ->whereIn('kit_no', $kitNos)
            ->get()
            ->groupBy('kit_no');

        $rows->transform(function ($r) use ($details) {
            $r->detail = $details->get($r->kit_no, collect());
            return $r;
        });
        
        $this->material = $rows;
        $this->totalCount = $rows->count();


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
