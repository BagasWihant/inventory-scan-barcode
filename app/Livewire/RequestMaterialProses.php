<?php

namespace App\Livewire;

use App\Exports\ItemMaterialRequest;
use App\Models\MaterialRequest;
use App\Models\temp_request;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\On;
use Livewire\Component;
use Maatwebsite\Excel\Facades\Excel;

class RequestMaterialProses extends Component
{
    public $searchKey;
    public $materialScan;
    public $transaksiSelected;
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
            ->select('mst.iss_min_lot');
        if ($materialScanned->exists()) {

            $item = $materialScanned->first();
            $tempTransaksiSelected = $this->transaksiSelected;
            $scannedMaterial = $tempTransaksiSelected->filter(function ($sub) {
                return $sub->material_no == $this->materialScan;
            })->first();

            $checkingUser = temp_request::where('transaksi_no', $scannedMaterial->transaksi_no)
                ->select('user_id')->distinct()->first();

            if ($checkingUser && $checkingUser->user_id != $this->userId) {
                return $this->dispatch('alert', ['time' => 3500, 'icon' => 'error', 'title' => "Material is already scanned by another user"]);
            }

            $qtySupply = $item->iss_min_lot;
            if ($item->iss_min_lot > $scannedMaterial->request_qty) {
                $qtySupply = $scannedMaterial->request_qty;
                $this->dispatch('alert', ['time' => 3500, 'icon' => 'error', 'title' => "Qty supply $item->iss_min_lot melebihi Qty request $scannedMaterial->request_qty"]);
            }

            $this->tempRequest = temp_request::where('transaksi_no', $scannedMaterial->transaksi_no)
                ->where('material_no', $scannedMaterial->material_no)
                ->first();

            if ($this->tempRequest) {
                if ($qtySupply == 1) {
                    return $this->dispatch('qtyInput', ['trx' => $scannedMaterial->transaksi_no, 'title' => "$scannedMaterial->material_no Qty request"]);
                }

                $this->tempRequest->update(['qty_supply' => $this->tempRequest->qty_supply + $qtySupply]);
                $this->dispatch('alert', ['time' => 3500, 'icon' => 'success', 'title' => "Material Added"]);
            } else {
                if ($qtySupply == 1) {
                    return $this->dispatch('qtyInput', ['trx' => $scannedMaterial->transaksi_no, 'title' => "$scannedMaterial->material_no Qty request"]);
                }

                temp_request::create([
                    'transaksi_no' => $scannedMaterial->transaksi_no,
                    'material_no' => $scannedMaterial->material_no,
                    'qty_request' => $scannedMaterial->request_qty,
                    'qty_supply' => $qtySupply,
                    'user_id' => $this->userId
                ]);
                $this->dispatch('alert', ['time' => 3500, 'icon' => 'success', 'title' => "Material Added"]);
            }

            $this->getMaterial($scannedMaterial->transaksi_no);
        }
        $this->materialScan = null;
    }
    #[On('inputQty')]
    public function inputQty($qty)
    {
        $tempTransaksiSelected = $this->transaksiSelected;

        $scannedMaterial = $tempTransaksiSelected->filter(function ($sub) {
            return $sub->material_no == $this->materialScan;
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
        $dataPrint = MaterialRequest::where('transaksi_no', $id)
            ->leftJoin('material_mst as b', 'material_request.material_no', '=', 'b.matl_no')
            ->select(['material_request.*', 'b.loc_cd'])->get();

        return Excel::download(new ItemMaterialRequest($dataPrint), "Request Material_" . $id . "_" . date('YmdHis') . ".pdf", \Maatwebsite\Excel\Excel::MPDF);
    }

    public function getMaterial($trx)
    {
        $dataPrint = MaterialRequest::where('material_request.transaksi_no', $trx)
            ->leftJoin('temp_requests as r', function ($join) {
                $join->on('material_request.transaksi_no', '=', 'r.transaksi_no')
                    ->on('material_request.material_no', '=', 'r.material_no');
            })
            ->select(['material_request.*', 'r.qty_supply'])->get();
        $this->transaksiSelected = $dataPrint;

        $this->transaksiNo = $trx;
        $this->materialScan = null;
    }

    public function getData()
    {
        $this->data = MaterialRequest::where('status', '0')
            ->when($this->searchKey, function ($q) {
                $q->where('transaksi_no', 'like', '%' . $this->searchKey . '%');
            })
            ->select(['transaksi_no', 'status', 'type'])
            ->groupBy('transaksi_no', 'status', 'type', 'created_at')
            ->orderByDesc('created_at')
            ->get();
    }


    public function saveDetailScanned()
    {
        $dataConfirm = MaterialRequest::where('material_request.transaksi_no', $this->transaksiNo)
            ->leftJoin('temp_requests as r', function ($join) {
                $join->on('material_request.transaksi_no', '=', 'r.transaksi_no')
                    ->on('material_request.material_no', '=', 'r.material_no');
            })
            ->select(['material_request.*', 'r.qty_supply'])->get();
        
        MaterialRequest::where('material_request.transaksi_no', $this->transaksiNo)->update([
            'status' => '1',
            'proses_date' => now()
        ]);
        DB::beginTransaction();
        foreach ($dataConfirm as $item) {
            if ($item->qty_supply != $item->request_qty) {
                DB::rollBack();
                $this->getMaterial($this->transaksiNo);
                return $this->dispatch('alert', ['time' => 3500, 'icon' => 'error', 'title' => "Tidak bisa Confirm, Qty supply belum sesuai Qty request"]);
            }

            $idSetupMst = DB::table('Setup_mst')->insertGetId([
                'issue_dt' => date('Y-m-d'),
                'line_cd' => $this->transaksiNo,
                'status' => '1',
                'created_at' => now(),
                'created_by' => $this->userId,
                'finished_at' => now(),
            ]);
            DB::table('Setup_dtl')->insert([
                'setup_id' => $idSetupMst,
                'material_no' => $item->material_no,
                'qty' => $item->qty_supply,
                'created_at' => now(),
                'pallet_no' => $this->transaksiNo,
            ]);
            $matMst = DB::table('material_mst')->where('matl_no', $item->material_no);
            $matMstData = $matMst->first();
            $matMst->update([
                'qty' => $matMstData->qty - $item->qty_supply,
                'qty_OUT' => $matMstData->qty_OUT + $item->qty_supply
            ]);
        }
        temp_request::where('transaksi_no', $this->transaksiNo)->delete();
        DB::commit();
    }
    public function render()
    {
        $this->getData();
        return view('livewire.request-material-proses');
    }
}
