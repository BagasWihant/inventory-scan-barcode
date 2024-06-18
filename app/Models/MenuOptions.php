<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MenuOptions extends Model
{
    use HasFactory;
    protected $table = 'master_sto';
    public $timestamps = false;
    protected $fillable = [
        'code',
        'status',
        'date_start'
    ];

}
