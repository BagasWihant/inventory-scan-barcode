<?php

namespace App\Livewire;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class BomRequestV2 extends Component
{
    public $lineCode;
    public $lines = [];

    public function mount()
    {
        $this->lines = Cache::remember('linecode_bom_requestv2', 60 * 60 * 24, function () {
            return DB::table('it.dbo.prs_master_line')
                ->selectRaw('Line as location_cd, id')
                ->get();
        });
    }

    public function showData($line_id, $start, $end)
    {
        $plan = DB::table('it.dbo.prs_assy_daily_rev1_plan as p')
            ->join('it.dbo.prs_master_product as m', 'p.product_id', '=', 'm.id')
            ->where('p.line_id', $line_id)
            ->where('p.tanggal', '>=' ,$start)
            ->where('p.tanggal', '<=' ,$end)
            ->selectRaw("p.product_id, p.planning, TRIM(REPLACE(m.product_no, ' ', '')) as product_no, m.dc")
            ->get();

        $listData = [];
        $qty_request = 0;
        foreach ($plan as $item) {
            // jumlah qty request 
            $qty_request += $item->planning;
            $bom = DB::table('db_bantu.dbo.bom as b')
                ->leftJoin('pt_kias.dbo.material_mst as m', 'b.material_no', '=', 'm.matl_no')
                ->whereRaw("TRIM(REPLACE(product_no, ' ', '')) LIKE ?", ["%{$item->product_no}%"])
                ->whereRaw('dc LIKE ?', ["%{$item->dc}%"])
                ->select('product_no', 'dc', 'material_no', 'matl_nm', 'bom_qty')
                ->get()
                ->toArray();

            $listData = array_merge($listData, $bom);
        }
        $this->dispatch(
            'product-model-selected',
            data: $listData,
            qty_request: $qty_request,
        );
    }

    public function submitData($param)
    {
        $data = $param['data'];
        $line = $param['lineCode'];
        $qtyRequest = $param['qtyRequest'];
        $date = $param['date'];

        $y = date('y');
        $getConfig = DB::table('WH_config')->select('value')->whereIn('config', ['BomRequest', 'PeriodBomRequest'])->get();
        if (count($getConfig) < 2) {
            DB::table('WH_config')->insert([
                ['config' => 'BomRequest', 'value' => 0],
                ['config' => 'PeriodBomRequest', 'value' => $y],
            ]);
            $getConfig = DB::table('WH_config')->select('value')->whereIn('config', ['BomRequest', 'PeriodBomRequest'])->get();
        }

        $noBomRequest = (int) $getConfig[0]->value + 1;

        // kit direset perhari
        if ($getConfig[1]->value != $y) {
            $noBomRequest = 1;
            DB::table('WH_config')->where('config', 'PeriodBomRequest')->update(['value' => $y]);
        }

        $kitNo = 'L' . $y . '-' . str_pad($noBomRequest, 4, '0', STR_PAD_LEFT);

        // update nomor kit di config
        DB::table('WH_config')->where('config', 'BomRequest')->update(['value' => $noBomRequest]);

        $cushdesc = explode('-', $this->dc);
        try {
            DB::beginTransaction();

            // // insert mps
            $mpsid = DB::table('mps')->insertGetId([
                'product_no' => $this->product_no,
                'cusdesch_c1' => $cushdesc[0],
                'cusdesch_c2' => $cushdesc[1],
                'cusdesch_c3' => $cushdesc[2],
                'lot_no' => $qtyRequest,
                'plan_issue_qty' => $qtyRequest,
                'assy_section_cd' => $line,
                'line_c' => $line,
                'plan_issue_dt' => $date,
                'issue_dt' => $date,
                'entry_dt' => now(),
                'entry_by' => auth()->user()->username,
                'kit_no' => $kitNo
            ]);

            foreach ($data as $d) {
                // lagi insert dengan nilai remain dan issue baru
                $idDetail = DB::table('mps_detail')->insertGetId([
                    'kit_no' => $kitNo,
                    'material_no' => $d['material_no'],
                    'req_bom' => $d['qty_request'],
                    'issue_dt' => $date,
                    'entry_dt' => now(),
                    'issue_by' => auth()->user()->username,
                ]);
            }

            $sqlInsert = 'INSERT INTO [172.99.0.5].[DB_Lain].[dbo].[mps]
                    SELECT * FROM mps
                    WHERE id = ?';

            foreach ($data as $d) {
                // ambil data sek
                $getdata = DB::connection('server_asus')
                    ->table('db_bantu.dbo.b3r_tabel')
                    ->selectRaw('remain,issue')
                    ->where('material_no', $d['material_no'])
                    ->first();

                // update remain dan issue
                DB::table('mps_detail')
                    ->where('kit_no', $kitNo)
                    ->where('material_no', $d)
                    ->update([
                        'remain' => $getdata->remain,
                        'issue' => $getdata->issue,
                    ]);

                $sqlInsert = 'INSERT INTO [172.99.0.5].[DB_Lain].[dbo].[mps_detail]
                    SELECT * FROM mps_detail
                    WHERE id = ?';
                DB::statement($sqlInsert, [$idDetail]);
            }

            DB::statement($sqlInsert, [$mpsid]);

            DB::commit();
            return [
                'success' => true,
                'msg' => 'Berhasil Simpan'
            ];
        } catch (\Throwable $th) {
            DB::rollBack();
            return [
                'success' => false,
                'msg' => $th->getMessage() . ' ' . $th->getLine()
            ];
        }
    }

    public function render()
    {
        $line = [];

        return view('livewire.bom-request-v2', compact('line'));
    }
}
