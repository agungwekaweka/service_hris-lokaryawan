<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RoleApprove extends Model
{
    protected $table="role_approve";

    protected $fillable = [
        'id_role_approve',
        'id_departemen',
        'id_sub_departemen',
        'ord',
        'id_grade',
        'pic',
        'type_role'
    ];
}
