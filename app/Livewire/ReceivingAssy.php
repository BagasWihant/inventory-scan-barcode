<?php

namespace App\Livewire;

use App\Exports\ItemMaterialRequest;
use App\Models\MaterialInStockAssy;
use App\Models\MaterialRequestAssy;
use App\Models\SetupDtlAssy;
use App\Models\SetupMstAssy;
use App\Models\temp_request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\On;
use Livewire\Component;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Str;

class ReceivingAssy extends Component
{
    // public $searchKey;
    public $materialScan;
    public $transaksiNo;
    public $tempRequest;
    public $materialSelected;
    public $userId;
    public $data;
    public $dada;

    public function mount()
    {
        $this->userId = auth()->user()->id;
        $this->getData();
    }

    private function configAddMaterial()
    {
        $configKey = 'ADD_MAT_BTN_RECEIVE';
        if (auth()->user()->Admin == 1) {
            return 1;
        }

        $row = Cache::rememberForever($configKey, function () use ($configKey) {
            return DB::table('WH_config')->where('config', $configKey)->first();
        });

        if (!empty($row)) {
            $data = json_decode($row->value, true);
            if ($data[$this->userId] == 1) {
                return 1;
            }
            return 0;
        }
    }
    public function print($id)
    {
        $dataPrint = MaterialRequestAssy::where('transaksi_no', $id)
            ->leftJoin('material_mst as b', 'material_request_assy.material_no', '=', 'b.matl_no')
            ->select(['material_request_assy.*', 'b.loc_cd', DB::raw('(b.iss_min_lot/request_qty) as pax')])->orderBy('b.loc_cd', 'asc')->get();
        // MaterialRequestAssy::where('transaksi_no', $id)->update(['status' => '9']);

        // hapus temp request pindah sini
        temp_request::where('transaksi_no', $id)->delete();

        return Excel::download(new ItemMaterialRequest($dataPrint), "Request Material_" . $id . "_" . date('YmdHis') . ".pdf", \Maatwebsite\Excel\Excel::MPDF);
    }

    public function getMaterial($trx)
    {
        $dataPrint = DB::table('material_request_assy as mra')
            ->leftJoin('setup_dtl_assy as sd', function ($join) {
                $join->on('mra.material_no', '=', 'sd.material_no')
                    ->on('mra.transaksi_no', '=', 'sd.pallet_no');
            })
            ->where('mra.transaksi_no', $trx)
            ->selectRaw('mra.transaksi_no,mra.material_no, mra.material_name, mra.request_qty, sd.qty as qty_supply, mra.status, sd.qty as qty_receive, surat_jalan, mra.line_c, mra.issue_date,sd.setup_id')->get();

        $this->transaksiNo = $trx;
        $this->materialScan = null;
        $buttonAdd = $this->configAddMaterial();

        return [$dataPrint, $buttonAdd];
    }

    public function addMaterialDetail($add, $sample)
    {
        MaterialRequestAssy::create([
            'transaksi_no' => $sample[0]['transaksi_no'],
            'material_no' => $add['material_no'],
            'material_name' => '-',
            'type' => '-',
            'request_qty' => 0,
            'bag_qty' => 0,
            'issue_date' => $sample[0]['issue_date'],
            'line_c' => $sample[0]['line_c'],
            'user_id' => auth()->user()->id,
            'status' => '+',
            'penanggung_jwb' => $add['penanggung_jawab'],
        ]);

        DB::table('Setup_dtl_assy')->insert([
            'setup_id' => $add['setup_id'],
            'material_no' => $add['material_no'],
            'qty' => $add['qty_receive'],
            'created_at' => now(),
            'pallet_no' => $sample[0]['transaksi_no'],
        ]);
    }

    public function getData()
    {
        $this->data = MaterialRequestAssy::
            // ->when($this->searchKey, function ($q) {
            //     $q->where('transaksi_no', 'like', '%' . $this->searchKey . '%');
            // })
            selectRaw('transaksi_no, status, type, issue_date, line_c, CONVERT(DATE,created_at) as created_at')
            ->where('status', '1')
            ->groupByRaw('transaksi_no, status, type, CONVERT(DATE,created_at), issue_date, line_c')
            ->orderByDesc(DB::raw('CONVERT(DATE,created_at)'))->get();
    }

    public function searchPenerima($input)
    {
        $data = DB::table('ms_nik')
            ->where('nik', 'like', '%' . $input . '%')
            ->orWhere('nama', 'like', '%' . $input . '%')
            ->select('nama', 'nik')->limit(5)->get()->toArray();

        return $data;
    }

    function searchMaterial($material)
    {
        $list = DB::table('material_mst')
            ->where('matl_no', 'like', '%' . $material . '%')->select('matl_no', 'matl_nm','qty')->limit(5)->get();

        return $list;
    }
    public function saveDetailScanned($data, $penerima)
    {
        try {
            DB::beginTransaction();
            foreach ($data as $item) {
                MaterialInStockAssy::create([
                    'transaksi_no' => $this->transaksiNo,
                    'material_no' => $item['material_no'],
                    'material_name' => $item['material_name'],
                    'qty' => $item['qty_receive'],
                    'issue_date' => $item['issue_date'],
                    'line_c' => $item['line_c'],
                    'user_id' => $this->userId,
                    'surat_jalan' => $item['surat_jalan'],
                    'penerima' => $penerima['nama'],
                ]);

                if ($item["status"] == "+") {
                    // $matMst = DB::table('material_mst')->where('matl_no', $item['material_no']);
                    // $matMstData = $matMst->first();
                    // $matMst->update([
                    //     'qty' => (int) $matMstData->qty - (int) $item['qty_supply'],
                    //     'qty_OUT' => (int)$matMstData->qty_OUT + (int)$item['qty_supply']
                    // ]);
                }
            }

            // qty supply baca dari temp request nek tak delete di receiving assy suplly nya kosong
            // temp_request::where('transaksi_no', $this->transaksiNo)->delete();
            DB::commit();

            MaterialRequestAssy::where('material_request_assy.transaksi_no', $this->transaksiNo)->update([
                'status' => '2',
                'proses_date' => now()
            ]);
            return ['success' => true];
        } catch (\Throwable $th) {
            DB::rollBack();
            dd($th);
            $this->getMaterial($this->transaksiNo);
            return ['success' => false, 'title' => $th->getMessage()];
        }
    }
    public function render()
    {
        $this->getData();
        return view('livewire.receiving-assy');
    }
}
