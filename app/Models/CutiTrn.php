<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CutiTrn extends Model
{
    protected $table="cuti_trn";

    protected $fillable = [
        'id_cuti',
        'id_periode',
        'tahun',
        'id_karyawan',
        'tipe_cuti',
        'cuti',
        'tanggal',
        'total_cuti',
        'keterangan',
        'tgl_pengajuan',
        'status',
        'note',
        'reff_1',
        'reff_2',
        'reff_3',
        'is_dell'
    ];
}
