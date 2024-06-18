<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Style\Border;
use Maatwebsite\Excel\Concerns\WithDrawings;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromCollection;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use Maatwebsite\Excel\Concerns\WithDefaultStyles;
use Maatwebsite\Excel\Concerns\WithMapping;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Style;

class ScannedExport implements WithEvents, WithCustomStartCell,FromCollection,WithColumnWidths,WithHeadings,WithMapping
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
            'C' => 22,            
            'D' => 7,            
            'E' => 15,            
            'F' => 15,            
            'G' => 20,            
        ];
    }

    public function view(): View
    {
        return view('exports.in-stock', ['data' => $this->data]);
    }

    public function map($row): array{
        // $qty = $row->counter;
        // if($row->counter > $row->total){
        //     $qty = $row->total;
        // }
        $char = "□";
        return [
            $char,
            $row->palet,
            $row->material,
            $row->pax,
            $row->counter,
            $row->trucking_id,
            $row->location_cd,
        ];
    }

    public function collection(){
        return $this->data;
    }

    public function headings(): array{
        return [
            'Pallet No',
            ' ',
            'Material No',
            'Pax',
            'Picking Qty',
            'Trucking ID',
            'Location',
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
                $sheet->setCellValue('A1', "Scanned List");
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
                
                $sheet->mergeCells('A5:B5');
                $cellRange = 'A1:G3'; // All headers
                $event->sheet->getDelegate()->getStyle($cellRange)->applyFromArray($styleArray);
                $event->sheet->getDelegate()->getStyle("A5:G".$this->count+5)->applyFromArray($styleArray);

                $border = [
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color' => ['argb' => '000000'],
                        ],
                    ],
                ];
                $event->sheet->getDelegate()->getStyle("A5:G".$this->count+5)->applyFromArray($border);
            }
        ];
    }
}
