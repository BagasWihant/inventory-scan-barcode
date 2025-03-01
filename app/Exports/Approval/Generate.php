<?php

namespace App\Exports\Approval;


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

class Generate implements WithMapping, WithHeadings, WithEvents, WithCustomStartCell, WithColumnWidths, FromCollection, WithDrawings
{

    public $data;
    public $detail;
    public $count;
    public $startRow;
    public $rowBarcode;
    public function __construct($data)
    {
        $this->data = $data;
        $this->detail = $data->detail;
        $this->count = count($data->detail);
    }

    public function collection()
    {
        return $this->detail;
    }

    public function startCell(): string
    {
        $this->startRow = 1;
        return 'A1';
    }

    public function columnWidths(): array
    {
        return [
            'A' => 5,
            'B' => 30,
            'C' => 20,
            'D' => 25,
            'E' => 25,
            'F' => 25,
            'G' => 20,
        ];
    }

    public function headings(): array
    {
        return [
            'No',
            'Uraian',
            'No Item Master',
            'Qty',
            'Satuan',
            'Tanggal Permintaan Kedatangan Barang',
            'Keterangan',
        ];
    }

    public function map($row): array
    {
        $char = "  ";
        return [
            $char,
            $row->item_brg,
            $row->item_id,
            $row->qty,
            $row->satuan,
            $char,
            $row->alasan_beli,
        ];
    }


    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet;
                $maxCol = "G";
                $startRow = $this->startRow;

                $center = [
                    'alignment' => [
                        'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                        'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
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
                $borderOut = [
                    'borders' => [
                        'outline' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color' => ['argb' => '000000'],
                        ],
                    ],
                ];

                $baris = 0;
                for ($i = 1; $i <= $this->count + $baris; $i++) {
                    $sheet->setCellValue('A' . $startRow + $i, $i);
                }


                $event->sheet->getDelegate()->getStyle("A" . $startRow . ":" . $maxCol . $this->count + $startRow)->applyFromArray($border);
                $event->sheet->getDelegate()->getStyle("A" . $startRow . ":" . $maxCol . $this->count + $startRow)->applyFromArray($center);

                $lastRow = $this->count + $startRow;
                $sign1 = $lastRow + 3;
                $this->rowBarcode = $sign1 + 1;

                $sheet->setCellValue('D' . $sign1, " Dibuat Oleh");
                $sheet->mergeCells('D' . $sign1 + 1 . ':D' . $sign1 + 4);
                $sheet->setCellValue('D' . $sign1 + 1, " ");
                $event->sheet->getDelegate()->getStyle("D" . $sign1 . ":D" . $sign1)->applyFromArray($border);
                $event->sheet->getDelegate()->getStyle("D" . $sign1 + 1 . ":D" . $sign1 + 5)->applyFromArray($borderOut);
                $sheet->setCellValue('D' . $sign1 + 5, "Nama:
                \nNIP  ");

                $sheet->setCellValue('E' . $sign1, " Diperiksa Oleh");
                $sheet->mergeCells('E' . $sign1 + 1 . ':E' . $sign1 + 4);
                $sheet->setCellValue('E' . $sign1 + 1, "  ");
                $event->sheet->getDelegate()->getStyle("E" . $sign1 . ":E" . $sign1)->applyFromArray($border);
                $event->sheet->getDelegate()->getStyle("E" . $sign1 + 1 . ":E" . $sign1 + 5)->applyFromArray($borderOut);
                $sheet->setCellValue('E' . $sign1 + 5, "Nama:
                \nNIP  ");

                $sheet->setCellValue('F' . $sign1, " Disetujui Oleh");
                $sheet->mergeCells('F' . $sign1 + 1 . ':F' . $sign1 + 4);
                $sheet->setCellValue('F' . $sign1 + 1, "  ");
                $event->sheet->getDelegate()->getStyle("F" . $sign1 . ":F" . $sign1)->applyFromArray($border);
                $event->sheet->getDelegate()->getStyle("F" . $sign1 + 1 . ":F" . $sign1 + 5)->applyFromArray($borderOut);
                $sheet->setCellValue('F' . $sign1 + 5, "Nama:
                \nNIP  ");

                $sheet->getStyle("D" . $sign1+1 . ":F" . $sign1 + 1)->applyFromArray([
                    'alignment' => [
                        'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                        'vertical'   => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                    ],
                ]);
            }
        ];
    }

    public function drawings()
    {
        $drawings = [];

        foreach (['D', 'E', 'F'] as $column) {

            $drawing = new Drawing();
            $drawing->setName('Barcode');
            $drawing->setDescription('Generated Barcode');
            $drawing->setPath($this->data->barcode);
            $drawing->setCoordinates($column . ($this->count + 5));
            $drawing->setWidth(150);
            $drawings[] = $drawing;
        }
        return $drawings;
    }
}
