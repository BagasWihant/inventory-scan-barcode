<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RakMaterial extends Model
{
    protected $table = 'rak_material';
    public $timestamps = false;

    public $fillable = [
        'rak_id',
        'nama',
        'kode',
        'satuan',
        'stok'
    ];

    public function rak()
    {
        return $this->belongsTo(Rak::class, 'rak_id');
    }

    public function transaksi()
    {
        return $this->hasMany(RakTransaksi::class, 'id_material');
    }
}
