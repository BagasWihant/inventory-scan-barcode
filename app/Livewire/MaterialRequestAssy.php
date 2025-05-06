<?php

namespace App\Livewire;

use App\Models\MaterialRequestAssy as ModelsMaterialRequestAssy;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class MaterialRequestAssy extends Component
{
    public $type;
    public $requestQty;
    public $materialNo;
    public $searchMaterialNo;
    public $resultSearchMaterial = [];
    public $selectedData = [];
    public $materialRequest = [];
    public $editedUser = false;
    public $userRequest;
    public $userRequestDisable = false;
    public $variablePage = [
        'materialRequestNW' => 0,
        'materialRequestWR' => 0,
        'timeNow' => null,
    ];
    public $totalRequest = [
        'qty' => 0,
        'data' => []
    ];
    public $transactionNo = [
        'nw' => null,
        'wr' => null,
    ];
    public $line_c;
    public $date;
    public $listLine = [];
    public $listMaterialNo = [];

    protected $listeners = ['editQty'];

    private function loadTable()
    {
        $materialRequest = ModelsMaterialRequestAssy::where('status', '-')
            ->where('user_id', auth()->user()->id)
            ->whereIn('transaksi_no', $this->transactionNo)
            ->get();
        $this->materialRequest = $materialRequest;
    }

    private function generateNoTransaksi(): void
    {
        $getConfig = DB::table('WH_config')->select('config', 'value')
            ->whereIn('config', ['materialRequestNW', 'materialRequestWR', 'periodRequest'])
            ->get()->keyBy('config');

        $ymd = date('Ymd');
        $this->variablePage['materialRequestWR'] = (int)$getConfig['materialRequestWR']->value + 1;
        $this->variablePage['materialRequestNW'] = (int)$getConfig['materialRequestNW']->value + 1;

        if ($getConfig['periodRequest']->value != $ymd) {
            $this->variablePage['materialRequestNW'] = 1;
            $this->variablePage['materialRequestWR'] = 1;
            DB::table('WH_config')->where('config', 'periodRequest')->update(['value' => $ymd]);
        }
        DB::table('WH_config')->where('config', 'materialRequestNW')->update(['value' => $this->variablePage['materialRequestNW']]);
        DB::table('WH_config')->where('config', 'materialRequestWR')->update(['value' => $this->variablePage['materialRequestWR']]);

        $this->transactionNo['wr'] = "WR$ymd-" . str_pad($this->variablePage['materialRequestWR'], 4, '0', STR_PAD_LEFT);
        $this->transactionNo['nw'] = "NW$ymd-" . str_pad($this->variablePage['materialRequestNW'], 4, '0', STR_PAD_LEFT);
    }

    private function getListLine($date)
    {
        $listLine = DB::table('material_setup_mst')->select('line_c')
            ->whereRaw('CONVERT(DATE, plan_issue_dt_from) = ?', [$date])
            ->distinct();

        return $listLine->get();
    }

    public function mount()
    {
        $this->loadTable();
        $this->streamTableSum();
        $this->generateNoTransaksi();
        $this->variablePage['timeNow'] = Carbon::now()->format('Y-m-d H:i:s');
        $date = Carbon::now()->format('Y-m-d');
        $this->listLine = $this->getListLine($date);
    }


    public function dateDebounce($date)
    {
        return $this->getListLine($date);
    }

    public function selectedDataDebounce()
    {
        $decode = json_decode($this->searchMaterialNo, true);
        $this->selectedData = $decode;
        $this->materialNo = $this->selectedData['material_no'];
    }

    public function lineChange($line_c, $date)
    {
        $materialSudah = false;
        $this->line_c = $line_c;
        $this->date = $date;
        // cek data di request assy 
        $yangSudahRequest = DB::table('material_request_assy')->where('user_id', auth()->user()->id)
            ->where('line_c', $line_c)
            ->where('issue_date', $date)
            ->select('material_no', 'sisa_request_qty')->get();
        if ($yangSudahRequest->isEmpty()) {
            // Tampilkan semua material
            $materialList = DB::table('material_setup_mst as s')
                ->join('material_in_stock as mis', function ($join) {
                    $join->on('s.material_no', '=', 'mis.material_no')
                        ->on('s.kit_no', '=', 'mis.kit_no')
                        ->on('s.line_c', '=', 'mis.line_c');
                })
                ->join('material_mst as m', 's.material_no', '=', 'm.matl_no')
                ->where('s.line_c', $line_c)
                ->where(DB::raw("CONVERT(DATE, s.plan_issue_dt_from)"), $date)
                ->selectRaw('s.material_no, m.matl_nm as material_name, sum(mis.picking_qty) as request_qty,sum(mis.picking_qty) as request_qty_ori , s.kit_no, m.qty as qty_stock, m.bag_qty')
                ->groupByRaw('s.material_no, m.iss_unit, m.iss_min_lot, m.matl_nm, s.kit_no, m.qty, m.bag_qty');

            $this->materialRequest = $materialList->get();
        } else {

            $materialNoLebihDariNol = $yangSudahRequest->filter(function ($item) {
                return $item->sisa_request_qty > 0;
            })->pluck('material_no')->toArray();

            if (empty($materialNoLebihDariNol)) {
                $materialSudah = true;
                $this->materialRequest = [];
            } else {
                $materialSudah = true;
                $materialListSql = DB::table('material_setup_mst as s')

                    ->join('material_mst as m', 's.material_no', '=', 'm.matl_no')
                    ->join('material_request_assy as mr', function ($join) {
                        $join->on('s.material_no', '=', 'mr.material_no')
                            ->on('s.line_c', '=', 'mr.line_c')
                            ->on(DB::raw("CONVERT(DATE, mr.issue_date)"), '=', 'mr.issue_date');
                    })
                    ->where('s.line_c', $line_c)
                    ->where(DB::raw("CONVERT(DATE, s.plan_issue_dt_from)"), $date)
                    ->where('sisa_request_qty', '>', 0)
                    ->whereIn('s.material_no', $materialNoLebihDariNol)
                    ->selectRaw('s.material_no, m.matl_nm as material_name,mr.sisa_request_qty as request_qty, mr.sisa_request_qty as request_qty_ori, s.kit_no, m.qty as qty_stock, m.bag_qty ')
                    ->groupByRaw('s.material_no, m.iss_unit, m.iss_min_lot, m.matl_nm, s.kit_no, m.qty, m.bag_qty, mr.sisa_request_qty');

                $materialList = DB::table('material_setup_mst as s')
                    ->join('material_in_stock as mis', function ($join) {
                        $join->on('s.material_no', '=', 'mis.material_no')
                            ->on('s.kit_no', '=', 'mis.kit_no')
                            ->on('s.line_c', '=', 'mis.line_c');
                    })
                    ->join('material_mst as m', 's.material_no', '=', 'm.matl_no')
                    ->where('s.line_c', $line_c)
                    ->where(DB::raw("CONVERT(DATE, s.plan_issue_dt_from)"), $date)
                    ->whereNotIn('s.material_no', $materialNoLebihDariNol)

                    ->selectRaw('s.material_no, m.matl_nm as material_name, sum(mis.picking_qty) as request_qty,sum(mis.picking_qty) as request_qty_ori , s.kit_no, m.qty as qty_stock, m.bag_qty')
                    ->groupByRaw('s.material_no, m.iss_unit, m.iss_min_lot, m.matl_nm, s.kit_no, m.qty, m.bag_qty');

                $data1 = $materialList->get();
                $data2 = $materialListSql->get();
                $mergedData = $data2->merge($data1);
                $this->materialRequest = $mergedData;
            }
        }

        if (count($this->materialRequest) == 0 && $materialSudah) {
            $this->dispatch('alert', ['time' => 2500, 'icon' => 'info', 'title' => 'Material kosong / sudah di request']);
        }
        $this->dispatch('materialsUpdated', $this->materialRequest);
        return $this->materialRequest;
    }

    public function resetField()
    {
        $this->materialRequest = [];
        $this->type = null;
        $this->dispatch('materialsUpdated', $this->materialRequest);
    }

    public function saveRequest()
    {
        $requestQty = $this->selectedData['req_qty'];
        // if (!$this->userRequest) {
        //     $this->dispatch('alert', ['time' => 2500, 'icon' => 'warning', 'title' => 'Please fill User Request']);
        //     return false;
        // }        
        if ($this->selectedData['qty'] < $requestQty) {
            $this->dispatch('alert', ['time' => 2500, 'icon' => 'error', 'title' => 'Maksimal qty : ' . $this->selectedData['qty']]);
            return false;
        }
        if ($requestQty % $this->selectedData['iss_min_lot'] !== 0) {
            $this->dispatch('alert', ['time' => 2500, 'icon' => 'error', 'title' => 'Request Qty bukan kelipatan dari Min. Lot']);
            return  false;
        }

        $transaksiNoItem = (preg_match("/[a-z]/i", $this->materialNo)) ? $this->transactionNo['wr'] : $this->transactionNo['nw'];

        $cekExist = ModelsMaterialRequestAssy::where('transaksi_no', $transaksiNoItem)
            ->where('material_no', $this->materialNo)->exists();

        if ($cekExist) {
            $this->dispatch('alert', ['time' => 2500, 'icon' => 'error', 'title' => 'Material sudah di input']);
            $this->resetField();
            return false;
        }

        if (count($this->selectedData) > 1 && $this->materialNo != null) {
            ModelsMaterialRequestAssy::create([
                'transaksi_no' => $transaksiNoItem,
                'material_no' => $this->materialNo,
                'material_name' => $this->selectedData['matl_nm'],
                'type' => '-',
                'request_qty' => $requestQty,
                'bag_qty' => $this->selectedData['bag_qty'],
                'issue_date' => $this->date,
                'line_c' => $this->line_c,

                'iss_min_lot' => $this->selectedData['iss_min_lot'],
                'iss_unit' => $this->selectedData['iss_unit'],
                'user_id' => auth()->user()->id,
                'status' => '-',
                'user_request' => $this->userRequest
            ]);
            $this->dispatch('alert', ['time' => 2500, 'icon' => 'success', 'title' => 'Request Material Berhasil']);
            $this->userRequestDisable = true;
            $this->loadTable();
            $this->resetField();
            return true;
        }
    }

    public function deleteItem($id)
    {
        ModelsMaterialRequestAssy::where('id', $id)->delete();
        $this->loadTable();
        return $this->dispatch('alert', ['time' => 2500, 'icon' => 'success', 'title' => 'Material Telah di Hapus']);
    }

    public function getMaterialData()
    {
        return $this->materialRequest;
    }


    public function submitRequest($data, $type)
    {
        $materialNos = array_column($data, 'material_no');


        foreach ($data as $value) {
            if (($value['request_qty'] > 0 && $type == '1') || (isset($value['request_qty_new']) && $type == '2')) {

                $transaksiNoItem = (preg_match("/[a-z]/i", $value['material_no'])) ? $this->transactionNo['wr'] : $this->transactionNo['nw'];

                $sisa = 0;
                $req_qty = $value['request_qty'];

                if ($type == '2') {
                    if (isset($value['request_qty_new'])) {
                        $sisa = $value['request_qty_ori'] - $value['request_qty_new'];
                        $req_qty = $value['request_qty_new'];

                        // untuk skip jika edit tapi di isi 0
                        if ($value['request_qty_new'] < 1) {
                            continue;
                        }
                        ModelsMaterialRequestAssy::where('user_id', auth()->user()->id)
                            ->where('line_c', $this->line_c)
                            ->where('issue_date', $this->date)
                            ->where('material_no', $value['material_no'])
                            ->update(['sisa_request_qty' => 0]);
                    }
                } else if ($type == '1') {
                    ModelsMaterialRequestAssy::where('user_id', auth()->user()->id)
                        ->where('line_c', $this->line_c)
                        ->where('issue_date', $this->date)
                        ->where('material_no', $value['material_no'])
                        ->update(['sisa_request_qty' => 0]);
                }
                ModelsMaterialRequestAssy::create([
                    'transaksi_no' => $transaksiNoItem,
                    'material_no' => $value['material_no'],
                    'material_name' => $value['material_name'],
                    'type' => '.',
                    'request_qty' => $req_qty,
                    'bag_qty' => $value['bag_qty'],
                    'issue_date' => $this->date,
                    'line_c' => $this->line_c,
                    'user_id' => auth()->user()->id,
                    'status' => '0',
                    'user_request' => $this->userRequest,
                    'sisa_request_qty' => $sisa,
                ]);
            }
        }

        // reset tabel
        $this->materialRequest = [];
        $this->dispatch('materialsUpdated', $this->materialRequest);

        $this->dispatch('alert', ['time' => 2500, 'icon' => 'success', 'title' => 'Request Material Berhasil']);
        $this->generateNoTransaksi();
    }

    public function streamTableSum()
    {
        $dataGroup = ModelsMaterialRequestAssy::whereIn('status', ['0', '9'])->groupBy(['transaksi_no', 'created_at'])
            ->select(['transaksi_no', DB::raw('count(transaksi_no) as count'), 'created_at'])->orderBy('created_at')->get();

        $now = Carbon::now();
        $totalQty = 0;
        foreach ($dataGroup as $value) {
            $start = Carbon::parse($value->created_at);
            $value->time_request = $start->diffInMinutes($now);
            $totalQty += $value->count;
        }

        $this->totalRequest['qty'] = $totalQty;
        $this->totalRequest['data'] = $dataGroup;
    }

    public function cancelRequest()
    {
        ModelsMaterialRequestAssy::whereIn('transaksi_no', [$this->transactionNo['wr'], $this->transactionNo['nw']])->where('status', '-')->delete();
        $this->loadTable();
        $this->userRequestDisable = false;
        $this->userRequest = null;
        $this->resetField();
    }

    public function cancelTransaksi($transaksiNo)
    {
        ModelsMaterialRequestAssy::where('transaksi_no', $transaksiNo)->delete();
        $this->streamTableSum();
    }

    public function render()
    {
        return view('livewire.material-request-assy');
    }
}
