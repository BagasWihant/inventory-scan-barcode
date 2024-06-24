<?php

namespace App\Livewire;

use App\Exports\StockTakingConfirm;
use Livewire\Component;
use App\Models\MenuOptions;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use Spatie\LaravelIgnition\Recorders\DumpRecorder\Dump;

class StockTakingConf extends Component
{
    public $data, $queryStock, $stoID, $confirm;

    public function mount()
    {
        $sto = MenuOptions::where('status', '1')
            ->where('user_id', auth()->user()->id)->first();
        if ($sto) {
            $this->stoID = $sto->id;
            $this->confirm = true;
        } else {
            $this->confirm = false;
            $this->stoID = "-";
        }
    }
    public function konfirmasi()
    {
        $export = $this->data;
        foreach ($this->data as  $value) {
            DB::table('material_mst')
                ->where('matl_no', $value->material_no)
                ->update([
                    'qty_IN' => $value->qty,
                ]);
            DB::table('stock_takings')
                ->where('is_taking', 0)
                ->where('sto_id', $this->stoID)
                ->update([
                    'is_taking' => 1
                ]);
        }

        return Excel::download(new StockTakingConfirm($export), "Confirmation Stock_$this->stoID _" . date('YmdHis') . ".pdf", \Maatwebsite\Excel\Excel::MPDF);
    }
    
    public function export()
    {
        $export = $this->data;

        return Excel::download(new StockTakingConfirm($export), "Confirmation Stock_$this->stoID _" . date('YmdHis') . ".pdf", \Maatwebsite\Excel\Excel::MPDF);
    }
    public function render()
    {
        $queryStock = DB::table('stock_takings')
            ->select('material_no', 'loc', 'qty', 'hitung', 'created_at', DB::raw('ROW_NUMBER() OVER (PARTITION BY material_no ORDER BY created_at DESC) AS lastest_rank'))
            ->where('is_taking', '0');
        $getqryLatest = $queryStock->get();
        $data = $getqryLatest->where('lastest_rank', 1);

        $listMat = $data->pluck('material_no')->unique()->all();

        $inStock = DB::table('material_in_stock')
            ->select('material_no', 'locate', DB::raw('sum(picking_qty) as qty'))
            ->whereIn('material_no', $listMat)
            ->groupBy('material_no', 'locate')
            ->get();

        foreach ($inStock as $value) {
            foreach ($data as $st) {
                if ($st->material_no == $value->material_no) {
                    $st->locsys = $value->locate;
                    $st->qtysys = $value->qty;
                    $res = $st->qty - $value->qty; 
                    if($res < 0) $st->min = abs($res);
                    elseif($res > 0) $st->plus = $res;
                }
            }
        }

        $this->data = $data;



        return view('livewire.stock-taking-conf');
    }
}
