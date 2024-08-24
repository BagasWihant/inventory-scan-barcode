<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class tempCounter extends Model
{
    use HasFactory;
    public $timestamps = false;
    public $keyType = 'string';
    protected $fillable = [
        'material',
        'userID',
        'total',
        'counter',
        'sisa',
        'palet',
        'pax',
        'qty_more',
        'prop_ori',
        'prop_scan',
        'scan_count',
        'flag',
        'line_c',
        'scanned_time',
    ];
}
