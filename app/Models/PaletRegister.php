<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaletRegister extends Model
{
    use HasFactory;

    protected $primaryKey = 'palet_no';
    protected $keyType = 'string';
    public $fillable = ['palet_no','line_c','status','supply_date','is_done'];
}
