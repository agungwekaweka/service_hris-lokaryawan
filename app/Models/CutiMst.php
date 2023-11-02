<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CutiMst extends Model
{
    protected $table="cuti_mst";

    protected $fillable = [
        'id_cuti',
        'tahun',
        'id_karyawan',
        'tipe_cuti',
        'cuti',
        'sisa_cuti',
        'date_start',
        'date_end',
        'is_dell'
    ];
}
