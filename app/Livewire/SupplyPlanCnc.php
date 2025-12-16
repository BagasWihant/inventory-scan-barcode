<?php

namespace App\Livewire;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithPagination;

class SupplyPlanCnc extends Component
{
    use WithPagination;

    public $materialNo = null;
    public $datestart  = null;
    public $dateend    = null;
    public $dataJson   = [];
    public $tanggal    = [];
    public $today;
    public $currentPage = 1;
    public $lastPage    = 1;
    public $perPage     = 5;

    public function mount()
    {
        $this->today = date('Y-m-d');
    }

    public function searchMaterial($key)
    {
        return DB::table('material_mst')
                   ->selectRaw('matl_no,id')
                   ->where('matl_no', 'like', '%' . $key . '%')
                   ->limit(4)
                   ->get();
    }

    public function selectMaterial($data)
    {
        $this->materialNo = $data['matl_no'];

        $this->dispatch(
            'data-load',
            $data['matl_no']
        );
    }

    public function showData($start, $end)
    {
        $this->datestart   = $start;
        $this->dateend     = $end;
        $this->currentPage = 1;
        $this->loadData();
    }

    public function loadData()
    {
        $endSql = date('Y-m-d', strtotime($this->dateend) + 1 * 24 * 60 * 60);
        $limit  = $this->materialNo ? 1 : $this->perPage;

        $paginator = DB::table('material_mst')
                         ->select('matl_no')
                         ->when($this->materialNo, function ($query) {
                             $query->where('matl_no', $this->materialNo);
                         })
                         ->whereNotNull('loc_cd')
                         ->where('loc_cd', '!=', '')
                         ->paginate(perPage: $limit, columns: ['*'], page: $this->currentPage);

        $master_material = $paginator->getCollection()->pluck('matl_no')->map(function ($v) {
            return trim($v);
        })->all();

        $this->lastPage = $paginator->lastPage();

        if (empty($master_material)) {
            $this->dataJson  = [];
            $this->tanggal   = [];
            return;
        }

        $bom_wip = DB::table('master_wip as w')
                       ->selectRaw("REPLACE(CONCAT(b.product_no,'_',b.dc), ' ', '') AS p_no,
                TRIM(REPLACE(b.material_no, ' ', '')) as material_no,
                b.product_no, b.dc, b.bom_qty, w.qty, w.tanggal, (w.qty * b.bom_qty) as final_qty")
                       ->leftJoin('db_bantu.dbo.bom as b', function ($join) {
                           $join->on('w.model', '=', 'b.product_no')
                                ->on('w.dc', '=', 'b.dc');
                       })
                       ->whereIn('b.material_no', $master_material)
                       ->get();

        $receiving = DB::table('material_in_stock')
                         ->whereBetween('created_at', [$this->datestart, $endSql])
                         ->whereIn(DB::raw("REPLACE(material_no, ' ', '')"), $master_material)
                         ->get();

        $supply = DB::table('setup_dtl')
                      ->whereBetween('created_at', [$this->datestart, $endSql])
                      ->whereIn(DB::raw("REPLACE(material_no, ' ', '')"), $master_material)
                      ->get();

        $dates = [];
        for ($date = Carbon::parse($this->datestart); $date->lte($this->dateend); $date->addDay()) {
            $dates[] = $date->format('Y-m-d');
        }
        $this->tanggal = $dates;

        $finalData = [];
        foreach ($master_material as $m) {
            $finalData[$m] = [
                'material_no' => $m,
                'product_no'  => '',
                'dc'          => '',
                'qty_wip'     => 0,
                'tanggal'     => []
            ];

            foreach ($dates as $d) {
                $finalData[$m]['tanggal'][$d] = [
                    'receiving' => null,
                    'supply'    => null,
                    'wip'       => null,
                    'stock_cnc' => null,
                    'stock_mc'  => null
                ];
            }
        }

        foreach ($bom_wip as $row) {
            $mat = $row->material_no ?? '';
            if (isset($finalData[$mat])) {
                $finalData[$mat]['product_no']                    = $row->product_no ?? '';
                $finalData[$mat]['dc']                            = $row->dc ?? '';
                $finalData[$mat]['qty_wip']                       = $row->qty ?? 0;
                $finalData[$mat]['tanggal'][$row->tanggal]['wip'] = (array) $row;
            }
        }

        foreach ($receiving as $r) {
            $date = Carbon::parse($r->created_at)->format('Y-m-d');
            $mat  = str_replace(' ', '', $r->material_no);
            if (isset($finalData[$mat]['tanggal'][$date])) {
                $finalData[$mat]['tanggal'][$date]['receiving'] = $r;
            }
        }

        foreach ($supply as $s) {
            $date = Carbon::parse($s->created_at)->format('Y-m-d');
            $mat  = str_replace(' ', '', $s->material_no);
            if (isset($finalData[$mat]['tanggal'][$date])) {
                $finalData[$mat]['tanggal'][$date]['supply'] = $s;
            }
        }

        foreach ($finalData as $materialNo => &$item) {
            foreach ($dates as $index => $currentDate) {
                $item['tanggal'][$currentDate]['stock_cnc'] = $this->hitungStokCNC($item, $currentDate, $index, $dates);

                $item['tanggal'][$currentDate]['stock_mc'] = $this->hitungStokMC($item, $currentDate, $index, $dates);
            }
        }

        $this->dataJson  = $finalData;
    }

    private function hitungStokCNC($item, $currentDate, $index, $dates)
    {
        $dataToday = $item['tanggal'][$currentDate] ?? [];

        if ($index === 0) {
            $supply = $dataToday['supply']->qty ?? 0;
            $wip    = $dataToday['wip']['final_qty'] ?? 0;
            return $supply - $wip;
        }

        $kemarin          = $dates[$index - 1];
        $dataKemarin      = $item['tanggal'][$kemarin] ?? [];
        $stok_cnc_kemarin = $dataKemarin['stock_cnc'] ?? 0;

        $supply = $dataToday['supply']->qty ?? 0;
        $wip    = $dataToday['wip']['final_qty'] ?? 0;

        return $stok_cnc_kemarin + $supply - $wip;
    }

    private function hitungStokMC($item, $currentDate, $index, $dates)
    {
        $dataToday = $item['tanggal'][$currentDate] ?? [];

        if ($index === 0) {
            $recvqty = $dataToday['receiving']->picking_qty ?? 0;
            $supqty  = $dataToday['supply']->qty ?? 0;
            return $recvqty - $supqty;
        }

        $kemarin         = $dates[$index - 1];
        $dataKemarin     = $item['tanggal'][$kemarin] ?? [];
        $stok_mc_kemarin = $dataKemarin['stock_mc'] ?? 0;

        $recvqty = $dataToday['receiving']->picking_qty ?? 0;
        $supqty  = $dataToday['supply']->qty ?? 0;

        return $stok_mc_kemarin + $recvqty - $supqty;
    }

    public function nextPage()
    {
        if ($this->currentPage < $this->lastPage) {
            $this->currentPage++;
            $this->loadData();
        }
    }

    public function prevPage()
    {
        if ($this->currentPage > 1) {
            $this->currentPage--;
            $this->loadData();
        }
    }

    public function gotoLastPage()
    {
        $this->currentPage = $this->lastPage;
        $this->loadData();
    }

    public function resetData()
    {
        $this->reset(['materialNo', 'datestart', 'dateend', 'dataJson', 'currentPage']);
        $this->lastPage = 1;
    }

    public function render()
    {
        return view('livewire.supply-plan-cnc');
    }
}
