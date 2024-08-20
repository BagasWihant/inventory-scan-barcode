<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use Maatwebsite\Excel\Concerns\WithDrawings;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;

class PaletRegisterExport implements FromCollection, WithMapping, WithHeadings, WithEvents, WithCustomStartCell, WithColumnWidths,WithDrawings
{
    public $barcode, $data, $line, $palet_no,$count;
    public function __construct($dataArray)
    {
        $this->data = $dataArray['data'];
        $this->count = count($dataArray['data']);
        $this->line = $dataArray['line'];
        $this->palet_no = $dataArray['palet_no'];
    }

    public function startCell(): string
    {
        return 'A7';
    }

    public function columnWidths(): array
    {
        return [
            'A' => 5,
            'B' => 30,
            'C' => 20,
            'D' => 20,
        ];
    }
    public function collection()
    {
        return $this->data;
    }

    public function map($row): array
    {
        $char = "   ";
        return [
            $char,
            $row->material_no,
            $row->material_name,
            $row->qty,
        ];
    }

    public function headings(): array
    {
        return [
            'No',
            'Material no',
            'Material Name',
            'Qty',
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $max_col = "D";
                $baris=0;
                $sheet = $event->sheet;

                $sheet->mergeCells("A1:".$max_col."1");
                $sheet->setCellValue('A1', "Register Palet");
                
                $sheet->mergeCells("A2:".$max_col."2");
                // $sheet->setCellValue('A2', Carbon::now('Asia/Jakarta')->format('l, j F Y H:i:s'));
                $sheet->setCellValue('A3', "   ");
                
                $sheet->mergeCells("A4:B4");
                $sheet->setCellValue('A4', "Pallet No");
                $sheet->mergeCells("C4:E4");
                $sheet->mergeCells("A5:B5");
                $sheet->setCellValue('A5', "Line CD");
                $sheet->setCellValue('C5', ": " . $this->line);
                
                $sheet->setCellValue('A6', "  ");
                $sheet->getDelegate()->getStyle('A1:'.$max_col.'1')->getFont()->setSize(20);
                $sheet->getDelegate()->getStyle('A1:'.$max_col.'7')->getFont()->setBold(true);


                $styleArray = [
                    'alignment' => [
                        'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                        'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                    ],
                ];

                $cellRange = 'A1:K3'; // All headers
                // $event->sheet->getDelegate()->getStyle($cellRange)->applyFromArray($styleArray);
                // $event->sheet->getDelegate()->getStyle("A7:K" . $this->count + 7)->applyFromArray($styleArray);

                $border = [
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color' => ['argb' => '000000'],
                        ],
                    ],
                ];

                for ($i = 1; $i <= $this->count + $baris; $i++) {
                    $sheet->setCellValue('A' . 7 + $i, $i);
                }

                $event->sheet->getDelegate()->getStyle("A7:". $max_col . $this->count + 7)->applyFromArray($border);
                $event->sheet->getDelegate()->getStyle('A7:' . $max_col . $this->count + 7)->applyFromArray($styleArray);
                $event->sheet->getDelegate()->getStyle('A1:'.$max_col.'2')->applyFromArray($styleArray);
            }
        ];
    }

    public function drawings()
    {

        $drawing = new Drawing();
        $drawing->setName('Logo');
        $drawing->setDescription('This is my logo');
        $drawing->setPath(public_path('storage/barcodes/'.$this->palet_no.'.png'));
        $drawing->setHeight(40);
        $drawing->setWidth(300);
        $drawing->setCoordinates('C4');
        return $drawing;
    }
}
