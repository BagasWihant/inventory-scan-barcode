<?php

namespace App\Livewire;

use App\Exports\StockTakingConfirm;
use App\Models\itemIn;
use Livewire\Component;
use App\Models\MenuOptions;
use App\Models\RealStockTaking;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use Spatie\LaravelIgnition\Recorders\DumpRecorder\Dump;

class StockTakingConf extends Component
{
    public $data, $queryStock, $userID, $confirm, $sto, $search;

    public function mount()
    {
        $this->userID = auth()->user()->id;
        $this->sto = MenuOptions::where('status', '1')->first();
        if ($this->sto) {
            $this->confirm = true;
        } else {
            $this->confirm = false;
        }
    }
    public function konfirmasi()
    {
        $export = $this->data;

        foreach ($this->data as  $value) {
            $result_qty = $value->qty - $value->qtysys;
            RealStockTaking::create([
                'sto_id' => $this->sto->id,
                'user_id' => $this->userID,
                'material_no' => $value->material_no,
                'loc_sys' => $value->locsys,
                'qty_sys' => $value->qtysys,
                'loc_sto' => $value->loc,
                'qty_sto' => $value->qty,
                'result_qty' => $result_qty,
            ]);

            DB::table('material_in_stock')
                ->where('material_no', $value->material_no)
                ->where('user_id', $this->userID)
                ->update([
                    'is_taking' => 2
                ]);

            itemIn::create([
                'pallet_no' => "STO-" . $this->sto->id,
                'material_no' => $value->material_no,
                'picking_qty' => $result_qty,
                'locate' => $value->loc,
                'user_id' => $this->userID,
                'is_taking' => 9
            ]);

            DB::table('stock_takings')
                ->where('is_taking', 0)
                ->where('sto_id', $this->sto->id)
                ->update([
                    'is_taking' => 1
                ]);
        }

        $this->sto->update([
            'status' => 0
        ]);

        $this->confirm = false;
        return Excel::download(new StockTakingConfirm($export), "Confirmation Stock_" . $this->sto->id . "_" . date('YmdHis') . ".pdf", \Maatwebsite\Excel\Excel::MPDF);
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
            ->where('qty', ">", '0')
            ->where('is_taking', '0');

        if ($this->search) $queryStock->where('material_no', 'like', '%' . $this->search . '%');

        $getqryLatest = $queryStock->get();
        $data = $getqryLatest->where('lastest_rank', 1);
        

        $listMat = $data->pluck('material_no')->unique()->all();

        $inStock = DB::table('material_in_stock')
            ->select('material_no', 'locate', DB::raw('sum(picking_qty) as qty'))
            ->where('material_no', '>', '0')
            ->whereIn('material_no', $listMat)
            ->groupBy('material_no', 'locate')
            ->get();
            
        $countInStock = count($inStock);
        foreach ($data as $st) {
            if ($countInStock > 0) {
                $found = false;
                foreach ($inStock as $value) {                    
                    if ($st->material_no == $value->material_no) {
                        $st->locsys = $value->locate;
                        $st->qtysys = $value->qty;
                        $res = $st->qty - $value->qty;
                        if ($res < 0) $st->min = abs($res);
                        elseif ($res > 0) $st->plus = $res;
                        $found = true;
                        break;
                    } 
                }

                if (!$found) {
                    $st->locsys = 'NOT FOUND';
                    $st->qtysys = 0;
                    $res = $st->qty - 0;
                    
                    if ($res < 0) $st->min = abs($res);
                    elseif ($res > 0) $st->plus = $res;
                }
            } else {
                $st->locsys = 'NOT FOUND';
                $st->qtysys = 0;
                $res = $st->qty - 0;
                if ($res < 0) $st->min = abs($res);
                elseif ($res > 0) $st->plus = $res;
            }
        }

        // foreach ($inStock as $value) {
        //     foreach ($data as $st) {
        //         if ($st->material_no == $value->material_no) {
        //             $st->locsys = $value->locate;
        //             $st->qtysys = $value->qty;
        //             $res = $st->qty - $value->qty;
        //             if ($res < 0) $st->min = abs($res);
        //             elseif ($res > 0) $st->plus = $res;
        //         }
        //     }
        // }

        $this->data = $data;



        return view('livewire.stock-taking-conf');
    }
}
