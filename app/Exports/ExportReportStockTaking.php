<?php

namespace App\Exports;

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

class ExportReportStockTaking implements WithEvents, WithCustomStartCell,FromCollection,WithColumnWidths,WithHeadings,WithMapping
{
    public $data,$count;
    public function __construct($dt)
    {
        $this->data = $dt;
        $this->count = count($dt);
    }
    public function startCell(): string
    {
        return 'A5';
    }

    public function columnWidths(): array
    {
        return [
            'A' => 3,
            'B' => 22,
            'C' => 22,            
            'D' => 15,            
            'E' => 22,            
            'F' => 15,            
            'G' => 15,            
        ];
    }
    public function map($row): array{
        return [
            $row->material_no,
            $row->material_no,
            $row->loc_sys,
            $row->qty_sys,
            $row->loc_sto,
            $row->qty_sto,
            $row->result_qty >0 ? $row->result_qty : '  ',
            $row->result_qty <0 ? abs($row->result_qty) : '  ',
        ];
    }
    public function collection()
    {
        return $this->data;
    }

    public function headings(): array
    {
        return [
            'No',
            'Material No',
            'LOC',
            'QTY',
            'LOC',
            'QTY',
            'Result',
        ];
    }
    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet;
                $baris = 0;
                $spreadset = new Spreadsheet;

                $sheet->mergeCells('A1:H2');
                $sheet->setCellValue('A1', "STOCK TAKING CONFIRMATION");

                $sheet->mergeCells('A4:A5');
                $sheet->setCellValue('A4', "NO");

                $sheet->mergeCells('B4:B5');
                $sheet->setCellValue('B4', "MATERIAL NO");

                $sheet->mergeCells('C4:D4');
                $sheet->setCellValue('C4', "SYSTEM");
                $sheet->setCellValue('C5', "LOC");
                $sheet->setCellValue('D5', "QTY");

                $sheet->mergeCells('E4:F4');
                $sheet->setCellValue('E4', "STO");
                $sheet->setCellValue('E5', "LOC");
                $sheet->setCellValue('F5', "QTY");
                
                $sheet->mergeCells('G4:H4');
                $sheet->setCellValue('G4', "RESULT");
                $sheet->setCellValue('G5', "+");
                $sheet->setCellValue('H5', "-");

                $sheet->getDelegate()->getStyle('A1:H2')->getFont()->setSize(20);
                $sheet->getDelegate()->getStyle('A1:H5')->getFont()->setBold(true);

                $styleArray = [
                    'alignment' => [
                        'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                        'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                    ],
                ];
                $event->sheet->getDelegate()->getStyle('G4:H4')->applyFromArray($styleArray);
                $event->sheet->getDelegate()->getStyle('E4:F4')->applyFromArray($styleArray);
                $event->sheet->getDelegate()->getStyle('C4:D4')->applyFromArray($styleArray);

                $cellRange = 'A1:G3'; // All headers
                $lastRow = $this->count + $baris + 5;

                $event->sheet->getDelegate()->getStyle($cellRange)->applyFromArray($styleArray);
                $event->sheet->getDelegate()->getStyle("A5:H" . $lastRow)->applyFromArray($styleArray);

                $border = [
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color' => ['argb' => '000000'],
                        ],
                    ],
                ];
                $event->sheet->getDelegate()->getStyle("A4:H" . $lastRow)->applyFromArray($border);

                for ($i = 1; $i <= $this->count + $baris; $i++) {
                    $sheet->setCellValue('A' . 5 + $i, $i);
                }

                $blank = $lastRow + 5;
                for ($i = $lastRow + 1; $i <= $blank; $i++) {
                    $sheet->setCellValue('A' . $i, "  ");
                }


            }
        ];
    }

}
