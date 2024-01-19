<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Overtime extends Model
{
    protected $table="overtime";

    protected $fillable = [
        'id_overtime',
        'id_karyawan',
        'nip',
        'tgl_pengajuan',
        'tgl_lembur',
        'jam_lembur',
        'jam_awal',
        'jam_akhir',
        'total_jam',
        'status',
        'is_dell'
    ];
}
