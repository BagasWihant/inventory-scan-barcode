<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RakTransaksi extends Model
{
    protected $table = 'rak_transaksi';

    public $fillable = [
        'rak_id',
        'material_id',
        'qty',
        'user_id',
        'dihapus',
        'stat',
        'params'
    ];

    public function rak()
    {
        return $this->belongsTo(Rak::class, 'rak_id');
    }

    public function material()
    {
        return $this->belongsTo(RakMaterial::class, 'material_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
