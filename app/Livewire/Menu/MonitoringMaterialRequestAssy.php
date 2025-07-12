<?php

namespace App\Livewire\Menu;

use App\Models\MaterialRequest;
use App\Models\MaterialRequestAssy;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithoutUrlPagination;
use Livewire\WithPagination;

class MonitoringMaterialRequestAssy extends Component
{
    use WithPagination, WithoutUrlPagination;
    public $time, $totalCount;
    public $material;
    public $dateFilter;
    public $log;

    public function mount()
    {
        $this->dateFilter = date('Y-m-d');
    }
    public function refreshTable()
    {
        DB::enableQueryLog();
        $this->material = MaterialRequestAssy::when($this->dateFilter, function ($q) {
            $q->whereRaw('CONVERT(DATE,created_at) = ?', [$this->dateFilter]);
        })
            ->selectRaw('transaksi_no, status, type, issue_date, line_c, CONVERT(DATE,created_at) as created_at')
            ->groupByRaw('transaksi_no, status, type, CONVERT(DATE,created_at), issue_date, line_c')
            ->orderByDesc(DB::raw('CONVERT(DATE,created_at)'))
            ->get();

        $transaksiNos = $this->material->pluck('transaksi_no')->unique();
        $this->totalCount = count($transaksiNos);

        if ($transaksiNos->isNotEmpty()) {
            $details = MaterialRequestAssy::whereIn('material_request_assy.transaksi_no', $transaksiNos)
                ->leftJoin('temp_requests as r', function ($join) {
                    $join->on('material_request_assy.transaksi_no', '=', 'r.transaksi_no')
                        ->on('material_request_assy.material_no', '=', 'r.material_no');
                })
                ->leftJoin('material_mst as b', 'material_request_assy.material_no', '=', 'b.matl_no')
                ->select([
                    'material_request_assy.*',
                    'r.qty_supply',
                    'b.qty as stock'
                ])
                ->get()
                ->groupBy('transaksi_no'); 
                
            foreach ($this->material as $data) {
                $data->detail = $details[$data->transaksi_no] ?? collect();
            }
        }

        // dd($this->material);

        $this->log = DB::getRawQueryLog();
    }
    public function render()
    {
        $this->time =  now();
        $this->refreshTable();
        return view('livewire.menu.monitoring-material-request-assy');
    }
}
