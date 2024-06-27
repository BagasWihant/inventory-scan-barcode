<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RealStockTaking extends Model
{
    use HasFactory;

    public $fillable =[
        'sto_id',
        'user_id',
        'material_no',
        'loc_sys',
        'qty_sys',
        'loc_sto',
        'qty_sto',
        'result_qty',
    ];
}
