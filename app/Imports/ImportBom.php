<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class ImportBom implements ToCollection, WithHeadingRow
{
    public $data;
    public $product_no;
    public $dc;
    public $row = [];

    public function __construct($data, $product_no, $dc)
    {
        $this->data = $data;
        $this->product_no = $product_no;
        $this->dc = $dc;
    }
    /**
     * @param Collection $collection
     */
    public function collection(Collection $collection)
    {
        foreach ($collection as $r) {
            $this->row[] = [
                'product_no' => $this->product_no,
                'dc' => $this->dc,
                'material_no' => $r['material_no'],
                'bom_qty' =>(float) str_replace(',', '.', $r['bom_qty']),
                'created_at' => now(),
                'updated_at' => now(),
                'status' => 1
            ];
        }
        return $this->row;
    }

    public function getData()
    {
        return $this->row;
    }
}
