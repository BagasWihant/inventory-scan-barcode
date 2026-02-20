<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Rak extends Model
{
    protected $table = 'rak';
    protected $fillable = ['name'];
    public $timestamps = false;

    public function materials()
    {
        return $this->hasMany(RakMaterial::class, 'rak_id');
    }

}
