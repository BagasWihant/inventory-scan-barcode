<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\DB;

class ResultStockTaking extends Component
{
    public function render()
    {
        $query = DB::table('stock_takings')
            ->selectRaw('material_no,loc,sum(qty) as qty,hitung')
            ->where('user_id', auth()->user()->id)
            ->groupBy(['material_no', 'hitung', 'loc']);
        $data = $query->get();

        $listMat = $data->pluck('material_no')->unique()->all();

        $inStock = DB::table('material_in_stock')
            ->select('material_no', 'locate', DB::raw('sum(picking_qty) as qty'))
            ->whereIn('material_no', $listMat)
            ->groupBy('material_no', 'locate')
            ->get();
        $output = collect($data)->groupBy('material_no')->all();

        $listData =  collect($output)->mapWithKeys(function ($item, $key) {
            $tempArray = [];
            foreach ($item as $it) {
                $hit = $it->hitung;
                $tempArray["loc$hit"] = $it->loc;
                $tempArray["qty$hit"] = $it->qty;
            }
            return [$key => $tempArray];
        })->all();

        foreach ($inStock as $value) {
            if (isset($listData[$value->material_no])) {
                $listData[$value->material_no]['locsys'] = $value->locate;
                $listData[$value->material_no]['qtysys'] = $value->qty;
            }
        }

        return view(
            'livewire.result-stock-taking',
            [
                'data' => $listData
            ]
        );
    }
}
