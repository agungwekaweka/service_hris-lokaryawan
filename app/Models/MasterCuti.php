<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MasterCuti extends Model
{
    protected $table="cuti_mst";

    protected $fillable = [
        'id_cuti',
        'tahun',
        'id_karyawan',
        'tipe_cuti',
        'cuti',
        'sisa_cuti',
        'is_dell'
    ];
}
