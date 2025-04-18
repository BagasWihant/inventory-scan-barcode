<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SetupDtlAssy extends Model
{
    protected $table = 'setup_dtl_assy';
    protected $fillable = [
        'setup_id',
        'material_no',
        'qty',
        'pallet_no',
        'created_at',
        'updated_at'
    ];
}
