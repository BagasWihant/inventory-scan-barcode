<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class itemKurang extends Model
{
    use HasFactory;
    protected $table = 'material_kurang';


    protected $fillable= ['pallet_no','material_no','picking_qty','locate','trucking_id'];

}
