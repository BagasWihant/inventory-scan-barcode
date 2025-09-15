<?php

namespace App\Livewire;

use Illuminate\Support\Facades\DB;
use Livewire\Component;

class BomRequest extends Component
{
    protected $product_no;
    protected $dc;

    public function searchLineCode($qey)
    {
        return DB::table('material_setup_mst_supplier')
            ->selectRaw("line_c,line_c AS id")
            ->whereRaw("line_c LIKE ?", ["%{$qey}%"])
            ->groupBy('line_c')
            ->limit(5)
            ->get();
    }

    public function selectLineCode($item)
    {
        $this->dispatch('loading', true);

        $this->dispatch(
            'line-selected',
            data: $item
        );
    }
    public function searchPM($qey)
    {
        return DB::table('db_bantu.dbo.bom')
            ->selectRaw("
            REPLACE(CONCAT(product_no,'_',dc), ' ', '') AS product_no,
            REPLACE(CONCAT(product_no,'_',dc), ' ', '') AS id
        ")
            ->whereRaw("REPLACE(CONCAT(product_no,'_',dc), ' ', '') LIKE ?", ["%{$qey}%"])
            ->groupBy('product_no', 'dc')
            ->limit(5)
            ->get();
    }

    public function selectPM($item)
    {
        $this->dispatch('loading', true);
        $no = explode('_', $item['id']);
        $this->product_no = $no[0];
        $this->dc = $no[1];
        sleep(2);

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

    public function submitData($data)
    {
        foreach ($data as $d) {
            if (isset($d['edited']) && $d['edited'] == 'edited') {
                DB::table('db_bantu.dbo.bom')
                    ->where('product_no', $d['product_no'])
                    ->where('dc', $d['dc'])
                    ->where('material_no', $d['material_no'])
                    ->update([
                        'bom_qty' => $d['bom_qty'],
                        'status_request' => 1
                    ]);
            }
        }

        return [
            'success' => true,
        ];
    }

    public function render()
    {
        return view('livewire.bom-request');
    }
}
