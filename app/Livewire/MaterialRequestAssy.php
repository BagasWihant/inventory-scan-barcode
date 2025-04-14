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

    private function getListLine()
    {
        $listLine = DB::table('material_setup_mst_supplier')->select('line_c')
            ->whereRaw('CONVERT(DATE, plan_issue_dt_from) = ?', [$this->date])
            ->distinct();

        return $listLine->get();
    }

    public function editQty($qty, $id)
    {

        $data = ModelsMaterialRequestAssy::find($id);
        $data->update([
            'request_qty' => $qty
        ]);
        $this->loadTable();
    }

    public function mount()
    {
        $this->loadTable();
        $this->streamTableSum();
        $this->generateNoTransaksi();
        $this->variablePage['timeNow'] = Carbon::now()->format('Y-m-d H:i:s');
        $this->date = Carbon::now()->format('Y-m-d');
        $this->listLine = $this->getListLine();
    }

    public function updated($prop, $val)
    {
        // dd($prop,$val);
        // switch ($prop) {
        //     case 'materialNo':

        //         break;

        //     case 'selectedData':
        //         $this->searchMaterialNo = false;
        //         $this->selectedData = $val;
        //         $this->materialNo = $this->selectedData['material_no'];
        //         break;
        //     case 'date':
        //         break;
        //     default:
        //         # code...
        //         break;
        // }
    }

    public function dateDebounce()
    {
        $this->listLine = $this->getListLine();
    }

    public function selectedDataDebounce()
    {
        $decode = json_decode($this->searchMaterialNo, true);
        $this->selectedData = $decode;
        $this->materialNo = $this->selectedData['material_no'];
    }

    public function lineChange()
    {
        $materiallist = DB::table('material_setup_mst_supplier as s')
            ->join('material_in_stock as mis', function ($join) {
                $join->on('s.material_no', '=', 'mis.material_no')
                    ->on('s.kit_no', '=', 'mis.kit_no')
                    ->on('s.line_c', '=', 'mis.line_c');
            })
            ->join('material_mst as m', 's.material_no', '=', 'm.matl_no')
            ->where('s.line_c', $this->line_c)
            ->where(DB::raw("CONVERT(DATE, s.plan_issue_dt_from)"), $this->date)
            ->selectRaw('s.material_no, m.iss_unit, m.iss_min_lot, m.matl_nm, sum(mis.picking_qty) as req_qty, s.kit_no,m.qty,m.bag_qty')
            ->groupByRaw('s.material_no, m.iss_unit, m.iss_min_lot, m.matl_nm, s.kit_no, m.qty, m.bag_qty')
            ->get();
        foreach ($materiallist as $items) {

            $transaksiNoItem = (preg_match("/[a-z]/i", $items->material_no)) ? $this->transactionNo['wr'] : $this->transactionNo['nw'];
            ModelsMaterialRequestAssy::create([
                'transaksi_no' => $transaksiNoItem,
                'material_no' => $items->material_no,
                'material_name' => $items->matl_nm,
                'type' => $this->type,
                'request_qty' => $items->req_qty,
                'bag_qty' => $items->bag_qty,
                'issue_date' => $this->date,
                'line_c' => $this->line_c,

                'iss_min_lot' => $items->iss_min_lot,
                'iss_unit' => $items->iss_unit,
                'user_id' => auth()->user()->id,
                'status' => '-',
                'user_request' => $this->userRequest
            ]);
        }

        $this->loadTable();

        // dd($materiallist);
        // $this->selectedData = [];

        // $this->listMaterialNo = $materiallist;
    }

    public function resetField()
    {
        $this->searchMaterialNo = null;
        $this->selectedData = [];
        $this->materialNo = null;
        $this->requestQty = null;
    }

    public function saveRequest()
    {
        $requestQty = $this->selectedData['req_qty'];
        // if (!$this->userRequest) {
        //     $this->dispatch('alert', ['time' => 2500, 'icon' => 'warning', 'title' => 'Please fill User Request']);
        //     return false;
        // }
        if (!$this->type) {
            $this->dispatch('alert', ['time' => 2500, 'icon' => 'warning', 'title' => 'Please choose Type']);
            return false;
        }
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
                'type' => '.',
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



    public function submitRequest()
    {
        // $userRequstIsNull = ModelsMaterialRequestAssy::whereNull('user_request')
        //     ->whereIn('transaksi_no', [$this->transactionNo['wr'], $this->transactionNo['nw']])->exists();
        // if ($userRequstIsNull) {
        //     return $this->dispatch('alert', ['time' => 2500, 'icon' => 'error', 'title' => 'Please fill all User Request ']);
        // }
        $updateStatus = ModelsMaterialRequestAssy::whereIn('transaksi_no', [$this->transactionNo['wr'], $this->transactionNo['nw']]);

        if (!$updateStatus->exists()) {
            return $this->dispatch('alert', ['time' => 2500, 'icon' => 'error', 'title' => 'Submit Failed No. Transaksi berbeda (beda hari)']);
        }

        $updateStatus->update([
            'status' => 0,
            'user_request' => $this->userRequest,
            'created_at' => Carbon::now(),
        ]);

        $this->streamTableSum();
        $this->loadTable();
        $this->userRequestDisable = false;
        $this->userRequest = null;
        $this->resetField();
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
