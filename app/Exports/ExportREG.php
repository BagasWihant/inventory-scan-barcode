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

class ExportREG implements FromCollection, WithEvents, WithCustomStartCell, WithMapping, WithHeadings, WithColumnWidths
{

    public $data, $count,$no_sj,$qtySupply;
    public function __construct($data,$no_sj, $qtySupply)
    {
        $this->data = $data;
        $this->no_sj = $no_sj;
        $this->qtySupply = $qtySupply;
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
        return 'A7';
    }




    public function columnWidths(): array
    {
        return [
            'A' => 8,
            'B' => 40,
            'C' => 40,
            'D' => 20
        ];
    }

    public function headings(): array
    {
        return [
            'No',
            'Material No',
            'Material Name',
            'Qty Supply',
        ];
    }

    public function map($row): array
    {
        $char = "   ";

        return [
            $char,
            $row->material_no ?? " ",
            $row->material_name ?? " ",
            $row->qty_supply ?? " "
        ];
    }
    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet;

                $sheet->mergeCells('B1:D1');
                $sheet->setCellValue('B1', "MATERIAL REQUEST");
                $sheet->mergeCells('A2:C2');

                $sheet->setCellValue('A2', "Transaksi No : ".$this->data[0]->transaksi_no);
                $sheet->mergeCells('G2:D2');
                
                $sheet->mergeCells('A3:B3');
                $sheet->setCellValue('A3', "Tanggal Produksi");
                $sheet->setCellValue('A4', $this->data[0]->issue_date);
                $sheet->setCellValue('C3', "Line Code");
                $sheet->setCellValue('C4', $this->data[0]->line_c);
                $sheet->mergeCells('D3:D3');
                $sheet->setCellValue('D3', "Product model");
                $sheet->setCellValue('D4', $this->data[0]->product_model);
                // $sheet->setCellValue('D3', "Product model");
                $sheet->mergeCells('A5:B5');
                $sheet->setCellValue('A5', 'Total Supply : '. $this->qtySupply);


                $sheet->getDelegate()->getStyle('B1:D1')->getFont()->setSize(20);
                $sheet->getDelegate()->getStyle('B1:D3')->getFont()->setBold(true);

                $styleArray = [
                    'alignment' => [
                        'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                        'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                    ],
                ];
                for ($i = 1; $i <= $this->count; $i++) {
                    $sheet->setCellValue('A' . 7 + $i, $i);
                }

                $cellRange = 'A1:D1'; // All headers
                $event->sheet->getDelegate()->getStyle($cellRange)->applyFromArray($styleArray);

                $border = [
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color' => ['argb' => '000000'],
                        ],
                    ],
                ];
                $event->sheet->getDelegate()->getStyle("A7:D" . (7 + $this->count))->applyFromArray($border);
                $event->sheet->getDelegate()->getStyle("A7:D" . (7 + $this->count))->applyFromArray($styleArray);


                $lastRow = $this->count + 8;
                // for ($i = $this->count+6; $i <= $this->count+18; $i++) {
                //     $sheet->setCellValue('H' . $i, "   ");
                // }

                $sign1 = $lastRow + 3;
                // $sheet->setCellValue('G' . $sign1 - 1, "   ");
                // $sheet->setCellValue('D'.$lastRow+1, "   ");


                $sheet->setCellValue('B' . $sign1, " Check by");
                $sheet->mergeCells('B' . $sign1 + 1 . ':B' . $sign1 + 4);
                $sheet->setCellValue('B' . $sign1 + 1, "  ");
                $sheet->setCellValue('B' . $sign1 + 5, " Name :");
                $sheet->setCellValue('B' . $sign1 + 6, " Date :");

                $event->sheet->getDelegate()->getStyle("B" . $sign1 . ":B" . $sign1 + 6)->applyFromArray($border);


                $sheet->setCellValue('C' . $sign1, " Prepared by");
                $sheet->mergeCells('C' . $sign1 + 1 . ':C' . $sign1 + 4);
                $sheet->setCellValue('C' . $sign1 + 1, "  ");
                $sheet->setCellValue('C' . $sign1 + 5, " Name :");
                $sheet->setCellValue('C' . $sign1 + 6, " Date :");
                $event->sheet->getDelegate()->getStyle("C" . $sign1 . ":C" . $sign1 + 6)->applyFromArray($border);

                $sheet->mergeCells('D' . $sign1. ':D' . $sign1);
                $sheet->setCellValue('D' . $sign1, " Checked by");
                $sheet->mergeCells('D' . $sign1 + 1 . ':D' . $sign1 + 4);
                $sheet->setCellValue('D' . $sign1 + 1, "  ");
                $sheet->setCellValue('D' . $sign1 + 5, " Name :");
                $sheet->setCellValue('D' . $sign1 + 6, " Date :");
                $event->sheet->getDelegate()->getStyle("D" . $sign1 . ":D" . $sign1 + 6)->applyFromArray($border);

                // $sheet->setCellValue('G' . $sign1, " Prepared by");
                // $sheet->mergeCells('G' . $sign1 + 1 . ':H' . $sign1 + 4);
                // $sheet->setCellValue('G' . $sign1 + 1, "  ");

                // $sheet->mergeCells('G' . $sign1 + 5 . ':H' . $sign1 + 5);
                // $sheet->mergeCells('G' . $sign1 + 6 . ':H' . $sign1 + 6);
                // $sheet->setCellValue('G' . $sign1 + 5, " Name :");
                // $sheet->setCellValue('G' . $sign1 + 6, " Date :");

                // $event->sheet->getDelegate()->getStyle("G" . $sign1 . ":H" . $sign1 + 6)->applyFromArray($border);

                // $sheet->setCellValue('I' . $sign1, " Checked by");
                // $sheet->mergeCells('I' . $sign1 + 1 . ':I' . $sign1 + 4);
                // $sheet->setCellValue('I' . $sign1 + 1, "  ");
                // $sheet->setCellValue('I' . $sign1 + 5, " Name : ");
                // $sheet->setCellValue('I' . $sign1 + 6, " Date : ");
                // $event->sheet->getDelegate()->getStyle("I" . $sign1 . ":I" . $sign1 + 6)->applyFromArray($border);


                $sheet->getDelegate()->getStyle('B' . $sign1 . ':D' . $sign1)->getFont()->setBold(true);
                $event->sheet->getDelegate()->getStyle('A1:D' . $sign1)->getFont()->setSize(16);
            }
        ];
    }
}