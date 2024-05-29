<?php

namespace App\Imports;

use App\Models\MaterialKelebihan;
use Maatwebsite\Excel\Concerns\ToModel;

class KelebihanImport implements ToModel
{
    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        return new MaterialKelebihan([
            'pallet_no' => $row[0],
            'material_no' => $row[1],
            'picking_qty' => $row[2]
        ]);
    }
}
