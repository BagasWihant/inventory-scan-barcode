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

class InStockExport implements WithEvents, WithCustomStartCell, FromCollection,WithColumnWidths,WithHeadings
{
    public $data,$count;
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
        ];
    }

    public function view(): View
    {
        return view('exports.in-stock', ['data' => $this->data]);
    }

    public function collection(){
        return $this->data;
    }

    public function headings(): array{
        return [
            'Material No',
            'Qty',
            'Location',
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

                $sheet->mergeCells('B1:D2');
                $sheet->setCellValue('B1', "HASIL EXPORT");

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

                $cellRange = 'A1:C'.$this->count+5; // All headers
                $event->sheet->getDelegate()->getStyle($cellRange)->applyFromArray($styleArray);
                $event->sheet->getDelegate()->getStyle("A5:C".$this->count +5)->applyFromArray($border);
            }
        ];
    }
}
