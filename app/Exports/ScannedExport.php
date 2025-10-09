<?php

namespace App\Exports;

use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\WithEvents;
use PhpOffice\PhpSpreadsheet\Style\Border;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use Maatwebsite\Excel\Concerns\WithMapping;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

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
            'A' => 3,
            'B' => 25,
            'C' => 10,
            'D' => 10,
            'E' => 10,
            'F' => 25,
            'G' => 10,
        ];
    }

    public function map($row): array
    {
        $char = "□";
        return [
            $char,
            $row->material,
            $row->pax,
            $row->counter,
            $row->qty_pax,
            $row->trucking_id,
            $row->location_cd,
        ];
    }

    public function collection()
    {
        return $this->data;
    }

    public function headings(): array
    {
        return [
            ' ',
            'Material No',
            'Pax',
            'Picking Qty',
            'Qty Pax',
            'Trucking ID',
            'Location'
        ];
    }

    /**
     * @return array
     */
    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();

                $sheet->mergeCells('A1:G3');
                $sheet->setCellValue('A1', "Scanned List");
                $sheet->mergeCells('A4:B4');
                $sheet->setCellValue('A4', "Receiving Date");
                $sheet->mergeCells('C4:F4');
                $sheet->setCellValue('C4', ": " . date('d-m-Y'));

                $sheet->getStyle('A1:G2')->getFont()->setSize(20);
                $sheet->getStyle('A1:G6')->getFont()->setBold(true);
                $sheet->getStyle('A4:G5')->applyFromArray([
                    'alignment' => [
                        'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT,
                        'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                    ],
                ]);

                $lastRow = 7 + max($this->count, 1);

                $center = [
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                        'vertical'   => Alignment::VERTICAL_CENTER,
                    ],
                ];

                $sheet->getStyle('A1:G3')->applyFromArray($center);
                $sheet->getStyle("A7:G{$lastRow}")->applyFromArray($center);

                $sheet->mergeCells('A7:B7');
                $sheet->setCellValue('A7', 'Material No');

                $sheet->getStyle("A7:G{$lastRow}")->applyFromArray([
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color'       => ['argb' => '000000'],
                        ],
                    ],
                ]);
            }
        ];
    }
}
