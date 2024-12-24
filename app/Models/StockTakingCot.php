<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StockTakingCot extends Model
{
    protected $fillable = [
        'no_sto',
        'tgl_sto',
        'user_id',
        'line_code',
        'material_no',
        'qty',
        'issue_date',
        'palet_no',
        'location',
        'status'
    ];
}
