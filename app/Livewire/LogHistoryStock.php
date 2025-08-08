<?php

namespace App\Livewire;

use App\Exports\ExportLogHistory;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Maatwebsite\Excel\Facades\Excel;

class LogHistoryStock extends Component
{
    public $listMaterial = [];
    public function render()
    {
        return view('livewire.log-history-stock');
    }

    public function getData(
        $materialNo,
        $lineCode,
        $date,
        $dateSupply
    ) {

        $main = DB::table('material_in_stock')
            ->selectRaw("material_no, picking_qty as qty, created_at, 'in' as status")
            ->where('material_no', $materialNo)
            ->when($lineCode, fn($q) => $q->where('line_c', $lineCode))
            ->when($date, fn($q) => $q->whereDate('created_at', $date));

        $union = DB::table('setup_dtl')
            ->selectRaw("material_no, qty, setup_mst.created_at, 'out' as status")
            ->leftJoin('setup_mst', 'setup_dtl.setup_id', '=', 'setup_mst.id')
            ->where('material_no', $materialNo)
            ->when($lineCode, fn($q) => $q->where('line_cd', $lineCode))
            ->when($dateSupply, fn($q) => $q->whereDate('setup_mst.created_at', $dateSupply));

        $finalSql = match (true) {
            $date && !$dateSupply   => $main, // jika hanya date receiving aja yang diisi maka setupdtl gakusah tampil, soalnya yang di isi tanggal receiving aja
            $dateSupply && !$date   => $union, // begitupun sebaliknya, jika yang di isi date supply aja
            default                 => $main->unionAll($union),  // kalo diisi smua baru digabung
        };

        $data = $finalSql->get();
        $this->listMaterial = $data;

        return $data;
    }

    public function export()
    {
        $data = $this->listMaterial;
        return Excel::download(new ExportLogHistory($data), "Log History Stock " . date('YmdHis') . ".xlsx", \Maatwebsite\Excel\Excel::XLSX);
    }
}
