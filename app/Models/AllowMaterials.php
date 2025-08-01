<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AllowMaterials extends Model
{
    use HasFactory;
    public $table = 'allow_material';
    public $timestamps = false;
    protected $fillable = [
        'material_no',
        'type',
    ];
}
