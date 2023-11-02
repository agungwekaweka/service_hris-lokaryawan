<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RoleApprove extends Model
{
    protected $table="role_approve";

    protected $fillable = [
        'id_karyawan',
        'type_approve',
        'id_approve'
    ];
}
