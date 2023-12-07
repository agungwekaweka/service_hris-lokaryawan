<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KomplementTrn extends Model
{
    protected $table="komplement_trn";

    protected $fillable = [
        'id_komplemen_mst',
        'id_komplemen_trn',
        'id_karyawan',
        'email',
        'no_hp',
        'tanggal_pengajuan',
        'tanggal_kedatangan',
        'kode_booking',
        'ticket_order',
        'qty_total',
        'payment_methods',
        'keterangan',
        'is_dell'
    ];
}
