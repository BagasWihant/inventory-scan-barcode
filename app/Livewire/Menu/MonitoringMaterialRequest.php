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
    public $time;
    public function render()
    {
        $this->time = now();
        DB::enableQueryLog();
        $material = MaterialRequest::with('user')
        ->select([
            DB::raw("STRING_AGG(material_no, ', ') WITHIN GROUP (ORDER BY material_no) AS material_no"),
            DB::raw('MAX(created_at) as created_at'),
            DB::raw('MAX(proses_date) as proses_date'),
            DB::raw('count(transaksi_no) as total_varian'),
            "transaksi_no",
            'user_request',
            'user_id',
            'status',
            ])
            ->where('status','!=','-')
        ->orderBy('created_at', 'desc')
        ->groupBy('transaksi_no','user_request','user_id','status')->paginate(5);
        // dd($this->time,$material[0]->created_at,Carbon::parse($this->time)->shortAbsoluteDiffForHumans($material[0]->created_at));
        return view('livewire.menu.monitoring-material-request', compact('material'));
    }
}
