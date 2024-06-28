<?php

namespace App\Exports;

use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use Maatwebsite\Excel\Concerns\WithEvents;
use PhpOffice\PhpSpreadsheet\Style\Border;
use Maatwebsite\Excel\Concerns\WithMapping;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use Maatwebsite\Excel\Concerns\FromCollection;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use Maatwebsite\Excel\Concerns\WithDrawings;

class ExportStockTaking implements WithEvents, WithCustomStartCell, FromCollection, WithColumnWidths, WithMapping
{
    public $data, $count,$sto_id;
    public function __construct($dt,$sto_id)
    {
        $this->data = $dt;
        $this->sto_id = $sto_id;
        $this->count = count($dt);
    }

    public function startCell(): string
    {
        return 'A8';
    }

    public function columnWidths(): array
    {
        return [
            'A' => 3,
            'B' => 23,
            'C' => 20,
            'D' => 20,
            'E' => 20,
            'F' => 20,
            'G' => 20,
            'H' => 20,
            'I' => 20,
        ];
    }
    public function map($row): array
    {
        $char = " ";
        $no = 1;
        return [
            $no++,
            $row->material_no,
            $row->locate,
            $char,
            $char,

            $char,
            $char,

            $char,
            $char,
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
            ' ',
            'Location',
            'LOC',
            'QTY',
            'LOC',
            'QTY',
            'LOC',
            'QTY'
        ];
    }

    public function drawings()
    {
        $drawing = new Drawing();
        $drawing->setName('Logo');
        $drawing->setDescription('This is my logo');
        $drawing->setPath(public_path('/assets/sws.jpg'));
        $drawing->setHeight(50);
        $drawing->setCoordinates('H3');
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
                $baris = 50;
                $spreadset = new Spreadsheet;

                $sheet->mergeCells('A1:I1');
                $sheet->setCellValue('A1', "PRINT STOCK TAKING");

                $sheet->mergeCells('A2:B2');
                $sheet->setCellValue('A2', "Date");

                $sheet->mergeCells('C2:D2');
                $sheet->setCellValue('C2', ": " . date('d-m-Y'));

                $sheet->mergeCells('A3:B3');
                $sheet->setCellValue('A3', "Stock Taking ID ");
                $sheet->setCellValue('A4', "  ");
                $sheet->setCellValue('C3',  ": ".$this->sto_id);

                $left = [
                    'alignment' => [
                        'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT,
                        'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT,
                    ],
                ];
                $event->sheet->getDelegate()->getStyle('A2:H3')->applyFromArray($left);
                

                $sheet->mergeCells('A5:A6');
                $sheet->setCellValue('A5', "NO");

                $sheet->mergeCells('B5:B6');
                $sheet->setCellValue('B5', "MATERIAL NO");
                $sheet->mergeCells('C5:C6');
                $sheet->setCellValue('C5', "Location");

                $sheet->mergeCells('D5:E5');
                $sheet->setCellValue('D5', "HITUNG 1");
                $sheet->setCellValue('D6', "LOC");
                $sheet->setCellValue('E6', "QTY");

                $sheet->mergeCells('F5:G5');
                $sheet->setCellValue('F5', "HITUNG 2");
                $sheet->setCellValue('F6', "LOC");
                $sheet->setCellValue('G6', "QTY");

                $sheet->mergeCells('H5:I5');
                $sheet->setCellValue('H5', "HITUNG 3");
                $sheet->setCellValue('H6', "LOC");
                $sheet->setCellValue('I6', "QTY");

                $sheet->getDelegate()->getStyle('A1:H1')->getFont()->setSize(20);
                $sheet->getDelegate()->getStyle('A1:H6')->getFont()->setBold(true);

                $styleArray = [
                    'alignment' => [
                        'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                        'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                    ],
                ];
                $event->sheet->getDelegate()->getStyle('D5:I5')->applyFromArray($styleArray);

                $cellRange = 'A1:G1'; // All headers
                $lastRow = $this->count + $baris + 6;

                $event->sheet->getDelegate()->getStyle($cellRange)->applyFromArray($styleArray);
                $event->sheet->getDelegate()->getStyle("A5:I" . $lastRow)->applyFromArray($styleArray);

                $border = [
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color' => ['argb' => '000000'],
                        ],
                    ],
                ];
                $event->sheet->getDelegate()->getStyle("A5:I" . $lastRow)->applyFromArray($border);

                for ($i = 1; $i <= $this->count + $baris; $i++) {
                    $sheet->setCellValue('A' . 7 + $i, $i);
                }

                $blank = $lastRow + 10;
                for ($i = $lastRow + 1; $i <= $blank; $i++) {
                    $sheet->setCellValue('A' . $i, "  ");
                }

                $sign1 = $blank + 1;
                $sheet->setCellValue('F' . $sign1, " Penghitung 1");
                $sheet->mergeCells('F' . $sign1 + 1 . ':F' . $sign1 + 4);
                $sheet->setCellValue('F' . $sign1 + 1, "  ");
                $sheet->setCellValue('F' . $sign1 + 5, " Nama : ");
                $event->sheet->getDelegate()->getStyle("F" . $sign1)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER)->setWrapText(true);
                $event->sheet->getDelegate()->getStyle("F".$sign1.":F" . $sign1+5)->applyFromArray($border);


                $sheet->setCellValue('G' . $sign1, "Penghitung 2");
                $sheet->mergeCells('G' . $sign1 + 1 . ':G' . $sign1 + 4);
                $sheet->setCellValue('G' . $sign1 + 1, "  ");
                $sheet->setCellValue('G' . $sign1 + 5, " Nama :");
                $event->sheet->getDelegate()->getStyle("G" . $sign1)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER)->setWrapText(true);
                $event->sheet->getDelegate()->getStyle("G".$sign1.":G" . $sign1+5)->applyFromArray($border);

                $sheet->setCellValue('H' . $sign1, "Penghitung 3");
                $sheet->mergeCells('H' . $sign1 + 1 . ':H' . $sign1 + 4);
                $sheet->setCellValue('H' . $sign1 + 1, "  ");
                $sheet->setCellValue('H' . $sign1 + 5, " Nama :");
                $event->sheet->getDelegate()->getStyle("H" . $sign1)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER)->setWrapText(true);
                $event->sheet->getDelegate()->getStyle("H".$sign1.":H" . $sign1+5)->applyFromArray($border);


                $sheet->getDelegate()->getStyle('F'.$sign1.':H'.$sign1)->getFont()->setBold(true);
                
                $sign2 = $sign1 + 7;
                $sheet->setCellValue('F' . $sign2, "Penghitung 4");
                $sheet->mergeCells('F' . $sign2 + 1 . ':F' . $sign2 + 4);
                $sheet->setCellValue('F' . $sign2 + 1, "  ");
                $sheet->setCellValue('F' . $sign2 + 5, " Nama : ");
                $event->sheet->getDelegate()->getStyle("F" . $sign2)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER)->setWrapText(true);
                $event->sheet->getDelegate()->getStyle("F".$sign2.":F" . $sign2+5)->applyFromArray($border);


                $sheet->setCellValue('G' . $sign2, "Penghitung 5");
                $sheet->mergeCells('G' . $sign2 + 1 . ':G' . $sign2 + 4);
                $sheet->setCellValue('G' . $sign2 + 1, "  ");
                $sheet->setCellValue('G' . $sign2 + 5, " Nama :");
                $event->sheet->getDelegate()->getStyle("G" . $sign2)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER)->setWrapText(true);
                $event->sheet->getDelegate()->getStyle("G".$sign2.":G" . $sign2+5)->applyFromArray($border);


                $sheet->setCellValue('H' . $sign2, "Penghitung 6");
                $sheet->mergeCells('H' . $sign2 + 1 . ':H' . $sign2 + 4);
                $sheet->setCellValue('H' . $sign2 + 1, "  ");
                $sheet->setCellValue('H' . $sign2 + 5, " Nama :");
                $event->sheet->getDelegate()->getStyle("H" . $sign2)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER)->setWrapText(true);
                $event->sheet->getDelegate()->getStyle("H".$sign2.":H" . $sign2+5)->applyFromArray($border);

                $sheet->getDelegate()->getStyle('F'.$sign2.':H'.$sign2)->getFont()->setBold(true);

            }
        ];
    }
}
