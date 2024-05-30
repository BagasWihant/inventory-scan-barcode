<?php

namespace App\Exports;

use App\Models\itemIn;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class InStockExportExcel implements FromCollection,WithHeadings
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function headings(): array
    {
        return [
            '#',
            'Pallet No',
            'Material No',
        ];
    }
    public function collection()
    {
        return itemIn::all();
    }
}
