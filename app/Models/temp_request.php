<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class temp_request extends Model
{
    protected $fillable = [
        'transaksi_no',
        'material_no',
        'qty_request',
        'qty_supply',
        'user_id'
    ];

    public $timestamps = false;
}
