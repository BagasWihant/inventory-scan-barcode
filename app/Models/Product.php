<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable= ['pallet_barcode','product_name','stock'];

    public function getRouteKeyName()
    {
        return 'pallet_barcode';
    }
}
