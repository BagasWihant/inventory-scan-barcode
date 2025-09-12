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

class ReceivingSupplierNotAssyReport implements FromCollection, WithMapping, WithHeadings, WithEvents, WithCustomStartCell, WithColumnWidths
{
    public $data, $count,$palet_no;
    public function __construct($dt)
    {
        $this->palet_no = $dt['palet_no'];
        $this->data = $dt['data'];
        $this->count = count($dt['data']);
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
            'E' => 20,
            'F' => 20,
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
        $char = "□";
        return [
            $char,
            $row->material,
            $row->location,
            $row->line_c,
            $row->total,
            $row->counter,
        ];
    }

    public function headings(): array
    {
        return [
            '   ',
            'Material',
            'Location',
            'Line C',
            'Qty Picking',
            'Qty Received',
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $max_col = "F";
                $sheet = $event->sheet;

                $sheet->mergeCells("A1:".$max_col."1");
                $sheet->setCellValue('A1', "RECEIVING SUPPLIER REPORT");
                
                $sheet->mergeCells("A2:".$max_col."2");
                $sheet->setCellValue('A2', Carbon::now('Asia/Jakarta')->format('l, j F Y H:i:s'));
                $sheet->setCellValue('A3', "   ");

                $sheet->mergeCells("A4:B4");
                $sheet->setCellValue('A4', "Kit No");
                $sheet->setCellValue('C4', ": " . $this->data[0]->palet);
                
                $sheet->mergeCells("A5:B5");
                $sheet->setCellValue('A5', "Pallet No");
                $sheet->setCellValue('C5', ": " . $this->palet_no);
                
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
                $event->sheet->getDelegate()->getStyle("A7:". $max_col . $this->count + 7)->applyFromArray($border);
                $event->sheet->getDelegate()->getStyle('A7:' . $max_col . $this->count + 7)->applyFromArray($styleArray);
                $event->sheet->getDelegate()->getStyle('A1:'.$max_col.'2')->applyFromArray($styleArray);
            }
        ];
    }
}
