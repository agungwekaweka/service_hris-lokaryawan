<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KomplementMst extends Model
{
    protected $table="komplement_mst";

    protected $fillable = [
        'id_komplement',
        'tahun',
        'id_karyawan',
        'tipe_komplement',
        'sisa_komplement',
        'is_dell'
    ];
}
