<?php

namespace App\Exports;

use App\Models\itemIn;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\WithEvents;
use PhpOffice\PhpSpreadsheet\Style\Border;
use Maatwebsite\Excel\Concerns\WithDrawings;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromCollection;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;

class InStockExport implements WithEvents, WithCustomStartCell, FromCollection, WithColumnWidths, WithHeadings
{
    public $data, $count;
    public function __construct($dt)
    {
        $this->data = $dt;
        $this->count = count($dt);
    }

    public function startCell(): string
    {
        return 'A5';
    }

    public function columnWidths(): array
    {
        return [
            'A' => 25,
            'B' => 20,
            'C' => 20,
            'D' => 20,
            'E' => 10,
            'F' => 15,
            'G' => 15,
        ];
    }

    public function view(): View
    {
        return view('exports.in-stock', ['data' => $this->data]);
    }

    public function collection()
    {
        return $this->data;
    }

    public function headings(): array
    {
        return [
            'Material Code',
            'Material Name',
            'Wire Name',
            'Location',
            'Satuan',
            'Min Lot',
            'Qty',
        ];
    }


    /**
     * @return array
     */
    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet;

                $sheet->mergeCells('A1:G2');
                $sheet->setCellValue('A1', "REPORT STOCK MATERIAL");
                $sheet->setCellValue('A3', "Per Tanggal : " . date('d-m-Y H:i'));
                $sheet->getDelegate()->getStyle('A1:G2')->getFont()->setSize(20);
                $sheet->getDelegate()->getStyle('A1:G3')->getFont()->setBold(true);



                $styleArray = [
                    'alignment' => [
                        'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
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

                $cellRange = 'A1:G' . $this->count + 5; // All headers
                $event->sheet->getDelegate()->getStyle($cellRange)->applyFromArray($styleArray);
                $event->sheet->getDelegate()->getStyle("A5:G" . $this->count + 5)->applyFromArray($border);
            }
        ];
    }
}
