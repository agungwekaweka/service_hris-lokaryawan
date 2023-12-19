<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CutiLampiran extends Model
{
    protected $table="cuti_lampiran";

    protected $fillable = [
        'id_cuti_mst',
        'id_cuti_trn',
        'tahun',
        'url'
    ];
}
