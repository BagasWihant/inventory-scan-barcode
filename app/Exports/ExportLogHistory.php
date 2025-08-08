<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Events\AfterSheet;

class ExportLogHistory implements
    FromCollection,
    WithCustomStartCell,
    WithColumnWidths,
    WithHeadings,
    WithMapping,
    WithEvents
{
    protected $data;
    protected $counter = 0;

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
            'A' => 5,
            'B' => 20,
            'C' => 10,
            'D' => 25,
            'E' => 10,
        ];
    }

    public function map($row): array
    {
        return [
            ++$this->counter, // nomor urut otomatis
            $row->material_no ?? '-',
            $row->qty ?? "-",
            $row->created_at ?? "-",
            $row->status ?? "-",
        ];
    }

    public function headings(): array
    {
        return [
            'No',
            'Material No',
            'Qty',
            'Date',
            'Status',
        ];
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {

        return collect($this->data);
    }


    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {

                $lastRow = count($this->data) + 5;
                $cellRange = 'A5:E' . $lastRow;

                // tambahkan border 
                $event->sheet->getStyle($cellRange)->applyFromArray([
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                            'color' => ['argb' => '000000'],
                        ],
                    ],
                ]);

                // header bold
                $event->sheet->getStyle('A5:E5')->applyFromArray([
                    'font' => [
                        'bold' => true,
                    ],
                ]);
            },
        ];
    }
}
