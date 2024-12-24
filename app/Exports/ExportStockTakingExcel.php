<?php

namespace App\Exports;

use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Border;

class ExportStockTakingExcel implements FromCollection, WithMapping, WithHeadings, WithEvents, WithCustomStartCell
{
    public $data;
    public $lengData;
    public function __construct($data)
    {
        $this->data = $data;
        $this->lengData = count($data);
    }
    public function collection()
    {
        return $this->data;
    }

    public function map($row): array
    {
        $char = "  ";
        return [
            $char,
            $row->line_code,
            $row->material_no,
            $row->material_name,
            $row->qty,
            $row->palet_no,
            $row->location,
        ];
    }

    public function headings(): array
    {
        return [
            'No',
            'Line Code',
            'Material No',
            'Material Name',
            'Qty',
            'Palet No',
            'Location',
        ];
    }

    public function startCell(): string
    {
        return 'A5';
    }
    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $max_col = "G";
                $sheet = $event->sheet;

                // $sheet->mergeCells("A1:".$max_col."1");
                // $sheet->setCellValue('A1', "RECEIVING SUPPLIER REPORT");

                $sheet->mergeCells("A1:" . $max_col . "1");
                $sheet->setCellValue('A1', 'Stock Taking COT');
                $sheet->mergeCells("A2:" . $max_col . "2");
                $sheet->setCellValue('A2', 'Print date : ' . Carbon::now('Asia/Jakarta')->format('j F Y H:i:s'));
                $sheet->setCellValue('A3', "   ");
                $sheet->getDelegate()->getStyle('A1:' . $max_col . '1')->getFont()->setSize(18)->setBold(true);



                $styleArray = [
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
                $cellRange = 'A1:' . $max_col . $this->lengData + 5; // All headers
                $cellData = 'A5:' . $max_col . $this->lengData + 5; // All headers
                $event->sheet->getDelegate()->getStyle($cellRange)->applyFromArray( $styleArray);
                $event->sheet->getDelegate()->getStyle($cellData)->applyFromArray( $border);
                // // $event->sheet->getDelegate()->getStyle("A7:K" . $this->count + 7)->applyFromArray($styleArray);


                $baris = 0;
                for ($i = 1; $i <= $this->lengData; $i++) {
                    $sheet->setCellValue('A' . 5 + $i, $i);
                }


            }
        ];
    }
}
