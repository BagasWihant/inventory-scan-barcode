<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class abnormalMaterial extends Model
{
    use HasFactory;

    protected $fillable = [
        'pallet_no',
        'material_no',
        'picking_qty',
        'locate',
        'trucking_id',
        'user_id',
        'status',
        'kit_no',
        'surat_jalan',
        'line_c',
        'setup_by',
        'box'
    ];
}
