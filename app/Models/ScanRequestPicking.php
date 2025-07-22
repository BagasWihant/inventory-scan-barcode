<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ScanRequestPicking extends Model
{
    
    protected $fillable = [
        'transaksi_no',
        'material_no',
        'qty_request',
        'qty_supply',
        'user_id'
    ];
}
