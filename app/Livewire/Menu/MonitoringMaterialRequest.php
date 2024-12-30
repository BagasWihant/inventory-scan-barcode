<?php

namespace App\Livewire\Menu;

use App\Models\MaterialRequest;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithoutUrlPagination;
use Livewire\WithPagination;

class MonitoringMaterialRequest extends Component
{
    use WithPagination, WithoutUrlPagination;
    public $time, $totalCount;
    public $material;

    public function refreshTable()
    {
        $materialQuery = MaterialRequest::with('user')
            ->select([
                DB::raw("STRING_AGG(material_no, ', ') WITHIN GROUP (ORDER BY material_no) AS material_no"),
                // DB::raw("STRING_AGG(user_request, ', ') WITHIN GROUP (ORDER BY material_no) AS user_request"),
                DB::raw('MAX(created_at) as created_at'),
                DB::raw('MAX(proses_date) as proses_date'),
                DB::raw('count(transaksi_no) as total_varian'),
                "transaksi_no",
                "user_request",
                'user_id',
                'status',
            ])
            ->where('status', '!=', '-')
            ->where(DB::raw('CONVERT(date, created_at)'), $this->time->format('Y-m-d'))
            ->orderBy('created_at', 'desc')
            ->groupBy('transaksi_no', 'user_id', 'status', 'created_at','user_request');

        $this->totalCount = MaterialRequest::where('status', '!=', '-')
            ->where(DB::raw('CONVERT(date, created_at)'), $this->time->format('Y-m-d'))
            ->count();

        $this->material = $materialQuery->get();
    }
    public function render()
    {
        $this->time =  now();
        $this->refreshTable();
        return view('livewire.menu.monitoring-material-request');
    }
}
