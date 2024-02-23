<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class RejectedPayment extends Model
{
    use HasFactory;

    protected $table = "rejected_payments";

    protected $casts = [
        "data" => 'json'
    ];

    public function order(): HasOne
    {
        return $this->hasOne(Order::class, 'id', 'order_id');
    }
}
