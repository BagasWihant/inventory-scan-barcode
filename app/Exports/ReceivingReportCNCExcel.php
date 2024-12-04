<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Events\AfterSheet;

class ReceivingReportCNCExcel implements WithEvents, WithCustomStartCell,FromCollection,WithHeadings,WithColumnWidths,WithMapping
{
    public $data,$type;
    public function __construct($dt)
    {
        $this->data = $dt['data'];
        $this->type = $dt['type'];
    }

    public function headings(): array
    {
        return [
            'Date Siws',
            'Received Date',
            'Kit No',
            'Palet No',
            'Material No',
            'Line C',
            'QTY Supl',
            'QTY Kias',
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 25,
            'B' => 25,            
            'C' => 25,            
            'D' => 25,            
            'E' => 25,            
            'F' => 15,            
            'G' => 10,            
            'H' => 10,            
        ];
    }

    public function startCell(): string
    {
        return 'A5';
    }

    public function map($row): array {
        return [
            $row->Delivery_Supply_Date_SIWS,
            $row->Received_Date,
            $row->kit_no,
            $row->pallet_no,
            $row->material_no,
            $row->line_c,
            $row->Qty_Delivery_Supplier,
            $row->Qty_Received_KIAS,
        ];
    }

    public function collection(){
        return $this->data;
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet;

                $sheet->mergeCells('B1:H2');
                $sheet->setCellValue('B1', "Receiving Report ".$this->type);

                $styleArray = [
                    'alignment' => [
                        'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                    ],
                ];

                $cellRange = 'A1:H1'; // All headers
                $event->sheet->getDelegate()->getStyle($cellRange)->applyFromArray($styleArray);
            }
        ];
    }
}
