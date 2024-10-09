<?php

namespace App\Livewire;

use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithPagination;

class MaterialAvailable extends Component
{
    use WithPagination;
    public $dateStart, $dateEnd, $searchMat, $matDisable = false, $listMaterial = [];
    public $resetBtn = false;


    public function matChange()
    {
        if (strlen($this->searchMat) >= 3) {
            $this->listMaterial = DB::table('material_in_stock as  mis')->distinct()
                ->select('material_no as material')
                ->leftJoin('material_mst as mst', 'mis.material_no', 'mst.matl_no')
                ->whereNot('mst.loc_cd', "ASSY")
                ->where('material_no', 'like', '%' . $this->searchMat . '%')->limit(15)
                ->get();
        }
    }
    public function chooseMat($val)
    {
        $this->matDisable   = true;
        $this->searchMat = $val;
        $this->listMaterial = [];
    }

    public function resetFilter()
    {
        $this->matDisable   = false;
        $this->searchMat = null;
        $this->listMaterial = [];
        $this->resetBtn = false;
        $this->dateStart = null;
        $this->dateEnd = null;
    }
    public function showData()
    {
        $this->resetBtn = true;
        if (!$this->dateStart && !$this->dateEnd) {
            $this->dateStart = '2024-07-01';
            $this->dateEnd = date('Y-m-d');
        }
    }
    public function render()
    {

        $listData = [];
        $startDate = $this->dateStart;
        $endDate = $this->dateEnd;
        $materialNo = $this->searchMat;

        $result = DB::query()
            ->fromSub(function ($query) use ($startDate, $endDate, $materialNo) {
                $query->from('material_in_stock as mis')
                    ->select(
                        'mis.material_no',
                        DB::raw('SUM(mis.picking_qty) as total_picking_qty'),
                        DB::raw('MIN(CONVERT(DATE, mis.created_at)) as first_created_at')
                    )
                    ->whereBetween(DB::raw('CONVERT(DATE, mis.created_at)'), [$startDate, $endDate])
                    ->when($materialNo, function ($sub) use ($materialNo) {
                     $sub->where( 'mis.material_no', $materialNo); 
                    })
                    ->groupBy('mis.material_no');
            }, 'MaterialInStock')
            ->leftJoinSub(function ($query) use ($startDate, $endDate, $materialNo) {
                $query->fromSub(function ($subQuery) use ($startDate, $endDate, $materialNo) {
                    $subQuery->from('siws_materialrequest.dbo.dtl_transaction')
                        ->select('part_number', DB::raw('SUM(qty_mc) as Qty'))
                        ->whereBetween(DB::raw('CONVERT(DATE, transaction_date)'), [$startDate, $endDate])
                        ->when($materialNo, function ($sub) use ($materialNo) {
                            $sub->where( 'part_number', $materialNo); 
                           })
                        ->groupBy('part_number')
                        ->union(
                            DB::table('Setup_dtl as c')
                                ->leftJoin('Setup_mst as b', 'b.id', '=', 'c.setup_id')
                                ->select('c.material_no as part_number', DB::raw('SUM(c.qty) as Qty'))
                                ->whereNotNull('b.finished_at')
                                ->whereBetween(DB::raw('CONVERT(DATE, c.created_at)'), [$startDate, $endDate])
                                ->when($materialNo, function ($sub) use ($materialNo) {
                                    $sub->where( 'c.material_no', $materialNo); 
                                   })
                                ->groupBy('c.material_no')
                        );
                }, 'combined_qty_out')
                    ->select('part_number', DB::raw('SUM(Qty) as qty'))
                    ->groupBy('part_number');
            }, 'QuantityOut', 'MaterialInStock.material_no', '=', 'QuantityOut.part_number')
            ->leftJoin('material_mst as mst', 'MaterialInStock.material_no', '=', 'mst.matl_no')
            ->select(
                'MaterialInStock.material_no',
                DB::raw('MaterialInStock.total_picking_qty as qty_in'),
                DB::raw('COALESCE(QuantityOut.qty, 0) as qty_out'),
                DB::raw('(MaterialInStock.total_picking_qty - COALESCE(QuantityOut.qty, 0)) as qty_balance'),
                DB::raw('SUM(mst.qty) as qty_now'),
                'mst.loc_cd'
            )
            ->where('mst.loc_cd', '!=', 'ASSY')
            ->groupBy(
                'MaterialInStock.material_no',
                'MaterialInStock.total_picking_qty',
                'QuantityOut.qty',
                'mst.loc_cd'
            )
            ->orderBy('MaterialInStock.material_no');

        if ($this->dateStart && $this->dateEnd && $this->resetBtn) {
            $listData = $result->paginate(20);
        }


        return view('livewire.material-available', compact('listData'));
    }
}
