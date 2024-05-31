<?php

namespace App\Exports;

use App\Models\itemIn;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithDrawings;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;

class InStockExportExcel implements WithEvents, WithCustomStartCell, WithDrawings,FromCollection,WithHeadings,WithColumnWidths
{
    public $data;
    public function __construct($dt)
    {
        $this->data = $dt;
    }

    public function headings(): array
    {
        return [
            '#',
            'Pallet No',
            'Material No',
        ];
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

    public function startCell(): string
    {
        return 'A5';
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

    public function collection(){
        return $this->data;
    }

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
