<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReturQa extends Model
{
    protected $table = 'retur_qa';
    protected $fillable = [
        'material_no',
        'material_name',
        'qty',
        'surat_jalan',
        'line_c',
        'issue_date',
        'status',
        'no_retur',
    ];
}
