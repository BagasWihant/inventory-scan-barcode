<?php

namespace App\Livewire;

use App\Models\MaterialInStockAssy;
use App\Models\ReturQa;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
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
    private function generateNoRetur()
    {
        $prefix = date('Ym');
        $configKey = 'RTR';

        $row = DB::table('WH_config')->where('config', $configKey)->first();

        if (!$row || !Str::startsWith($row->value, $prefix)) {
            $newValue = $prefix . '0001';

            DB::table('WH_config')->updateOrInsert(
                ['config' => $configKey],
                ['value' => $newValue]
            );
        } else {
            $lastNumber = (int) substr($row->value, -4);
            $newNumber = str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);
            $newValue = $prefix . $newNumber;

            DB::table('WH_config')->where('config', $configKey)->update(['value' => $newValue]);
        }
        return $newValue;
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
        $returnData = DB::table('material_in_stock_assy as mas')->where('mas.user_id', auth()->user()->id)
            ->join('material_request_assy as sd', function ($join) {
                $join->on('mas.material_no', '=', 'sd.material_no')
                    ->on('mas.transaksi_no', '=', 'sd.transaksi_no')
                    ->on('mas.issue_date', '=', 'sd.issue_date');
            })
            ->where('mas.line_c', $this->line_c)
            ->where('mas.issue_date', $this->date)
            ->selectRaw('mas.material_no, mas.material_name, sum(mas.qty) as qty,
            mas.surat_jalan, mas.issue_date, mas.line_c, 
            sum(sd.request_qty) as qty_supply_assy, (sum(sd.request_qty) - sum(mas.qty)) as qty_retur')
            ->groupByRaw('mas.material_no,mas.material_name,mas.surat_jalan,mas.issue_date,mas.line_c')->get();
        // dd($returnData);

        // join setup dtl qty sama terus. soalnya di proses assy input qty nya emang sama 
        // $returnData = MaterialInStockAssy::where('user_id', auth()->user()->id)
        //     ->join('material_request_assy as sd', function ($join) {
        //         $join->on('material_in_stock_assy.material_no', '=', 'sd.material_no')
        //             ->on('material_in_stock_assy.transaksi_no', '=', 'sd.transaksi_no')
        //             ->on('material_in_stock_assy.issue_date', '=', 'sd.issue_date');
        //     })
        //     ->where('line_c', $this->line_c)
        //     ->where('issue_date', $this->date)
        //     ->selectRaw('material_in_stock_assy.*,sum(sd.request_qty) as qty_supply_assy, (sd.request_qty - material_in_stock_assy.qty) as qty_retur')
        //     ->get();

        return $returnData;
    }


    public function submitRequest($data)
    {
        $noRetur = 'RTR-' . $this->generateNoRetur();
        // ambil data yang sudah di edit
        foreach ($data as $v) {
            if (isset($v['retur_qty']) && $v['retur_qty'] > 0) {
                ReturQa::create([
                    'no_retur' => $noRetur,
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
        return 'success';
    }

    public function resetField()
    {
        $this->materialRequest = [];
        $this->line_c = null;
        $this->date = Carbon::now()->format('Y-m-d');
        $this->dispatch('materialsUpdated', $this->materialRequest);
    }

    public function streamTableSum()
    {
        $data = DB::table('retur_assy')
            ->selectRaw('no_retur, COUNT(no_retur) as count, MAX(created_at) as created_at')
            ->groupBy('no_retur')->get();
        $now = Carbon::now();
        $totalQty = 0;
        foreach ($data as $value) {
            $start = Carbon::parse($value->created_at);
            $value->time_request = $start->longRelativeDiffForHumans($now);
            $totalQty += $value->count;
        }

        $returndata = [
            'qty' => $totalQty,
            'data' => $data
        ];
        return $returndata;
    }


    public function render()
    {
        return view('livewire.retur-request-assy');
    }
}
