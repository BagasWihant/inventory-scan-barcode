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
use Maatwebsite\Excel\Concerns\WithMapping;

class InStockExportExcel implements WithEvents, WithCustomStartCell, FromCollection,WithHeadings,WithColumnWidths,WithMapping
{
    public $data;
    public $totalData;
    public function __construct($dt)
    {
        $this->data = collect($dt);
        $this->totalData = count($dt);
    }

    public function headings(): array
    {
        return [
            '#',
            'Pallet No',
            'Kit No',
            'Material No',
            'Line C',
            'Location',
            'Status',
            'Qty',
        ];
    }

    public function map($row): array
    {
        return [
            '',
            $row->pallet_no,
            $row->kit_no,
            $row->material_no,
            $row->line_c,
            $row->locate,
            $row->status == '0' ? "Kurang" : "Kelebihan",
            $row->qty,
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 8,
            'B' => 30,            
            'C' => 30,            
            'D' => 30,            
            'E' => 12,            
            'F' => 12,            
            'G' => 12,            
            'H' => 12,            
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
        $drawing->setPath(public_path('/assets/logo.jpg'));
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

                $sheet->mergeCells('A1:H1');
                $sheet->setCellValue('A1', "HASIL EXPORT");

                $styleArray = [
                    'alignment' => [
                        'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                    ],
                ];
                for ($i = 1; $i <= $this->totalData; $i++) {
                    $sheet->setCellValue('A' . $i + 5, $i);
                }
                $cellRange = 'A1:D1'; // All headers
                $event->sheet->getDelegate()->getStyle($cellRange)->applyFromArray($styleArray);
            }
        ];
    }
}
