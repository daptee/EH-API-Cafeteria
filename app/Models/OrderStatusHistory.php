<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class OrderStatusHistory extends Model
{
    use HasFactory;

    protected $table = 'orders_status_history';

    public function status(): HasOne
    {
        return $this->hasOne(OrderStatus::class, 'id', 'status_id');
    }
}
