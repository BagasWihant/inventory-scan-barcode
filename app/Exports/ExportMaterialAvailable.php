<?php

namespace App\Exports;

use App\Livewire\MaterialAvailable;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\WithEvents;
use PhpOffice\PhpSpreadsheet\Style\Border;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;

class ExportMaterialAvailable implements FromQuery, WithEvents, WithCustomStartCell, WithColumnWidths, WithHeadings
{
    public $data, $count, $dateStart, $dateEnd, $searchMat, $shift;
    public function __construct($dt)
    {
        $this->dateStart = $dt[0];
        $this->dateEnd = $dt[1];
        $this->searchMat = $dt[2];
        $this->shift = $dt[3];
    }

    public function startCell(): string
    {
        return 'A5';
    }

    public function columnWidths(): array
    {
        return [
            'A' => 20,
            'B' => 25,
            'C' => 15,
            'D' => 15,
            'E' => 15,
            'F' => 15,
        ];
    }

    public function query()
    {
        $startDate = $this->dateStart;
        $endDate = $this->dateEnd;
        $materialNo = $this->searchMat;
        $shift = $this->shift;

        return MaterialAvailable::queryHandle($startDate, $endDate, $materialNo, $shift);
    }
    public function headings(): array
    {
        return [
            'Material Code',
            'Qty In',
            'Qty Out',
            'Qty Balance',
            'Qty Now',
            'Lokasi',
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

                $sheet->mergeCells('A1:F2');
                $sheet->setCellValue('A1', "MATERIAL AVAILABLE");
                $sheet->setCellValue('A3', "Per Tanggal : " . date('d-m-Y H:i'));
                $sheet->getDelegate()->getStyle('A1:F2')->getFont()->setSize(20);
                $sheet->getDelegate()->getStyle('A1:F3')->getFont()->setBold(true);



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

                $cellRange = 'A1:F' . $this->count + 5; // All headers
                $event->sheet->getDelegate()->getStyle($cellRange)->applyFromArray($styleArray);
                $event->sheet->getDelegate()->getStyle("A5:F" . $this->count + 5)->applyFromArray($border);
            }
        ];
    }
}
