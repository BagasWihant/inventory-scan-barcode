<?php

namespace App\Exports;

use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\WithEvents;
use PhpOffice\PhpSpreadsheet\Style\Border;
use Maatwebsite\Excel\Concerns\WithDrawings;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromCollection;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use Maatwebsite\Excel\Concerns\WithMapping;
use PhpOffice\PhpSpreadsheet\Spreadsheet;

class ExportStockTaking implements WithEvents, WithCustomStartCell, WithDrawings,FromCollection,WithColumnWidths,WithHeadings,WithMapping
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
            'A' => 3,
            'B' => 15,
            'C' => 20,            
            'D' => 20,            
            'E' => 20,            
            'F' => 20,            
            'G' => 20,            
            'H' => 20,            
        ];
    }
    public function map($row): array{
        $char = " ";
        $no = 1;
        return [
            $no++,
            $row->material_no,
            $char,
            $char,
            $char,
            $char,
            $char,
            $char,
        ];
    }

    public function collection(){
        return $this->data;
    }

    public function headings(): array{
        return [
            'No',
            'Material No',
            ' ',
            ' ',
            ' ',
            ' ',
            ' ',
            ' ',
        ];
    }
   
    public function drawings()
    {

        $drawing = new Drawing();
        $drawing->setName('Logo');
        $drawing->setDescription('This is my logo');
        $drawing->setPath(public_path('/assets/logo.png'));
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
                $spreadset = new Spreadsheet;

                $sheet->mergeCells('A1:G3');
                $sheet->setCellValue('A1', "PRINT STOCK TAKING");
                $sheet->mergeCells('A4:C4');
                $sheet->setCellValue('A4', "  ");
                $sheet->getDelegate()->getStyle('A1:G2')->getFont()->setSize(20);
                $sheet->getDelegate()->getStyle('A1:G5')->getFont()->setBold(true);

                $styleArray = [
                    'alignment' => [
                        'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                        'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                    ],
                ];
                
                $cellRange = 'A1:G3'; // All headers
                $event->sheet->getDelegate()->getStyle($cellRange)->applyFromArray($styleArray);
                $event->sheet->getDelegate()->getStyle("A5:G".$this->count+30)->applyFromArray($styleArray);

                $border = [
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color' => ['argb' => '000000'],
                        ],
                    ],
                ];
                $event->sheet->getDelegate()->getStyle("A5:G".$this->count+30)->applyFromArray($border);
            }
        ];
    }
}
