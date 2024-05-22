<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class itemSisa extends Model
{
    use HasFactory;

    protected $fillable= ['pallet_no','material_no','picking_qty'];

}
