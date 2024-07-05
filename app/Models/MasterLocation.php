<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MasterLocation extends Model
{
    use HasFactory;

    public $timestamps = false;
    public $fillable = ['material_no', 'location'];
}
