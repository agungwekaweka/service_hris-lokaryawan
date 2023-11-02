<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KomplementTrn extends Model
{
    protected $table="komplement_trn";

    protected $fillable = [
        'id_komplement',
        'id_karyawan',
        'tanggal',
        'tipe_komplement',
        'komplement',
        'harga_normal',
        'harga_komplement',
        'kode_booking',
        'keterangan',
        'reff_1',
        'reff_2',
        'reff_3',
        'is_dell'
    ];
}
