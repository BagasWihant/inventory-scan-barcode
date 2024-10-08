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
        $dateRange = [$this->dateStart, $this->dateEnd];

        $subQuery = DB::table(function ($query) use ($dateRange) {
            $query->select(DB::raw('SUM(qty_mc) AS Qty'), 'part_number')
                ->from('siws_materialrequest.dbo.dtl_transaction')
                ->whereBetween(DB::raw('CONVERT(date, transaction_date)'), $dateRange)
                ->when($this->searchMat, function ($q) {
                    $q->where('part_number', $this->searchMat);
                })
                ->groupBy('part_number')
                ->unionAll(
                    DB::table('Setup_dtl AS c')
                        ->select(DB::raw('SUM(qty) AS Qty'), DB::raw('c.material_no AS part_number'))
                        ->leftJoin('Setup_mst AS b', 'b.id', '=', 'c.setup_id')
                        ->whereNotNull('b.finished_at')
                        ->when($this->searchMat, function ($q) {
                            $q->where('c.material_no', $this->searchMat);
                        })
                        ->whereBetween(DB::raw('CONVERT(date, c.created_at)'), $dateRange)
                        ->groupBy('c.material_no')
                );
        },'te')->select(DB::raw('SUM(Qty) AS qty'), 'part_number')->groupBy('part_number');

        $query =  DB::table('material_in_stock AS mis')
            ->select(
                'mis.material_no',
                // DB::raw('MIN(mis.created_at) as tgl'),
                DB::raw('SUM(mis.picking_qty) AS qty_in'),
                DB::raw('COALESCE(SUM(qo.qty), 0) AS qty_out'),
                DB::raw('(SUM(mis.picking_qty) - COALESCE ( SUM ( qo.qty ), 0 )) as qty_balance'),
                DB::raw('sum(mst.qty) as qty_now'),
                'mst.loc_cd'
            )
            ->leftJoinSub($subQuery, 'qo', function ($join) {
                $join->on('mis.material_no', '=', 'qo.part_number');
            })
            ->leftJoin('material_mst as mst', 'mis.material_no', 'mst.matl_no')
            ->whereRaw('convert(date,mis.created_at) between ? and ?', $dateRange)
            ->when($this->searchMat, function ($q) {
                $q->where('mis.material_no', $this->searchMat);
            })
            ->where('mst.loc_cd', '!=', 'ASSY')
            ->groupBy('mis.material_no', 'mst.loc_cd')
            ->orderBy('mis.material_no');
        $listData = [];
        if ($this->dateStart && $this->dateEnd && $this->resetBtn) {
            $listData = $query->paginate(20);
        }


        return view('livewire.material-available', compact('listData'));
    }

    private function getQuantityOutSubquery()
    {
        return "
            (SELECT SUM(te.Qty) AS qty, te.part_number
            FROM (
                SELECT SUM(qty_mc) AS Qty, part_number
                FROM siws_materialrequest.dbo.dtl_transaction
                WHERE " . $this->filterTanggal('transaction_date') . $this->filterMaterial('part_number') . "
                GROUP BY part_number

                UNION ALL

                SELECT SUM(qty) AS Qty, c.material_no AS part_number 
                FROM Setup_dtl c
                LEFT JOIN Setup_mst b ON b.id = c.setup_id 
                WHERE b.finished_at IS NOT NULL
                    AND " . $this->filterTanggal('c.created_at') . $this->filterMaterial('c.material_no') . "
                GROUP BY c.material_no
            ) te 
            GROUP BY te.part_number
        ) AS qo";
    }

    private function filterTanggal($field)
    {
        return "CONVERT(date, {$field}) BETWEEN ? AND ?";
    }

    private function filterMaterial($field)
    {
        return $this->searchMat ? " AND {$field} = ?" : "";
    }
}
