<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderAudit extends Model
{
    use HasFactory;

    protected $table = 'orders_audit';

    protected $casts = [
        "detail" => 'json'
    ];
}
