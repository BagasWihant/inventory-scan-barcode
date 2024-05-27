<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class tempCounter extends Model
{
    use HasFactory;
    public $timestamps = false;
    public $keyType = 'string';
    protected $fillable= ['material','material_fix','userID','total','counter','sisa','palet','pax'];

    
}
