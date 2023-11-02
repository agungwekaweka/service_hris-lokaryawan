<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Informasi extends Model
{
    protected $table="informasi";

    protected $fillable = [
        'pic',
        'judul',
        'image',
        'keterangan',
        'is_dell'
    ];
}
