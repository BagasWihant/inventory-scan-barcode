<?php

namespace App\Livewire;

use Illuminate\Support\Facades\DB;
use Livewire\Component;

class ReceivingRack extends Component
{
    public function searchPalet(string $s): object
    {
        $data = DB::table("material_in_stock")
            ->selectRaw("pallet_no, pallet_no as id")
            ->where("pallet_no", "like", "%$s%")
            ->groupBy("pallet_no")
            ->limit(10)
            ->get();
        return $data;
    }

    public function selectPalet(array $v)
    {
        $data = DB::table("material_in_stock as m")
            ->where("pallet_no", $v['pallet_no'])
            ->selectRaw("material_no,
                (
                SELECT
                    string_AGG ( CAST ( x.supplier_code AS VARCHAR ( 255 ) ), ', ' ) 
                FROM
                    ( SELECT DISTINCT supplier_code FROM material_conversion_mst WHERE sws_code = m.material_no ) AS x 
                ) AS qr,
                s.sws_code,
                sum(picking_qty) AS picking_qty,
                pallet_no,
                count(*) as pack")
            ->leftJoin('material_conversion_mst as s', 'm.material_no', '=', 's.sws_code')
            ->groupByRaw("material_no,  pallet_no, s.sws_code")
            ->get();
        $this->dispatch(
            'data-load',
            data: $data
        );
    }

    public function submitData($data){
        foreach ($data['data'] as $v) {
            if($v['scanned_pack'] < 1) continue;
            
            DB::table('material_in_stock')
            ->where('pallet_no', $v['pallet_no'])
            ->where('material_no', $v['material_no'])
            ->update([
                'is_stored' => $v['scanned_pack'],
                'stored_at' => date('Y-m-d H:i:s'),
                'stored_by' => auth()->user()->id,
            ]);
        }
    }

    public function render()
    {
        return view('livewire.receiving-rack');
    }
}
