<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MaterialRequest extends Model
{
    protected $table = 'material_request';
    protected $fillable = [
        'transaksi_no',
        'material_no',
        'material_name',
        'type',
        'request_qty',
        'user_request',
        'bag_qty',
        'iss_min_lot',
        'iss_unit',
        'user_id',
        'loc_cd',
        'status',
        'proses_date',
        'exclude'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
