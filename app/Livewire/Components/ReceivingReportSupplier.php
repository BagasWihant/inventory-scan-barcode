<?php

namespace App\Livewire\Components;

use App\Exports\ReceivingReportCNCExcel;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Maatwebsite\Excel\Facades\Excel;

class ReceivingReportSupplier extends Component
{
    public $kitNo, $paletNo, $materialCode, $dateStart, $dateEnd, $suratJalan;
    public $listPalet = [], $listPaletNoSup = [], $listMaterial = [], $receivingData = [], $listSuratJalan = [];
    public $clearButton = false, $suratJalanDisable = false, $kitNoDisable = false, $paletNoDisable = false, $materialCodeDisable = false, $exportDisable = false;

    public function updated($prop)
    {
        switch ($prop) {
            case 'kitNo':

                $distinc = DB::table('material_in_stock')
                    ->where('kit_no', 'like', '%' . $this->kitNo . '%')
                    ->when($this->suratJalan, function ($query) {
                        return $query->where('surat_jalan', $this->suratJalan);
                    })
                    ->select('kit_no as pallet_no')->distinct()->limit(10);
                $this->listPalet = $distinc->pluck('pallet_no')->all();
                break;

            case 'paletNo':

                $distinc = DB::table('material_in_stock')->select('pallet_no')
                    ->where('kit_no', $this->kitNo)
                    ->where('pallet_no', 'like', '%' . $this->paletNo . '%')
                    ->distinct()->limit(10);
                $this->listPaletNoSup = $distinc->pluck('pallet_no')->all();

                break;
            case 'suratJalan':
                $distincSJ = DB::table('material_in_stock')
                    ->where('surat_jalan', 'like', '%' . $this->suratJalan . '%')
                    ->select('surat_jalan')->distinct()->limit(10);
                $this->listSuratJalan = $distincSJ->pluck('surat_jalan')->all();

                break;
            case 'materialCode':

                $distinc = DB::table('material_in_stock')->select('material_no')
                    ->where('kit_no', $this->kitNo)
                    ->where('pallet_no', $this->paletNo)
                    ->where('material_no', 'like', '%' . $this->materialCode . '%')
                    ->distinct()->limit(10);
                $this->listMaterial = $distinc->pluck('material_no')->all();

                break;
            default:
                return;
                break;
        }
    }

    public function chooseSuratJalan($palet)
    {
        $this->suratJalan = $palet;
        $this->suratJalanDisable = true;
        $this->clearButton = true;
        $this->listSuratJalan =  'kosong';
    }

    public function chooseKitNo($kit)
    {
        $this->kitNo = $kit;
        $this->kitNoDisable = true;
        $this->clearButton = true;
        $this->suratJalanDisable = true;
        $this->listPalet = 'kosong';
    }


    public function choosePalet($palet)
    {
        $this->paletNo = $palet;
        $this->paletNoDisable = true;
        $this->clearButton = true;
        $this->listPaletNoSup = 'kosong';
    }

    public function chooseMaterial($mat)
    {
        $this->materialCode = $mat;
        $this->materialCodeDisable = true;
        $this->clearButton = true;
        $this->listMaterial = 'kosong';
    }

    public function showData()
    {
        $this->exportDisable = true;

        $this->clearButton = true;
        if (!$this->dateStart && !$this->dateEnd) {
            $this->dateStart = '2023-01-01';
            $this->dateEnd = date('Y-m-d');
        }

        $this->receivingData = collect($this->getData(
            $this->paletNo,
            $this->materialCode,
            $this->dateStart,
            $this->dateEnd,
            $this->kitNo ?? null
        ));
        // $this->receivingData = DB::select('EXEC sp_Receiving_report_supplier ?,?,?,?,?,?', $data);
    }

    public function resetData()
    {
        $this->receivingData = [];
        $this->materialCode = "";
        $this->dateStart = "";
        $this->paletNo = "";
        $this->kitNo = "";
        $this->suratJalan = "";
        $this->dateEnd = "";
        $this->clearButton = false;
        $this->suratJalanDisable = false;
        $this->kitNoDisable = false;
        $this->paletNoDisable = false;
        $this->exportDisable = false;
    }

    public function export($type)
    {
        switch ($type) {
            case 'xls':
                $data = [
                    'data' => collect($this->receivingData),
                    'type' => "Supplier",
                ];
                // dump($data);
                return Excel::download(new ReceivingReportCNCExcel($data), "Receiving Report_" . date('YmdHis') . ".xls", \Maatwebsite\Excel\Excel::XLSX);
                break;

            default:
                # code...
                break;
        }
    }

    public function render()
    {
        return view('livewire.components.receiving-report-supplier');
    }

    private function getData($pallet, $material, $dStart, $dEnd, $kit = NULL)
    {
        $mis = DB::table('material_in_stock as b')
            ->select('b.kit_no', 'b.material_no', 'b.line_c')
            ->selectRaw('SUM(b.picking_qty) AS picking_qty')
            ->selectRaw('MIN(CONVERT(date, b.created_at)) AS created_first')
            ->selectRaw('MAX(CONVERT(date, b.created_at)) AS created_last')
            ->groupBy('b.kit_no', 'b.material_no', 'b.line_c');

        $q = DB::table('material_setup_mst_supplier as a')
            ->leftJoin('material_in_stock as c', function ($j) {
                $j->on('c.kit_no', '=', 'a.kit_no')
                    ->on('c.material_no', '=', 'a.material_no')
                    ->on('c.line_c', '=', 'a.line_c');
            })
            ->leftJoinSub($mis, 'mis', function ($j) {
                $j->on('mis.kit_no', '=', 'a.kit_no')
                    ->on('mis.material_no', '=', 'a.material_no')
                    ->on('mis.line_c', '=', 'a.line_c');
            })
            ->whereNotNull('a.kit_no')
            ->when($kit,      fn($qr) => $qr->where('a.kit_no', $kit))
            ->when($pallet,   fn($qr) => $qr->where('c.pallet_no', $pallet))
            ->when($material, fn($qr) => $qr->where('a.material_no', $material))
            // filter tanggal di c.created_at sesuai SP
            ->whereRaw('CONVERT(varchar, c.created_at, 23) BETWEEN ? AND ?', [$dStart, $dEnd])
            ->selectRaw("
            CONVERT(varchar, a.setup_date, 23) AS [Delivery_Supply_Date_SIWS],
            ISNULL(CONVERT(varchar, mis.created_first, 23), 'On Progress Delivery') AS [Received_Date],
            a.kit_no,
            c.pallet_no,
            a.material_no,
            a.line_c,
            SUM(a.picking_qty) AS [Qty_Delivery_Supplier],
            ISNULL(mis.picking_qty, 0) AS Qty_Received_KIAS,
            CASE
              WHEN SUM(a.picking_qty) = ISNULL(mis.picking_qty, 0)
                THEN CONVERT(varchar, mis.created_last, 23)
              ELSE ''
            END AS [Completed_Received_Date]
        ")
            ->groupByRaw("
            a.kit_no, a.material_no, a.line_c,
            CONVERT(varchar, a.setup_date, 23),
            c.pallet_no,
            mis.created_first, mis.created_last, mis.picking_qty
        ")
            ->orderBy('a.kit_no')
            ->orderBy('a.material_no');

        return $q->get();
    }
}
