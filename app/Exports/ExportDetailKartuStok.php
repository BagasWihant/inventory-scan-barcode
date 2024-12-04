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

class ExportDetailKartuStok implements WithEvents, WithCustomStartCell,FromCollection,WithColumnWidths,WithHeadings,WithMapping
{
    public $data,$count,$title;
    public function __construct($dt,$title)
    {
        $this->title = $title;
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
            'A' => 20,
            'B' => 20,
            'C' => 20,            
            'D' => 15,            
            'E' => 15,            
            'F' => 15,            
            'G' => 15,            
        ];
    }
    public function map($row): array{
        return [
            $row->Tgl ?? '-',
            $row->Trucking ?? "-",
            $row->Receive ?? "-",
            $row->{'Qty IN'},
            $row->{'Qty Out'},
            $row->Supply ?? '-',
            $row->Qty ?? '-',
        ];
    }
    public function collection()
    {
        return $this->data;
    }

    public function headings(): array
    {
        return [
            'Tanggal',
            'Trucking',
            'Receive',
            'IN',
            'OUT',
            'SUPPLY',
            'QTY',
        ];
    }
    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet;
                $baris = 0;
                $spreadset = new Spreadsheet;

                $sheet->mergeCells('A1:G3');
                $sheet->setCellValue('A1', "Material ".$this->title);

                $sheet->getDelegate()->getStyle('A1:G3')->getFont()->setSize(20);
                $sheet->getDelegate()->getStyle('A1:G5')->getFont()->setBold(true);

                $styleArray = [
                    'alignment' => [
                        'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                        'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                    ],
                ];
                $event->sheet->getDelegate()->getStyle('A4:G4')->applyFromArray($styleArray);

                $cellRange = 'A1:G3'; // All headers
                $lastRow = $this->count + $baris + 5;

                $event->sheet->getDelegate()->getStyle($cellRange)->applyFromArray($styleArray);
                $event->sheet->getDelegate()->getStyle("A5:G" . $lastRow)->applyFromArray($styleArray);

                $border = [
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color' => ['argb' => '000000'],
                        ],
                    ],
                ];
                $event->sheet->getDelegate()->getStyle("A4:G" . $lastRow)->applyFromArray($border);

                // for ($i = 1; $i <= $this->count + $baris; $i++) {
                //     $sheet->setCellValue('A' . 5 + $i, $i);
                // }

                // $blank = $lastRow + 5;
                // for ($i = $lastRow + 1; $i <= $blank; $i++) {
                //     $sheet->setCellValue('A' . $i, "  ");
                // }


            }
        ];
    }
}
