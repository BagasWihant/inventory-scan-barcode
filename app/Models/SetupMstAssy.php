<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SetupMstAssy extends Model
{
    protected $table = 'setup_mst_assy';
    protected $fillable = [
        'issue_date',
        'line_cd',
        'status',
        'created_by',
        'created_at',
        'updated_at',
        'finished_at'
    ];
}
