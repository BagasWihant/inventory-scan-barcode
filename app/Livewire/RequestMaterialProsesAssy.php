<?php

namespace App\Livewire;

use App\Exports\ItemMaterialRequest;
use App\Models\MaterialRequestAssy;
use App\Models\temp_request;
use \Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\On;
use Livewire\Component;
use Maatwebsite\Excel\Facades\Excel;

class RequestMaterialProsesAssy extends Component
{
    public $searchKey;
    public $materialScan;
    public $transaksiSelected;
    public $transaksiNo;
    public $tempRequest;
    public $materialSelected;
    public $userId;
    public $data;
    public $todayCount;
    public $canConfirm;

    public function mount()
    {
        $this->userId = auth()->user()->id;
        $this->getData();
    }

    private function generateSJ()
    {
        $prefix = date('Ym');
        $configKey = 'SJ_MR';

        $row = DB::table('WH_config')->where('config', $configKey)->first();

        if (!$row || !Str::startsWith($row->value, $prefix)) {
            $newValue = $prefix . '0001';

            DB::table('WH_config')->updateOrInsert(
                ['config' => $configKey],
                ['value' => $newValue]
            );
        } else {
            $lastNumber = (int) substr($row->value, -4);
            $newNumber = str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);
            $newValue = $prefix . $newNumber;

            DB::table('WH_config')->where('config', $configKey)->update(['value' => $newValue]);
        }
        return $newValue;
    }

    public function updated($prop, $val)
    {

        switch ($prop) {
            case 'searchKey':
                $this->getData();
                break;

            default:
                # code...
                break;
        }
    }

    public function prosesScan()
    {
        if (strlen($this->materialScan) < 3) {
            return;
        }
        if (strtolower(substr($this->materialScan, 0, 1)) == "c") {
            $tempSplit = explode(' ', $this->materialScan);

            if (strtolower(substr($this->materialScan, 0, 1)) == "p") {
                $this->materialScan = substr($this->materialScan, 1, 15);
            } else {
                $this->materialScan = substr($tempSplit[0], 23, 15);
            }
        }

        $materialScanned = DB::table('material_conversion_mst as m')->where('supplier_code', $this->materialScan)
            ->leftJoin('material_mst as mst', 'm.sws_code', '=', 'mst.matl_no')
            ->select(['mst.iss_min_lot', 'm.sws_code']);
        
        $this->dispatch('playsound');

        if ($materialScanned->exists()) {
            $item = $materialScanned->first();
            $tempTransaksiSelected = $this->transaksiSelected;
            $this->materialScan = $item->sws_code;
            $scannedMaterial = $tempTransaksiSelected->filter(function ($sub) use ($item) {
                return str_replace(' ', '', $sub->material_no) == str_replace(' ', '', $item->sws_code);
            })->first();

            if (!$scannedMaterial) {
                return $this->dispatch('alert', ['time' => 3500, 'icon' => 'error', 'title' => "Material not in list"]);
            }

            // Lama yang baru dibawah biar gak dobel query. yang bawah query this->tempRequest
            // $checkingUser = temp_request::where('transaksi_no', $scannedMaterial->transaksi_no)
            //     ->select('user_id')->distinct()->first();

            $checkingUser = temp_request::where('transaksi_no', $scannedMaterial->transaksi_no)
                ->where('material_no', $scannedMaterial->material_no)
                ->first();

            if ($checkingUser && $checkingUser->user_id != $this->userId) {
                return $this->dispatch('alert', ['time' => 3500, 'icon' => 'error', 'title' => "Material is already scanned by another user"]);
            }

            $qtySupply = $item->iss_min_lot;
            if ($item->iss_min_lot > $scannedMaterial->request_qty) {
                $qtySupply = $scannedMaterial->request_qty;
                $this->dispatch('alert', ['time' => 3500, 'icon' => 'error', 'title' => "Qty supply $item->iss_min_lot melebihi Qty request $scannedMaterial->request_qty"]);
            }

            $this->tempRequest =  $checkingUser;

            $this->inputQty($scannedMaterial->request_qty);

            $this->getMaterial($scannedMaterial->transaksi_no);
        }
        $this->materialScan = null;
    }
    #[On('inputQty')]
    public function inputQty($qty)
    {
        $tempTransaksiSelected = $this->transaksiSelected;

        $scannedMaterial = $tempTransaksiSelected->filter(function ($sub) {
            return str_replace(' ', '', trim($sub->material_no)) == str_replace(' ', '', trim($this->materialScan));
        })->first();

        if ($this->tempRequest) {
            $qtySupply = $qty + $this->tempRequest->qty_supply;

            if ($qtySupply > $scannedMaterial->request_qty) {
                $this->getMaterial($this->transaksiNo);
                return $this->dispatch('alert', ['time' => 3500, 'icon' => 'error', 'title' => "Qty supply $qtySupply melebihi Qty request $scannedMaterial->request_qty"]);
            }

            $this->tempRequest->update(['qty_supply' => $qtySupply]);
        } else {
            if ($qty > $scannedMaterial->request_qty) {
                $this->getMaterial($this->transaksiNo);
                return $this->dispatch('alert', ['time' => 3500, 'icon' => 'error', 'title' => "Qty supply $qty melebihi Qty request $scannedMaterial->request_qty"]);
            }

            temp_request::create([
                'transaksi_no' => $scannedMaterial->transaksi_no,
                'material_no' => $scannedMaterial->material_no,
                'qty_request' => $scannedMaterial->request_qty,
                'qty_supply' => $qty,
                'user_id' => $this->userId
            ]);
        }
        $this->getMaterial($this->transaksiNo);
        $this->materialScan = null;
        $this->dispatch('alert', ['time' => 3500, 'icon' => 'success', 'title' => "Material Added"]);
    }

    public function print($id)
    {
        $dataPrint = MaterialRequestAssy::where('transaksi_no', $id)
            ->leftJoin('material_mst as b', 'material_request_assy.material_no', '=', 'b.matl_no')
            ->select(['material_request_assy.*', 'b.loc_cd', DB::raw('(b.iss_min_lot/request_qty) as pax')])->orderBy('b.loc_cd', 'asc')->get();
        $no_sj = $this->generateSJ();
        MaterialRequestAssy::where('transaksi_no', $id)->update(['type' => '1', 'surat_jalan' => $no_sj]);

        return Excel::download(new ItemMaterialRequest($dataPrint, $no_sj), "Request Material_" . $id . "_" . date('YmdHis') . ".pdf", \Maatwebsite\Excel\Excel::MPDF);
    }

    public function getMaterial($trx)
    {
        DB::enableQueryLog();
        $dataPrint = MaterialRequestAssy::where('material_request_assy.transaksi_no', $trx)
            ->leftJoin('temp_requests as r', function ($join) {
                $join->on('material_request_assy.transaksi_no', '=', 'r.transaksi_no')
                    ->on('material_request_assy.material_no', '=', 'r.material_no');
            })
            ->leftJoin('material_mst as b', 'material_request_assy.material_no', '=', 'b.matl_no')
            ->select(['material_request_assy.*', 'r.qty_supply', 'b.qty as stock'])->get();
        // dump(DB::getRawQueryLog());
        $this->canConfirm = $dataPrint->first()->status;
        $this->transaksiSelected = $dataPrint;

        $this->transaksiNo = $trx;
        $this->materialScan = null;
        return ['success' => true];
    }

    public function getData()
    {
        $this->data =  MaterialRequestAssy::when($this->searchKey, function ($q) {
            $q->where('transaksi_no', 'like', '%' . $this->searchKey . '%');
        })
            ->selectRaw('transaksi_no, status, type, issue_date, line_c, CONVERT(DATE,created_at) as created_at')
            ->groupByRaw('transaksi_no, status, type, CONVERT(DATE,created_at), issue_date, line_c')
            ->orderByDesc(DB::raw('CONVERT(DATE,created_at)'))->get();

        $today = now()->toDateString();
        $this->todayCount = $this->data->filter(function ($item) use ($today) {
            return \Carbon\Carbon::parse($item->created_at)->toDateString() === $today;
        })->count();
    }

    public function resetQty($material)
    {
        temp_request::where('material_no', $material)->where('transaksi_no', $this->transaksiNo)->update(['qty_supply' => 0]);
        $this->getMaterial($this->transaksiNo);
    }

    #[On('editQty')]
    public function editQty($data)
    {
        temp_request::where('material_no', $data['material'])->where('transaksi_no', $this->transaksiNo)->update(['qty_request' => $data['qty']]);
        MaterialRequestAssy::where('material_no', $data['material'])->where('transaksi_no', $this->transaksiNo)->update(['request_qty' => $data['qty']]);
        $this->getMaterial($this->transaksiNo);
        $this->dispatch('alert', ['time' => 1500, 'icon' => 'success', 'title' => "Qty Changed"]);
    }

    public function saveDetailScanned()
    {
        $dataConfirm = MaterialRequestAssy::where('material_request_assy.transaksi_no', $this->transaksiNo)
            ->leftJoin('temp_requests as r', function ($join) {
                $join->on('material_request_assy.transaksi_no', '=', 'r.transaksi_no')
                    ->on('material_request_assy.material_no', '=', 'r.material_no');
            })
            ->select(['material_request_assy.*', 'r.qty_supply'])->get();


        try {

            DB::beginTransaction();
            $idSetupMst = DB::table('Setup_mst')->insertGetId([
                'issue_dt' => date('Y-m-d'),
                'line_cd' => $this->transaksiNo,
                'status' => '1',
                'created_at' => now(),
                'created_by' => $this->userId,
                'finished_at' => now(),
            ]);
            $idSetupMst = DB::table('Setup_mst_assy')->insertGetId([
                'issue_dt' => date('Y-m-d'),
                'line_cd' => $this->transaksiNo,
                'status' => '1',
                'created_at' => now(),
                'created_by' => $this->userId,
                'finished_at' => now(),
            ]);
            foreach ($dataConfirm as $item) {
                if ($item->qty_supply > $item->request_qty || $item->qty_supply == 0 || $item->qty_supply == null) {
                    DB::rollBack();
                    $this->getMaterial($this->transaksiNo);
                    return ['success' => false, 'message' => "Tidak bisa Confirm, Qty supply lebih atau kosong"];
                }

                DB::table('Setup_dtl_assy')->insert([
                    'setup_id' => $idSetupMst,
                    'material_no' => $item->material_no,
                    'qty' => $item->qty_supply,
                    'created_at' => now(),
                    'pallet_no' => $this->transaksiNo,
                ]);
                DB::table('Setup_dtl')->insert([
                    'setup_id' => $idSetupMst,
                    'material_no' => $item->material_no,
                    'qty' => $item->qty_supply,
                    'created_at' => now(),
                    'pallet_no' => $this->transaksiNo,
                ]);
                // $matMst = DB::table('material_mst')->where('matl_no', $item->material_no);
                // $matMstData = $matMst->first();
                // $matMst->update([
                //     'qty' => $matMstData->qty - $item->qty_supply,
                //     'qty_OUT' => $matMstData->qty_OUT + $item->qty_supply
                // ]);
            }

            // qty supply baca dari temp request nek tak delete di receiving assy suplly nya kosong
            // temp_request::where('transaksi_no', $this->transaksiNo)->delete();
            DB::commit();

            MaterialRequestAssy::where('material_request_assy.transaksi_no', $this->transaksiNo)->update([
                'status' => '1',
                'proses_date' => now()
            ]);
            return ['success' => true];
        } catch (\Throwable $th) {
            DB::rollBack();
            $this->getMaterial($this->transaksiNo);
            return ['success' => false, 'title' => $th->getMessage()];
        }
    }

    public function render()
    {
        $this->getData();
        return view('livewire.request-material-proses-assy');
    }
}
