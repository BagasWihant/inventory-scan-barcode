<?php

namespace App\Exports;

use App\Models\itemIn;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\WithEvents;
use PhpOffice\PhpSpreadsheet\Style\Border;
use Maatwebsite\Excel\Concerns\WithDrawings;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\FromQuery;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;

class ExportMaterialAvailable implements FromQuery, WithEvents, WithCustomStartCell, WithColumnWidths, WithHeadings
{
    public $data, $count, $dateStart, $dateEnd, $searchMat;
    public function __construct($dt)
    {
        $this->dateStart = $dt[0];
        $this->dateEnd = $dt[1];
        $this->searchMat = $dt[2];
    }

    public function startCell(): string
    {
        return 'A5';
    }

    public function columnWidths(): array
    {
        return [
            'A' => 20,
            'B' => 25,
            'C' => 15,
            'D' => 15,
            'E' => 15,
            'F' => 15,
        ];
    }

    public function query()
    {
        $startDate = $this->dateStart;
        $endDate = $this->dateEnd;
        $materialNo = $this->searchMat;
        return DB::query()
            ->fromSub(function ($query) use ($startDate, $endDate, $materialNo) {
                $query->from('material_in_stock as mis')
                    ->select(
                        'mis.material_no',
                        DB::raw('SUM(mis.picking_qty) as total_picking_qty'),
                        DB::raw('MIN(CONVERT(DATE, mis.created_at)) as first_created_at')
                    )
                    ->whereBetween(DB::raw('CONVERT(DATE, mis.created_at)'), [$startDate, $endDate])
                    ->when($materialNo, function ($sub) use ($materialNo) {
                        $sub->where('mis.material_no', $materialNo);
                    })->where('mis.locate', '!=', 'ASSY')
                    ->groupBy('mis.material_no');
            }, 'MaterialInStock')
            ->leftJoinSub(function ($query) use ($startDate, $endDate, $materialNo) {
                $query->fromSub(function ($subQuery) use ($startDate, $endDate, $materialNo) {
                    $subQuery->from('siws_materialrequest.dbo.dtl_transaction')
                        ->select('part_number', DB::raw('SUM(qty_mc) as Qty'))
                        ->whereBetween(DB::raw('CONVERT(DATE, transaction_date)'), [$startDate, $endDate])
                        ->when($materialNo, function ($sub) use ($materialNo) {
                            $sub->where('part_number', $materialNo);
                        })
                        ->groupBy('part_number')
                        ->union(
                            DB::table('Setup_dtl as c')
                                ->leftJoin('Setup_mst as b', 'b.id', '=', 'c.setup_id')
                                ->select('c.material_no as part_number', DB::raw('SUM(c.qty) as Qty'))
                                ->whereNotNull('b.finished_at')
                                ->whereBetween(DB::raw('CONVERT(DATE, c.created_at)'), [$startDate, $endDate])
                                ->when($materialNo, function ($sub) use ($materialNo) {
                                    $sub->where('c.material_no', $materialNo);
                                })
                                ->groupBy('c.material_no')
                        );
                }, 'combined_qty_out')
                    ->select('part_number', DB::raw('SUM(Qty) as qty'))
                    ->groupBy('part_number');
            }, 'QuantityOut', 'MaterialInStock.material_no', '=', 'QuantityOut.part_number')
            ->leftJoin('material_mst as mst', 'MaterialInStock.material_no', '=', 'mst.matl_no')
            ->select(
                'MaterialInStock.material_no',
                DB::raw('MaterialInStock.total_picking_qty as qty_in'),
                DB::raw('COALESCE(QuantityOut.qty, 0) as qty_out'),
                DB::raw('(MaterialInStock.total_picking_qty - COALESCE(QuantityOut.qty, 0)) as qty_balance'),
                DB::raw('SUM(mst.qty) as qty_now'),
                'mst.loc_cd'
            )
            ->where('mst.loc_cd', '!=', 'ASSY')
            ->groupBy(
                'MaterialInStock.material_no',
                'MaterialInStock.total_picking_qty',
                'QuantityOut.qty',
                'mst.loc_cd'
            )
            ->orderBy('MaterialInStock.material_no');
    }
    public function headings(): array
    {
        return [
            'Material Code',
            'Qty In',
            'Qty Out',
            'Qty Balance',
            'Qty Now',
            'Lokasi',
        ];
    }


    /**
     * @return array
     */
    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet;

                $sheet->mergeCells('A1:F2');
                $sheet->setCellValue('A1', "MATERIAL AVAILABLE");
                $sheet->setCellValue('A3', "Per Tanggal : " . date('d-m-Y H:i'));
                $sheet->getDelegate()->getStyle('A1:F2')->getFont()->setSize(20);
                $sheet->getDelegate()->getStyle('A1:F3')->getFont()->setBold(true);



                $styleArray = [
                    'alignment' => [
                        'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                    ],
                ];

                $border = [
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color' => ['argb' => '000000'],
                        ],
                    ],
                ];

                $cellRange = 'A1:F' . $this->count + 5; // All headers
                $event->sheet->getDelegate()->getStyle($cellRange)->applyFromArray($styleArray);
                $event->sheet->getDelegate()->getStyle("A5:F" . $this->count + 5)->applyFromArray($border);
            }
        ];
    }
}
