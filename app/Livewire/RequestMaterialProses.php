<?php

namespace App\Livewire;

use App\Exports\ItemMaterialRequest;
use App\Models\MaterialRequest;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Maatwebsite\Excel\Facades\Excel;

class RequestMaterialProses extends Component
{
    public $searchKey;
    public $materialScan;

    public function updated($prop,$val){

        switch ($prop) {
            case 'materialScan':
                if (strtolower(substr($val, 0, 1)) == "c") {
                    $tempSplit = explode(' ', $val);
    
                    if(strtolower(substr($val, 0, 1)) == "p") {
                        $this->materialScan = substr($val, 1, 15);
                    }else{
                        $this->materialScan = substr($tempSplit[0], 23, 15);
                    }
                }
                
                break;
            
            default:
                # code...
                break;
        }
    }
    
    public function print($id)
    {
        $dataPrint = MaterialRequest::where('transaksi_no', $id)
            ->leftJoin('material_mst as b', 'material_request.material_no', '=', 'b.matl_no')
            ->select(['material_request.*', 'b.loc_cd'])->get();

        return Excel::download(new ItemMaterialRequest($dataPrint), "Request Material_" . $id . "_" . date('YmdHis') . ".pdf", \Maatwebsite\Excel\Excel::MPDF);
    }

    public function getMaterial($trx){
        $dataPrint = MaterialRequest::where('transaksi_no', $trx)
        ->leftJoin('material_mst as b', 'material_request.material_no', '=', 'b.matl_no')
        ->select(['material_request.*', 'b.loc_cd'])->get()->toArray();

        return [$trx,$dataPrint];
    }

    public function render()
    {
        $data = MaterialRequest::where('status', '0')
            ->when($this->searchKey, function ($q) {
                $q->where('transaksi_no', 'like', '%' . $this->searchKey . '%');
            })
            ->select(['transaksi_no', 'status', 'type'])
            ->groupBy('transaksi_no', 'status', 'type')->get();

        return view('livewire.request-material-proses', compact('data'));
    }
}
