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
            ->where('user_id', auth()->user()->id);
        if ($sto->count() > 0) {
            $laststo = collect($sto->first());
        } else {
            $laststo['id'] = '-';
        }
        $query = DB::table('stock_takings')
            ->selectRaw('material_no,loc,sum(qty) as qty,hitung')
            ->where('sto_id', $laststo['id'])
            ->groupBy(['material_no', 'hitung', 'loc'])->orderByRaw('material_no ASC, hitung ASC');
        if ($this->searchKey) $query->where('material_no', 'like', "%$this->searchKey%");
        $data = $query->get();

        $listMat = $data->pluck('material_no')->unique()->all();

        $inStock = DB::table('material_in_stock')
            ->select('material_no', 'locate', DB::raw('sum(picking_qty) as qty'))
            ->whereIn('material_no', $listMat)
            ->groupBy('material_no', 'locate')
            ->get();
        $countInStock = count($inStock);

        $output = collect($data)->groupBy('material_no')->all();

        $listData =  collect($output)->mapWithKeys(function ($item, $key) {
            $tempArray = [];
            foreach ($item as $it) {
                $hit = $it->hitung;
                $tempArray["loc$hit"] = $it->loc;
                $tempArray["qty$hit"] = $it->qty;
                // if($hit))
            }
            return [$key => $tempArray];
        })->all();

        foreach ($listData as $key => $v) {

            if ($countInStock > 0) {
                foreach ($inStock as $value) {
                    if ($key == $value->material_no) {
                        $qty = $listData[$value->material_no]['qty3'] ??
                            $listData[$value->material_no]['qty2'] ??
                            $listData[$value->material_no]['qty1'] ?? 0;

                        $listData[$value->material_no]['locsys'] = $value->locate;
                        $listData[$value->material_no]['qtysys'] = $value->qty;

                        $res = $qty - $listData[$value->material_no]['qtysys'];
                        $listData[$value->material_no]['locsys'] = $value->locate;
                        $listData[$value->material_no]['qtysys'] = $value->qty;

                        if ($res != 0) {
                            $listData[$value->material_no][$res > 0 ? 'plus' : 'min'] = abs($res);
                        }
                    } else {

                        $qty = $listData[$key]['qty3'] ??
                            $listData[$key]['qty2'] ??
                            $listData[$key]['qty1'] ?? 0;

                        $res = $qty - 0;
                        $listData[$key]['locsys'] = "NOT FOUND";
                        $listData[$key]['qtysys'] = 0;

                        if ($res != 0) {
                            $listData[$key][$res > 0 ? 'plus' : 'min'] = abs($res);
                        }
                    }
                }
            } else {

                $qty = $listData[$key]['qty3'] ??
                    $listData[$key]['qty2'] ??
                    $listData[$key]['qty1'] ?? 0;

                $res = $qty - 0;
                $listData[$key]['locsys'] = "NOT FOUND";
                $listData[$key]['qtysys'] = 0;
                if ($res != 0) {
                    $listData[$key][$res > 0 ? 'plus' : 'min'] = abs($res);
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
