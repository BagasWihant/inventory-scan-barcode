<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class paletIn extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $fillable = [
        'pallet_no',
        'line_c',
        'pallet_serial',
        'trucking_id',
    ];

}
