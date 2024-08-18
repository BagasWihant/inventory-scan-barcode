<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Border;

class ReceivingSupplierReport implements FromCollection, WithMapping, WithHeadings, WithEvents, WithCustomStartCell, WithColumnWidths
{
    public $data, $count;
    public function __construct($dt)
    {
        $this->data = $dt;
        $this->count = count($dt);
    }
    public function startCell(): string
    {
        return 'A7';
    }
    public function columnWidths(): array
    {
        return [
            'A' => 30,
            'B' => 20,
            'C' => 20,
            'D' => 20,
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
        return [
            $row->material,
            isset(json_decode($row->prop_ori, true)['location']) ? json_decode($row->prop_ori, true)['location'] : $row->loc_cd,
            $row->total,
            $row->counter,
        ];
    }

    public function headings(): array
    {
        return [
            'Material',
            'Location',
            'Qty Picking',
            'Qty Received',
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $max_col = "D";
                $sheet = $event->sheet;

                $sheet->mergeCells("A1:".$max_col."1");
                $sheet->setCellValue('A1', "RECEIVING SUPPLIER REPORT");

                $sheet->setCellValue('A2', "   ");
                $sheet->setCellValue('A3', "Pallet No");
                $sheet->setCellValue('B3', ": " . $this->data[0]->palet);

                $sheet->setCellValue('A4', "LINE C");
                $sheet->setCellValue('B4', ": " . $this->data[0]->line_c);
                
                $sheet->setCellValue('A5', "  ");
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
                $event->sheet->getDelegate()->getStyle('A1:'.$max_col.'1')->applyFromArray($styleArray);
            }
        ];
    }
}
