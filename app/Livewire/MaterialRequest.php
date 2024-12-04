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

    private function loadTable($table = 'all')
    {
        $materialRequest = ModelsMaterialRequest::where('status', '-')->get();
        if ($table == 'tableItem') {
            $this->materialRequest = $materialRequest;
            return;
        }

        $dataGroup = ModelsMaterialRequest::where('status', 0)->groupBy(['transaksi_no', 'created_at'])
            ->select(['transaksi_no', DB::raw('count(transaksi_no) as count'), 'created_at'])->orderBy('created_at')->get();

        $now = Carbon::now();
        foreach ($dataGroup as $value) {
            $start = Carbon::parse($value->created_at);
            $value->time_request = $start->diffInMinutes($now);
        }

        if ($table == 'tableSum') {
            $this->totalRequest['data'] = $dataGroup;
        }
        if ($table == 'all') {
            $this->materialRequest = $materialRequest;
            $this->totalRequest['data'] = $dataGroup;
        }
    }

    public function mount()
    {
        $this->loadTable();
        $this->variablePage['timeNow'] = Carbon::now()->format('Y-m-d H:i:s');
        // GENERATE Number
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

    public function updated($prop, $val)
    {
        switch ($prop) {
            case 'materialNo':
                $qrySearch = DB::table('material_mst')->where('matl_no', 'like', "%$val%")->select(['matl_no', 'iss_unit', 'bag_qty', 'iss_min_lot', 'matl_nm'])->limit(10)->get();
                $countSearch = count($qrySearch);
                if ($countSearch > 1) {
                    $this->searchMaterialNo = true;
                    $this->resultSearchMaterial = $qrySearch;
                }

                break;

            case 'selectedData':
                $this->selectedData = json_decode($val, true);
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
        if (!$this->type) {
            return $this->dispatch('alert', [ 'time' => 2500, 'icon' => 'warning', 'title' => 'Please choose Type']);
        }
        if ($this->searchMaterialNo && $this->materialNo != null) {
            ModelsMaterialRequest::create([
                'transaksi_no' => (preg_match("/[a-z]/i", $this->materialNo)) ? $this->transactionNo['wr'] : $this->transactionNo['nw'],
                'material_no' => $this->materialNo,
                'material_name' => $this->selectedData['matl_nm'],
                'type' => $this->type,
                'request_qty' => $this->requestQty,
                'bag_qty' => $this->selectedData['bag_qty'],
                'iss_min_lot' => $this->selectedData['iss_min_lot'],
                'created_by' => auth()->user()->id,
                'status' => '-',
            ]);

            $this->loadTable('tableItem');
            $this->resetField();
        }
    }

    public function updateUserRequest($id)
    {
        ModelsMaterialRequest::where('id', $id)->update([
            'request_user' => $this->userRequest
        ]);
        $this->loadTable('tableItem');
    }

    public function submitRequest()
    {
        ModelsMaterialRequest::whereIn('transaksi_no', [$this->transactionNo['wr'], $this->transactionNo['nw']])
            ->update([
                'status' => 0,
                'created_at' => Carbon::now(),
            ]);
        DB::table('WH_config')->where('config', 'materialRequestNW')->update(['value' => $this->variablePage['materialRequestNW']]);
        DB::table('WH_config')->where('config', 'materialRequestWR')->update(['value' => $this->variablePage['materialRequestWR']]);

        $this->loadTable();
    }

    public function render()
    {
        $this->loadTable('tableSum');
        return view('livewire.material-request');
    }
}
