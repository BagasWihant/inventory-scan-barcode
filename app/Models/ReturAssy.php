<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReturAssy extends Model
{
    protected $table = 'retur_assy';
    protected $fillable = [
        'material_no',
        'material_name',
        'qty',
        'surat_jalan',
        'line_c',
        'issue_date',
        'status',
    ];
}
