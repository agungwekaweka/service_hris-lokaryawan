<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CutiApproveHistory extends Model
{
    protected $table="cuti_approve_history";

    protected $fillable = [
        'id_cuti_mst',
        'id_cuti_trn',
        'status',
        'telephone',
        'id_karyawan_approve',
        'tgl_approve',
        'note'
    ];
}
