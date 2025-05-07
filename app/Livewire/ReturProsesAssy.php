<?php

namespace App\Livewire;

use App\Models\ReturAssy;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class ReturProsesAssy extends Component
{
    public function loadData()
    {
        $data = DB::table('retur_qa')
            ->selectRaw('no_retur, min(issue_date) as issue_date, line_c, status ')
            ->groupByRaw('no_retur, line_c, status')->get();
        return $data;
    }

    public function getDetail($no_retur)
    {
        $data = DB::table('retur_qa')
            ->where('no_retur', $no_retur)->get();

        return $data;
    }

    public function saveDetailScanned($req)
    {

        $no_retur = $req['0'];
        $status = $req['1'];
        $data = $req['2'];

        if ($status == 'x') {
            // direject
            DB::table('retur_qa')
                ->where('no_retur', $no_retur)->update(['status' => $status]);
            return 'success';
        } elseif ($status == '1') {
            foreach ($data as $v) {
                // jika ada yang oke
                if (isset($v["retur_qty_pass"])) {
                    if ($v["retur_qty_pass"] > 0) {
                        ReturAssy::create([
                            'no_retur' => $v['no_retur'],
                            'material_no' => $v['material_no'],
                            'material_name' => $v['material_name'],
                            'qty' => $v['retur_qty_pass'],
                            'surat_jalan' => $v['surat_jalan'],
                            'line_c' => $v['line_c'],
                            'issue_date' => $v['issue_date'],
                            'status' => 1,
                        ]);

                        $matMst = DB::table('material_mst')->where('matl_no', $v['material_no']);
                        $matMstData = $matMst->first();
                        $matMst->update([
                            'qty' => $matMstData->qty + $v['retur_qty_pass'],
                            'qty_IN' => $matMstData->qty_OUT + $v['retur_qty_pass']
                        ]);
                    }
                }

                if (isset($v["retur_qty_fail"])) {
                    if ($v["retur_qty_fail"] > 0) {
                        DB::table('retur_qa')->where('id', $v['id'])->update(['status' => 'x']);
                    }
                }
            }
            return 'success';
        }
    }

    public function render()
    {
        return view('livewire.retur-proses-assy');
    }
}
