<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KomplementTicketOrder extends Model
{
    protected $table="komplement_ticket_order";

    protected $fillable = [
        'id_komplemen_mst',
        'id_komplemen_trn',
        'ticket_id',
        'product_name',
        'ticket_price_id',
        'qty',
        'qty_bonus',
        'price_unit',
        'subtotal',
        'is_dell'
    ];
}