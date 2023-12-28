<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderStatus extends Model
{
    use HasFactory;

    const PENDIENTE = 1;
    const PAGO_RECHAZADO = 2;
    const CONFIRMADO = 3;
    const CANCELADO = 4;

    protected $table = 'orders_status';

    protected $fillable = ['name'];
}
