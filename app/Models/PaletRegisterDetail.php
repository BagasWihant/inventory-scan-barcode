<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaletRegisterDetail extends Model
{
    use HasFactory;

    public $fillable = ['palet_no','material_no','material_name','qty','is_done'];
}
