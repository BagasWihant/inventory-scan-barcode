<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pallet extends Model
{
    use HasFactory;

    public $timestamps = false;
    protected $fillable = [
        'pallet_barcode',
        'line',
        'pallet_serial',
        'trucking_id',
        'scanned',
        'scanned_by'
    ];

}
