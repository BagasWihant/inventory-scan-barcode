<?php

namespace App\Livewire;

use App\Exports\ItemMaterialRequest;
use App\Models\MaterialRequestAssy;
use App\Models\SetupDtlAssy;
use App\Models\SetupMstAssy;
use App\Models\temp_request;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\On;
use Livewire\Component;
use Maatwebsite\Excel\Facades\Excel;

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
            ->leftJoin('setup_dtl as sd', function ($join) {
                $join->on('mra.material_no', '=', 'sd.material_no')
                    ->on('mra.transaksi_no', '=', 'sd.pallet_no');
            })
            ->where('mra.transaksi_no', $trx)
            ->selectRaw('mra.material_no, mra.material_name, mra.request_qty, sd.qty as qty_supply, mra.status, sd.qty as qty_receive')->get();

        $this->transaksiNo = $trx;
        $this->materialScan = null;
        return $dataPrint;
    }

    public function getData()
    {
        $this->data = MaterialRequestAssy::
            // ->when($this->searchKey, function ($q) {
            //     $q->where('transaksi_no', 'like', '%' . $this->searchKey . '%');
            // })
            selectRaw('transaksi_no, status, type, issue_date, line_c, CONVERT(DATE,created_at) as created_at')
            ->groupByRaw('transaksi_no, status, type, CONVERT(DATE,created_at), issue_date, line_c')
            ->orderByDesc(DB::raw('CONVERT(DATE,created_at)'))->get();
    }
   
    public function saveDetailScanned($data)
    {        
        try {
            DB::beginTransaction();
            $mst = SetupMstAssy::create([
                'issue_date' => date('Y-m-d'),
                'status' => '1',
                'line_cd' => $this->transaksiNo,
                'created_by' => $this->userId,
                
            ]);
            $mstId = $mst->id;
            foreach ($data as $item) {
                SetupDtlAssy::create([
                    'setup_id' => $mstId,
                    'material_no' => $item['material_no'],
                    'qty' => $item['qty_receive'],
                    'created_at' => now(),
                    'pallet_no' => $this->transaksiNo,

                ]);
                
                $matMst = DB::table('material_mst')->where('matl_no', $item['material_no']);
                $matMstData = $matMst->first();
                $matMst->update([
                    'qty' => $matMstData->qty - $item['qty_supply'],
                    'qty_OUT' => $matMstData->qty_OUT + $item['qty_supply']
                ]);
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
