<?php

namespace App\Livewire;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class BomRequest extends Component
{
    public $product_no;
    public $dc;

    public function getLineCode()
    {
        // cache sehari line nya, kalo yakin line jarang ganti cache lama saja biar gak panggil ke db
        return Cache::remember('linecode_bom_request', 60 * 60 * 24, function () {
            return DB::table('mst_line_code')
                ->selectRaw("location_cd, id")
                ->get();
        });
    }

    public function searchPM($qey)
    {
        // ini di cache 20 menit, jika user yg sama cari nama yang sama, muncul nya itu terus, jika ingin beda tulis yang lebih komplit
        return Cache::remember($qey . '_' . auth()->user()->id, 60 * 20, function () use ($qey) {
            return DB::table('db_bantu.dbo.bom')
                ->selectRaw("
            REPLACE(CONCAT(product_no,'_',dc), ' ', '') AS product_no,
            REPLACE(CONCAT(product_no,'_',dc), ' ', '') AS id
            ")
                ->whereRaw("REPLACE(CONCAT(product_no,'_',dc), ' ', '') LIKE ?", ["%{$qey}%"])
                ->groupBy('product_no', 'dc')
                ->limit(5)
                ->get();
        });
    }

    public function selectPM($item)
    {
        $this->dispatch('loading', true);
        $no = explode('_', $item['id']);
        $this->product_no = $no[0];
        $this->dc = $no[1];
        // dd($this->product_no, $this->dc,$item);

        $listData = DB::table('db_bantu.dbo.bom as b')
            ->selectRaw("product_no,dc,material_no,matl_nm,bom_qty")
            ->leftJoin('pt_kias.dbo.material_mst as m', 'b.material_no', '=', 'm.matl_no')
            ->whereRaw("product_no LIKE ?", ["%{$no[0]}%"])
            ->whereRaw("dc LIKE ?", ["%{$no[1]}%"])
            ->get();

        $this->dispatch(
            'product-model-selected',
            data: $listData
        );
    }

    public function submitData($param)
    {
        $data = $param['data'];
        $line = $param['lineCode'];
        $qtyRequest = $param['qtyRequest'];
        $date = $param['date'];


        $ymd = date('Ymd');
        $d = date('d');
        $getConfig = DB::table('WH_config')->select('value')->whereIn('config', ['BomRequest', 'PeriodBomRequest'])->get();
        if (count($getConfig) < 2) {
            DB::table('WH_config')->insert([
                ['config' => 'BomRequest', 'value' => 0],
                ['config' => 'PeriodBomRequest', 'value' => $ymd],
            ]);
            $getConfig = DB::table('WH_config')->select('value')->whereIn('config', ['BomRequest', 'PeriodBomRequest'])->get();
        }

        $noBomRequest = (int)$getConfig[0]->value + 1;

        // kit direset perhari
        if ($getConfig[1]->value != $ymd) {
            $noBomRequest = 1;
            DB::table('WH_config')->where('config', 'PeriodBomRequest')->update(['value' => $ymd]);
        }


        $kitNo = 'L' . $d . '-' . str_pad($noBomRequest, 4, '0', STR_PAD_LEFT);

        // update nomor kit di config
        DB::table('WH_config')->where('config', 'BomRequest')->update(['value' => $noBomRequest]);

        $cushdesc = explode('-', $this->dc);
        try {
            DB::beginTransaction();

            // insert mps
            DB::table('mps')->insert([
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
                DB::table('mps_detail')->insert([
                    'kit_no' => $kitNo,
                    'material_no' => $d['material_no'],
                    'req_bom' => $d['qty_request'],
                    'issue_dt' => $date,
                    'entry_dt' => now(),
                    'issue_by' => auth()->user()->username,
                ]);
            }

            DB::commit();
            return [
                'success' => true,
                'msg' => 'Berhasil Simpan'
            ];
        } catch (\Throwable $th) {
            DB::rollBack();
            return [
                'success' => false,
                'msg' => $th->getMessage().' '.$th->getLine()
            ];
        }
    }

    public function render()
    {
        $line = $this->getLineCode();

        return view('livewire.bom-request', compact('line'));
    }
}
