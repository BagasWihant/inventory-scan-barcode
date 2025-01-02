<?php

namespace App\Livewire;

use App\Models\MaterialRequest as ModelsMaterialRequest;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class MaterialRequest extends Component
{
    public $type;
    public $requestQty;
    public $materialNo;
    public $searchMaterialNo = false;
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

    private function loadTable()
    {
        $materialRequest = ModelsMaterialRequest::where('status', '-')->get();
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

        $this->transactionNo['wr'] = "WR$ymd-" . str_pad($this->variablePage['materialRequestWR'], 4, '0', STR_PAD_LEFT);
        $this->transactionNo['nw'] = "NW$ymd-" . str_pad($this->variablePage['materialRequestNW'], 4, '0', STR_PAD_LEFT);
    }

    public function mount()
    {
        $this->loadTable();
        $this->streamTableSum();
        $this->generateNoTransaksi();
        $this->variablePage['timeNow'] = Carbon::now()->format('Y-m-d H:i:s');
    }

    public function updated($prop, $val)
    {
        switch ($prop) {
            case 'materialNo':
                DB::enableQueryLog();
                $qrySearch = DB::table('material_mst')
                    // ->where('matl_no', 'like', "%$val%")
                    ->whereRaw("REPLACE(matl_no, ' ', '') LIKE ?", ["%$val%"])
                    ->select(['matl_no', 'iss_unit', 'bag_qty', 'iss_min_lot', 'matl_nm', 'qty'])->limit(10)->get();
                $countSearch = count($qrySearch);

                if ($countSearch > 0) {
                    $this->searchMaterialNo = true;
                    $this->resultSearchMaterial = $qrySearch;
                } else {
                    $this->resultSearchMaterial = [];
                }

                break;

            case 'selectedData':
                $this->searchMaterialNo = false;
                $this->selectedData = $val;
                $this->materialNo = $this->selectedData['matl_no'];
                break;
            default:
                # code...
                break;
        }
    }

    public function resetField()
    {
        $this->searchMaterialNo = false;
        $this->selectedData = [];
        $this->materialNo = null;
        $this->requestQty = null;
    }

    public function saveRequest()
    {
        if (!$this->userRequest) {
            return $this->dispatch('alert', ['time' => 2500, 'icon' => 'warning', 'title' => 'Please fill User Request']);
        }
        if (!$this->type) {
            return $this->dispatch('alert', ['time' => 2500, 'icon' => 'warning', 'title' => 'Please choose Type']);
        }
        if ($this->selectedData['qty'] < $this->requestQty) {
            return $this->dispatch('alert', ['time' => 2500, 'icon' => 'error', 'title' => 'Maksimal qty : ' . $this->selectedData['qty']]);
        }

        if (count($this->selectedData) > 1 && $this->materialNo != null) {
            ModelsMaterialRequest::create([
                'transaksi_no' => (preg_match("/[a-z]/i", $this->materialNo)) ? $this->transactionNo['wr'] : $this->transactionNo['nw'],
                'material_no' => $this->materialNo,
                'material_name' => $this->selectedData['matl_nm'],
                'type' => $this->type,
                'request_qty' => $this->requestQty,
                'bag_qty' => $this->selectedData['bag_qty'],
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
        }
    }

    public function deleteItem($id)
    {
        ModelsMaterialRequest::where('id', $id)->delete();
        $this->loadTable();
        return $this->dispatch('alert', ['time' => 2500, 'icon' => 'success', 'title' => 'Material Telah di Hapus']);
    }

   

    public function submitRequest()
    {
        $userRequstIsNull = ModelsMaterialRequest::whereNull('user_request')
            ->whereIn('transaksi_no', [$this->transactionNo['wr'], $this->transactionNo['nw']])->exists();
        if ($userRequstIsNull) {
            return $this->dispatch('alert', ['time' => 2500, 'icon' => 'error', 'title' => 'Please fill all User Request ']);
        }
        $updateStatus = ModelsMaterialRequest::whereIn('transaksi_no', [$this->transactionNo['wr'], $this->transactionNo['nw']]);

        if (!$updateStatus->exists()) {
            return $this->dispatch('alert', ['time' => 2500, 'icon' => 'error', 'title' => 'Submit Failed No. Transaksi berbeda (beda hari)']);
        }

        $updateStatus->update([
            'status' => 0,
            'user_request' => $this->userRequest,
            'created_at' => Carbon::now(),
        ]);

        DB::table('WH_config')->where('config', 'materialRequestNW')->update(['value' => $this->variablePage['materialRequestNW']]);
        DB::table('WH_config')->where('config', 'materialRequestWR')->update(['value' => $this->variablePage['materialRequestWR']]);

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
        $dataGroup = ModelsMaterialRequest::where('status', '0')->groupBy(['transaksi_no', 'created_at'])
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
        ModelsMaterialRequest::whereIn('transaksi_no', [$this->transactionNo['wr'], $this->transactionNo['nw']])->where('status', '-')->delete();
        $this->loadTable();
        $this->userRequestDisable = false;
        $this->userRequest = null;
        $this->resetField();
    }

    public function render()
    {
        return view('livewire.material-request');
    }
}
