<?php

namespace App\Exports;

use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
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
use Picqer\Barcode\BarcodeGeneratorPNG;

class ReceivingSupplierReport implements FromCollection, WithMapping, WithHeadings, WithEvents, WithCustomStartCell, WithColumnWidths, WithDrawings
{
    public $data, $count, $palet_no, $line_c, $issue_date;
    public function __construct($dt)
    {
        $this->palet_no = $dt['palet_no'];
        $this->data = collect($dt['data'])->where('counter', '>', 0);
        $this->count = count($this->data);
        $this->issue_date = $dt['issue_date'];
        $this->line_c = $dt['line_c'];
    }
    public function startCell(): string
    {
        return 'A8';
    }
    public function columnWidths(): array
    {
        return [
            'A' => 5,
            'B' => 30,
            'C' => 30,
            'D' => 25,
            'E' => 25,
            'F' => 25,
        ];
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        return $this->data;
    }
    public function map($row): array
    {
        $char = "  ";
        return [
            $char,
            $row->material,
            $row->matl_nm,
            $row->counter,
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
                $sheet = $event->sheet;

                // $sheet->mergeCells("A1:".$max_col."1");
                // $sheet->setCellValue('A1', "RECEIVING SUPPLIER REPORT");

                $sheet->mergeCells("A1:" . $max_col . "1");
                $sheet->setCellValue('A1', Carbon::now('Asia/Jakarta')->format('l, j F Y H:i:s'));
                $sheet->setCellValue('A3', "   ");

                $sheet->mergeCells("A4:B4");
                $sheet->setCellValue('A4', "Issue Date");
                $sheet->setCellValue('C4', ": " . $this->issue_date);

                $sheet->mergeCells("A5:B5");
                $sheet->setCellValue('A5', "Line Code");
                $sheet->setCellValue('C5', ": " . $this->line_c);

                $sheet->mergeCells("A6:B6");
                $sheet->setCellValue('A6', "Pallet No");
                $sheet->setCellValue('C6', ": " . $this->palet_no);

                $sheet->mergeCells("D4:F6");
                $sheet->setCellValue('A7', "  ");
                $sheet->getDelegate()->getStyle('A1:' . $max_col . '8')->getFont()->setBold(true);


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

                $baris = 0;
                for ($i = 1; $i <= $this->count + $baris; $i++) {
                    $sheet->setCellValue('A' . 8 + $i, $i);
                }


                $event->sheet->getDelegate()->getStyle("A8:" . $max_col . $this->count + 8)->applyFromArray($border);
                $event->sheet->getDelegate()->getStyle('A8:' . $max_col . $this->count + 8)->applyFromArray($styleArray);

                $lastRow = $this->count + 8;

                $sign1 = $lastRow + 3;
                $sheet->setCellValue('D' . $sign1 - 1, "   ");
                // $sheet->setCellValue('D'.$lastRow+1, "   ");

                $sheet->setCellValue('D' . $sign1, " Created by");
                $sheet->mergeCells('D' . $sign1 + 1 . ':D' . $sign1 + 4);
                $sheet->setCellValue('D' . $sign1 + 1, "  ");
                $event->sheet->getDelegate()->getStyle("D" . $sign1 . ":D" . $sign1 + 5)->applyFromArray($border);

                $sheet->setCellValue('E' . $sign1, " Supply by");
                $sheet->mergeCells('E' . $sign1 + 1 . ':E' . $sign1 + 4);
                $sheet->setCellValue('E' . $sign1 + 1, "  ");
                $event->sheet->getDelegate()->getStyle("E" . $sign1 . ":E" . $sign1 + 5)->applyFromArray($border);

                $sheet->setCellValue('F' . $sign1, " Received by");
                $sheet->mergeCells('F' . $sign1 + 1 . ':F' . $sign1 + 4);
                $sheet->setCellValue('F' . $sign1 + 1, "  ");
                $event->sheet->getDelegate()->getStyle("F" . $sign1 . ":F" . $sign1 + 5)->applyFromArray($border);

                $sheet->getDelegate()->getStyle('D' . $sign1 . ':F' . $sign1)->getFont()->setBold(true);
                $event->sheet->getDelegate()->getStyle('A1:F' . $sign1)->getFont()->setSize(18);
            }
        ];
    }

    public function drawings()
    {

        $drawing = new Drawing();
        $drawing->setName('Logo');
        $drawing->setDescription('This is my logo');
        if ($this->palet_no != '-') {
            if (!file_exists(public_path('storage/barcodes/' . $this->palet_no . '.png'))) {
                $generator = new BarcodeGeneratorPNG ();
                $barcode = $generator->getBarcode($this->palet_no, $generator::TYPE_CODE_128);
                Storage::put('public/barcodes/' . $this->palet_no . '.png', $barcode); 
            }
            $drawing->setPath(public_path('storage/barcodes/' . $this->palet_no . '.png'));
            $drawing->setHeight(60);
            $drawing->setWidth(350);
        } else {
            $drawing->setPath(public_path('no-qr.png'));
            $drawing->setHeight(30);
            $drawing->setWidth(175);
        }
        $drawing->setCoordinates('D4');
        return $drawing;
    }
}
