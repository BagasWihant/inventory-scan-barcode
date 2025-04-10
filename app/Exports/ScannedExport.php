<?php

namespace App\Exports;

use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Style\Border;
use Maatwebsite\Excel\Concerns\WithDrawings;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromCollection;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use Maatwebsite\Excel\Concerns\WithDefaultStyles;
use Maatwebsite\Excel\Concerns\WithMapping;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Style;

class ScannedExport implements WithEvents, WithCustomStartCell, FromCollection, WithColumnWidths, WithHeadings, WithMapping
{
    public $data, $count;
    public function __construct($dt)
    {
        $this->data = $dt;
        $this->count = count($dt);
    }

    public function startCell(): string
    {
        return 'A7';
    }

    public function columnWidths(): array
    {
        return [
            'A' => 15,
            'B' => 10,
            'C' => 5,
            'D' => 5,
            'E' => 10,
            'F' => 10,
            'G' => 10,
            'H' => 10,
            'I' => 10,
            'J' => 10,
            'K' => 10,
            'L' => 10,
        ];
    }

    public function view(): View
    {
        return view('exports.in-stock', ['data' => $this->data]);
    }

    public function map($row): array
    {
        // $qty = $row->counter;
        // if($row->counter > $row->total){
        //     $qty = $row->total;
        // }
        $char = "□";
        $kosong = "  ";
        return [
            $row->material,
            $row->line_c,
            $row->pax,
            $row->counter,
        ];
    }

    public function collection()
    {
        return $this->data;
    }

    public function headings(): array
    {
        return [
            'Material No',
            'Line Code',
            'Pax',
            'Picking Qty',
            'Loc 1',
            'Qty',
            'Loc 2',
            'Qty',
            'Loc 3',
            'Qty',
            'Loc 4',
            'Qty',
        ];
    }

    public function drawings()
    {

        $drawing = new Drawing();
        $drawing->setName('Logo');
        $drawing->setDescription('This is my logo');
        $drawing->setPath(public_path('/assets/logo.png'));
        $drawing->setHeight(50);
        $drawing->setCoordinates('A1');
        return $drawing;
    }

    /**
     * @return array
     */
    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet;
                $spreadset = new Spreadsheet;

                $sheet->mergeCells('A1:K3');
                $sheet->setCellValue('A1', "Scanned List");
                $sheet->mergeCells('A4:B4');
                $sheet->setCellValue('A4', "Issue Date");
                $sheet->mergeCells('C4:F4');
                $sheet->setCellValue('C4', " ". Carbon::parse($this->data[0]->plan_issue_dt_from)->format('d-m-Y'));
                // $sheet->mergeCells('C4:D4');
                // $sheet->setCellValue('C4', ": " . $this->data[0]->palet);
                $sheet->mergeCells('A5:B5');
                $sheet->setCellValue('A5', "Receiving Date");
                $sheet->mergeCells('C5:F5');
                $sheet->setCellValue('C5', ": " . date('d-m-Y'));
                $sheet->getDelegate()->getStyle('A1:K2')->getFont()->setSize(20);
                $sheet->getDelegate()->getStyle('A1:K6')->getFont()->setBold(true);
                $sheet->getDelegate()->getStyle('A4:L5')->applyFromArray([
                    'alignment' => [
                        'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT,
                        'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                    ],
                ]);

                $styleArray = [
                    'alignment' => [
                        'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                        'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                    ],
                ];

                $cellRange = 'A1:L3'; // All headers
                $event->sheet->getDelegate()->getStyle($cellRange)->applyFromArray($styleArray);
                $event->sheet->getDelegate()->getStyle("A7:K" . $this->count + 7)->applyFromArray($styleArray);

                $border = [
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color' => ['argb' => '000000'],
                        ],
                    ],
                ];
                $event->sheet->getDelegate()->getStyle("A7:L" . $this->count + 7)->applyFromArray($border);
            }
        ];
    }
}
