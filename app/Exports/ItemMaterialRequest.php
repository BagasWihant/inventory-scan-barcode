<?php

namespace App\Exports;

use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Border;

class ItemMaterialRequest implements FromCollection, WithEvents, WithCustomStartCell, WithMapping, WithHeadings, WithColumnWidths
{
    public $data, $count;
    public function __construct($data)
    {
        $this->data = $data;
        $this->count = count($data);
    }
    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        return $this->data;
    }
    public function startCell(): string
    {
        return 'A5';
    }




    public function columnWidths(): array
    {
        return [
            'A' => 8,
            'B' => 40,
            'C' => 40,
            'D' => 20,
            'E' => 10,
            'F' => 15,
            'G' => 10,
            'H' => 20,
            'I' => 25,
        ];
    }

    public function headings(): array
    {
        return [
            'No',
            'Material No',
            'Material Name',
            'Date',
            'Qty',
            'Qty Issue',
            'Unit',
            'Location',
            'Request',
        ];
    }

    public function map($row): array
    {
        $char = "   ";

        return [
            $char,
            $row->material_no ?? " ",
            $row->material_name ?? " ",
            Carbon::parse($row->created_at)->format('d-m-Y') ?? " ",
            $row->request_ty ?? " ",
            $row->iss_min_lot ?? " ",
            $row->iss_unit ?? " ",
            $row->loc_cd ?? " ",
            $row->user_request ?? " ",
        ];
    }
    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet;

                $sheet->mergeCells('B1:I1');
                $sheet->setCellValue('B1', "MATERIAL REQUEST");
                $sheet->mergeCells('A2:C2');
                $sheet->setCellValue('A2', "Transaksi No : ".$this->data[0]->transaksi_no);
                $sheet->mergeCells('A3:C3');
                $sheet->setCellValue('A3', "Print Date   : ".date('d-m-Y H:i'));
                $sheet->setCellValue('A4', " ");


                $sheet->getDelegate()->getStyle('B1:I1')->getFont()->setSize(20);
                $sheet->getDelegate()->getStyle('B1:I3')->getFont()->setBold(true);

                $styleArray = [
                    'alignment' => [
                        'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                        'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                    ],
                ];
                for ($i = 1; $i <= $this->count; $i++) {
                    $sheet->setCellValue('A' . 5 + $i, $i);
                }

                $cellRange = 'A1:I1'; // All headers
                $event->sheet->getDelegate()->getStyle($cellRange)->applyFromArray($styleArray);

                $border = [
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color' => ['argb' => '000000'],
                        ],
                    ],
                ];
                $event->sheet->getDelegate()->getStyle("A5:I" . (5 + $this->count))->applyFromArray($border);
                $event->sheet->getDelegate()->getStyle("A5:I" . (5 + $this->count))->applyFromArray($styleArray);


                $lastRow = $this->count + 8;
                for ($i = $this->count+6; $i <= $this->count+18; $i++) {
                    $sheet->setCellValue('H' . $i, "   ");
                }

                $sign1 = $lastRow + 3;
                $sheet->setCellValue('G' . $sign1 - 1, "   ");
                // $sheet->setCellValue('D'.$lastRow+1, "   ");


                $sheet->mergeCells('E' . $sign1 . ':F' . $sign1 );
                $sheet->setCellValue('E' . $sign1, " Check by");
                $sheet->mergeCells('E' . $sign1 + 1 . ':F' . $sign1 + 4);
                $sheet->setCellValue('F' . $sign1 + 1, "  ");
                $sheet->mergeCells('E' . $sign1 + 5 . ':F' . $sign1 + 5);
                $sheet->mergeCells('E' . $sign1 + 6 . ':F' . $sign1 + 6);
                $sheet->setCellValue('E' . $sign1 + 5, " Name :");
                $sheet->setCellValue('E' . $sign1 + 6, " Date :");

                $event->sheet->getDelegate()->getStyle("E" . $sign1 . ":F" . $sign1 + 6)->applyFromArray($border);

                $sheet->mergeCells('G' . $sign1. ':H' . $sign1);
                $sheet->setCellValue('G' . $sign1, " Prepared by");
                $sheet->mergeCells('G' . $sign1 + 1 . ':H' . $sign1 + 4);
                $sheet->setCellValue('G' . $sign1 + 1, "  ");

                $sheet->mergeCells('G' . $sign1 + 5 . ':H' . $sign1 + 5);
                $sheet->mergeCells('G' . $sign1 + 6 . ':H' . $sign1 + 6);
                $sheet->setCellValue('G' . $sign1 + 5, " Name :");
                $sheet->setCellValue('G' . $sign1 + 6, " Date :");

                $event->sheet->getDelegate()->getStyle("G" . $sign1 . ":H" . $sign1 + 6)->applyFromArray($border);

                $sheet->setCellValue('I' . $sign1, " Checked by");
                $sheet->mergeCells('I' . $sign1 + 1 . ':I' . $sign1 + 4);
                $sheet->setCellValue('I' . $sign1 + 1, "  ");
                $sheet->setCellValue('I' . $sign1 + 5, " Name : ");
                $sheet->setCellValue('I' . $sign1 + 6, " Date : ");
                $event->sheet->getDelegate()->getStyle("I" . $sign1 . ":I" . $sign1 + 6)->applyFromArray($border);


                $sheet->getDelegate()->getStyle('D' . $sign1 . ':I' . $sign1)->getFont()->setBold(true);
                $event->sheet->getDelegate()->getStyle('A1:I' . $sign1)->getFont()->setSize(16);
            }
        ];
    }
}
