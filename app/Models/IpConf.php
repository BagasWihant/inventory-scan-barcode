<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class IpConf extends Model
{
    protected $table = 'ip_conf';
    public $timestamps = false;
    protected $fillable = ['nik', 'jabatan', 'ip'];
}
