<?php

namespace App\Livewire\Components;

use App\Exports\ReceivingReportCNCExcel;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Maatwebsite\Excel\Facades\Excel;

class ReceivingReportCnc extends Component
{
    public $searchPalet, $listPalet = [], $listMaterial = [], $receivingData = [], $materialCode, $dateEnd, $dateStart, $inputDisable = false;
    public $truckingId, $listTruck = [], $truckingDisable = false, $paletDisable = false, $exportDisable = false;

    public function updated($prop)
    {
        switch ($prop) {
            case 'truckingId':
                if (strlen($this->truckingId) >= 2) {
                    $distinc = DB::table('material_in_stock')
                        ->where('trucking_id', 'like', '%' . $this->truckingId . '%')
                        ->select('trucking_id')->distinct()->limit(10);
                    $this->listTruck = $distinc->pluck('trucking_id')->all();
                }
                break;
            case 'searchPalet':
                if (strlen($this->searchPalet) >= 2) {
                    $distinc = DB::table('material_in_stock')
                        ->where('trucking_id', $this->truckingId)
                        ->where('pallet_no', 'like', '%' . $this->searchPalet . '%')
                        ->select('pallet_no')->distinct()->limit(10);
                    $this->listPalet = $distinc->get();
                }
                break;
        }
    }
    public function choosePalet($palet)
    {
        $this->truckingDisable = true;
        $this->inputDisable = true;
        $this->paletDisable = true;
        $this->searchPalet = $palet;
        $this->listPalet = 'kosong';
        $this->listMaterial = DB::table('material_setup_mst_CNC_KIAS2')->select('material_no')->where('pallet_no', $this->searchPalet)->distinct()->get();
        //  = $distinc->pluck('material_no')->all();
    }

    public function chooseTrucking($truck)
    {
        $this->truckingId = $truck;
        $this->inputDisable = true;
        $this->truckingDisable = true;
        $this->listTruck = 'kosong';
    }
    public function resetData()
    {
        $this->inputDisable = false;
        $this->exportDisable = false;
        $this->paletDisable = false;
        $this->truckingDisable = false;
        $this->receivingData = [];
        $this->searchPalet = "";
        $this->materialCode = "";
        $this->dateStart = "";
        $this->dateEnd = "";
        $this->truckingId = null;
    }
    public function showData()
    {
        if (!$this->dateStart && !$this->dateEnd) {
            $this->dateStart = '2023-01-01';
            $this->dateEnd = date('Y-m-d');
        }
        $this->exportDisable = true;

        $this->receivingData = DB::select('EXEC sp_Receiving_report ?,?,?,?,?,?,?', [
            'detail',
            $this->searchPalet ?? "",
            $this->dateStart ?? '',
            $this->dateEnd ?? "",
            '',
            $this->materialCode ?? "",
            '',
        ]);
    }
    public function export($type)
    {
        switch ($type) {
            case 'xls':
                $data = [
                    'data' => collect($this->receivingData),
                    'type' => "CNC"
                ];
                return Excel::download(new ReceivingReportCNCExcel($data), "Receiving Report_" . date('YmdHis') . ".xls", \Maatwebsite\Excel\Excel::XLSX);
                break;

            default:
                # code...
                break;
        }
    }
    public function render()
    {
        return view('livewire.components.receiving-report-cnc');
    }
}
