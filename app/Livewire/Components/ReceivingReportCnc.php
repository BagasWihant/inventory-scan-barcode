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
    public $issue_dt;
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
                        ->when(
                            $this->truckingId,
                            fn($q) => $q->where('trucking_id', $this->truckingId)
                        )
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
        $this->receivingData = $this->getData(
            $this->searchPalet,
            $this->materialCode,
            $this->dateStart,
            $this->dateEnd,
            $this->issue_dt
        );
        // $this->receivingData = DB::select('EXEC sp_Receiving_report ?,?,?,?,?,?,?,?', [
        //     'detail',
        //     $this->searchPalet ?? "", Y-01-00003
        //     $this->dateStart ?? '', 2024-01-02 08:25:34.000
        //     $this->dateEnd ?? "",2024-01-02 08:25:34.000
        //     '',
        //     $this->materialCode ?? "", 81000801
        //     '',
        //     $this->issue_dt 2024-01-09 00:00:00.000
        // ]);
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

    private function getData(
        $pallet,
        $material,
        $dStart,
        $dEnd,
        $issueDt,
        $kit = NULL,
        $trucking = NULL
    ) {
        $mis = DB::table('material_in_stock as b')
            ->select('b.pallet_no', 'b.material_no')
            ->selectRaw('SUM(b.picking_qty) AS Qty_Received_KIAS')
            ->selectRaw('MIN(CONVERT(date, b.created_at)) AS created_first')
            ->selectRaw('MAX(CONVERT(date, b.created_at)) AS created_last')
            ->groupBy('b.pallet_no', 'b.material_no');


        $q = DB::table('material_setup_mst_CNC_KIAS2 as a')
            ->leftJoin('delivery_mst as c', 'c.pallet_no', '=', 'a.pallet_no')
            ->leftJoinSub($mis, 'mis', function ($join) {
                $join->on('mis.pallet_no', '=', 'a.pallet_no')
                    ->on('mis.material_no', '=', 'a.material_no');
            })
            ->whereNotNull('a.kit_no')
            ->when($kit,      fn($qr) => $qr->where('a.kit_no', $kit))
            ->when($pallet,   fn($qr) => $qr->where('a.pallet_no', $pallet))
            ->when($trucking, fn($qr) => $qr->where('c.trucking_id', $trucking))
            ->when($material, fn($qr) => $qr->where('a.material_no', $material))
            ->when($dStart && $dEnd, function ($qr) use ($dStart, $dEnd) {
                $qr->whereRaw('a.setup_date >= ? AND a.setup_date < DATEADD(DAY,1,?)', [$dStart, $dEnd]);
            })
            ->when($issueDt, function ($qr) use ($issueDt) {
                $qr->whereRaw('CONVERT(varchar, a.plan_issue_dt_from, 23) LIKE ?', ["%{$issueDt}%"]);
            })
            ->selectRaw("
            CONVERT(varchar, a.setup_date, 23) AS [Delivery_Supply_Date_SIWS],
            ISNULL(CONVERT(varchar, mis.created_first, 23), 'On Progress Delivery') AS [Received_Date],
            c.trucking_id,
            a.kit_no,
            a.pallet_no,
            a.material_no,
            SUM(a.picking_qty) AS [Qty_Delivery_SIWS],
            ISNULL(mis.Qty_Received_KIAS, 0) AS [Qty_Received_KIAS],
            CASE
                WHEN SUM(a.picking_qty) = ISNULL(mis.Qty_Received_KIAS, 0)
                    THEN CONVERT(varchar, mis.created_last, 23)
                ELSE ''
            END AS [Completed Received Date]
        ")
            ->groupByRaw("
            a.kit_no, a.pallet_no, a.material_no,
            CONVERT(varchar, a.setup_date, 23),
            c.trucking_id,
            mis.created_first, mis.created_last, mis.Qty_Received_KIAS
        ")
            ->orderBy('a.kit_no')
            ->orderBy('a.pallet_no');

        return $q->get();
    }
}
