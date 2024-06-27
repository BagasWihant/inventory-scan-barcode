<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\MenuOptions;
use Illuminate\Support\Facades\DB;

class ResultStockTaking extends Component
{
    public $searchKey;
    public function render()
    {
        // GET STO ACTIVE
        $sto = MenuOptions::select('id')->where('status', '1')
            ->where('user_id', auth()->user()->id)->first();
        $query = DB::table('stock_takings')
            ->selectRaw('material_no,loc,sum(qty) as qty,hitung')
            ->where('sto_id', $sto->id)
            ->groupBy(['material_no', 'hitung', 'loc'])->orderByRaw('material_no ASC, hitung ASC');
        if ($this->searchKey) $query->where('material_no', 'like', "%$this->searchKey%");
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
                // if($hit))
                // dump($it);
            }
            return [$key => $tempArray];
        })->all();

        foreach ($inStock as $value) {
            if (isset($listData[$value->material_no])) {
                $listData[$value->material_no]['locsys'] = $value->locate;
                $listData[$value->material_no]['qtysys'] = $value->qty;

                $qty = $listData[$value->material_no]['qty3'] ??
                    $listData[$value->material_no]['qty2'] ??
                    $listData[$value->material_no]['qty1'] ?? 0;

                $res = $qty - $listData[$value->material_no]['qtysys'];

                if ($res != 0) {
                    $listData[$value->material_no][$res > 0 ? 'plus' : 'min'] = abs($res);
                }

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
