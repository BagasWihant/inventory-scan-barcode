<?php

namespace App\Exports;

use App\Models\itemIn;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithDrawings;
use Maatwebsite\Excel\Concerns\WithHeadings;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;

class InStockExport implements WithEvents, WithCustomStartCell, WithDrawings,FromCollection,WithColumnWidths
{
    public $data;
    public function __construct($dt)
    {
        $this->data = $dt;
    }

    public function startCell(): string
    {
        return 'A5';
    }

    public function columnWidths(): array
    {
        return [
            'A' => 55,
            'B' => 45,            
            'C' => 45,            
            'D' => 45,            
        ];
    }

    public function view(): View
    {
        return view('exports.in-stock', ['data' => $this->data]);
    }

    public function collection(){
        return $this->data;
    }

    public function drawings()
    {

        $drawing = new Drawing();
        $drawing->setName('Logo');
        $drawing->setDescription('This is my logo');
        $drawing->setPath(public_path('/assets/bg.jpg'));
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

                $sheet->mergeCells('B1:D2');
                $sheet->setCellValue('B1', "HASIL EXPORT");

                $styleArray = [
                    'alignment' => [
                        'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                    ],
                ];

                $cellRange = 'A1:D1'; // All headers
                $event->sheet->getDelegate()->getStyle($cellRange)->applyFromArray($styleArray);
            }
        ];
    }
}
