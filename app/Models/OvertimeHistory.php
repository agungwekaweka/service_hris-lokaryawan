<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OvertimeHistory extends Model
{
    protected $table="overtime_history";

    protected $fillable = [
        'id_overtime',
        'status',
        'telephone',
        'id_karyawan_approve',
        'tgl_approve',
        'note'
    ];
}
