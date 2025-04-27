<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MaterialInStockAssy extends Model
{
    protected $table = 'material_in_stock_assy';
    protected $fillable = [
        'transaksi_no',
        'material_no',
        'material_name',
        'type',
        'qty',
        'issue_date',
        'line_c',
        'iss_min_lot',
        'iss_unit',
        'user_id',
        'status',
        'loc_cd',
        'proses_date',
        'surat_jalan',
    ];
}
