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

class ExportStockTaking implements WithEvents, WithCustomStartCell, FromCollection, WithColumnWidths, WithMapping
{
    public $data, $count;
    public function __construct($dt)
    {
        $this->data = $dt;
        $this->count = count($dt);
    }

    public function startCell(): string
    {
        return 'A6';
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
        ];
    }
    public function map($row): array
    {
        $char = " ";
        $no = 1;
        return [
            $no++,
            $row->material_no,
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
                $baris = 50;
                $spreadset = new Spreadsheet;

                $sheet->mergeCells('A1:H2');
                $sheet->setCellValue('A1', "PRINT STOCK TAKING");

                $sheet->mergeCells('A4:A5');
                $sheet->setCellValue('A4', "NO");

                $sheet->mergeCells('B4:B5');
                $sheet->setCellValue('B4', "MATERIAL NO");

                $sheet->mergeCells('C4:D4');
                $sheet->setCellValue('C4', "HITUNG 1");
                $sheet->setCellValue('C5', "LOC");
                $sheet->setCellValue('D5', "QTY");

                $sheet->mergeCells('E4:F4');
                $sheet->setCellValue('E4', "HITUNG 2");
                $sheet->setCellValue('E5', "LOC");
                $sheet->setCellValue('F5', "QTY");

                $sheet->mergeCells('G4:H4');
                $sheet->setCellValue('G4', "HITUNG 3");
                $sheet->setCellValue('G5', "LOC");
                $sheet->setCellValue('H5', "QTY");

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

                $sign1 = $blank + 1;
                $sheet->setCellValue('F' . $sign1, "Penghitung 1");
                $sheet->mergeCells('F' . $sign1 + 1 . ':F' . $sign1 + 4);
                $sheet->setCellValue('F' . $sign1 + 1, "  ");
                $sheet->setCellValue('F' . $sign1 + 5, "Nama: a a");
                $event->sheet->getDelegate()->getStyle("F" . $sign1 + 3)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $event->sheet->getDelegate()->getStyle("F" . $sign1 + 3)->getAlignment()->setWrapText(true);

                $sheet->setCellValue('G' . $sign1, "Penghitung 2");
                $sheet->mergeCells('G' . $sign1 + 1 . ':G' . $sign1 + 4);
                $sheet->setCellValue('G' . $sign1 + 1, "  ");
                $sheet->setCellValue('G' . $sign1 + 5, "Nama: a a");
                $event->sheet->getDelegate()->getStyle("G" . $sign1 + 3)->applyFromArray($styleArray)->getAlignment()->setWrapText(true);

                $sheet->setCellValue('H' . $sign1, "Penghitung 3");
                $sheet->mergeCells('H' . $sign1 + 1 . ':H' . $sign1 + 4);
                $sheet->setCellValue('H' . $sign1 + 1, "  ");
                $sheet->setCellValue('H' . $sign1 + 5, "Nama: a a");
                $event->sheet->getDelegate()->getStyle("H" . $sign1 + 3)->applyFromArray($styleArray)->getAlignment()->setWrapText(true);

                $sheet->getDelegate()->getStyle('F'.$sign1.':H'.$sign1)->getFont()->setBold(true);
                
                $sign2 = $sign1 + 7;
                $sheet->setCellValue('F' . $sign2, "Penghitung 4");
                $sheet->mergeCells('F' . $sign2 + 1 . ':F' . $sign2 + 4);
                $sheet->setCellValue('F' . $sign2 + 1, "  ");
                $sheet->setCellValue('F' . $sign2 + 5, "Nama: a a");
                $event->sheet->getDelegate()->getStyle("F" . $sign2 + 3)->applyFromArray($styleArray)->getAlignment()->setWrapText(true);

                $sheet->setCellValue('G' . $sign2, "Penghitung 5");
                $sheet->mergeCells('G' . $sign2 + 1 . ':G' . $sign2 + 4);
                $sheet->setCellValue('G' . $sign2 + 1, "  ");
                $sheet->setCellValue('G' . $sign2 + 5, "Nama: a a");
                $event->sheet->getDelegate()->getStyle("G" . $sign2 + 3)->applyFromArray($styleArray)->getAlignment()->setWrapText(true);

                $sheet->setCellValue('H' . $sign2, "Penghitung 6");
                $sheet->mergeCells('H' . $sign2 + 1 . ':H' . $sign2 + 4);
                $sheet->setCellValue('H' . $sign2 + 1, "  ");
                $sheet->setCellValue('H' . $sign2 + 5, "Nama: a a");
                $event->sheet->getDelegate()->getStyle("H" . $sign2 + 3)->applyFromArray($styleArray)->getAlignment()->setWrapText(true);

                $sheet->getDelegate()->getStyle('F'.$sign2.':H'.$sign2)->getFont()->setBold(true);

            }
        ];
    }
}
