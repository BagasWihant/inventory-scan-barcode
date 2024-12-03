<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MaterialRequest extends Model
{
    protected $fillable = [
        'transaksi_no',
        'material_no',
        'material_name',
        'type',
        'request_qty',
        'request_user',
        'bag_qty',
        'iss_min_lot',
        'created_by',
        'status',
        'proses_date'
    ];
}
