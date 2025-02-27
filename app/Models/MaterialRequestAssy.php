<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MaterialRequestAssy extends Model
{
    protected $table = 'material_request_assy';
    protected $fillable = [
        'transaksi_no',
        'material_no',
        'material_name',
        'type',
        'request_qty',
        'user_request',
        'bag_qty',
        'issue_date',
        'line_c',
        'iss_min_lot',
        'iss_unit',
        'user_id',
        'loc_cd',
        'status',
        'proses_date'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
