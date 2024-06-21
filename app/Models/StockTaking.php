<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockTaking extends Model
{
    use HasFactory;

    public $fillable = [
        'sto_id',
        'user_id',
        'material_no',
        'hitung',
        'loc',
        'qty'
    ];
}
