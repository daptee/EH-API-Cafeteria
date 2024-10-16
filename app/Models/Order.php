<?php

namespace App\Models;

use Exception;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Facades\Log;

class Order extends Model
{
    use HasFactory;

    public function status(): HasOne
    {
        return $this->hasOne(OrderStatus::class, 'id', 'status_id');
    }

    public function status_history(): HasMany
    {
        return $this->HasMany(OrderStatusHistory::class, 'order_id', 'id');
    }

    public function rejected_history(): HasMany
    {
        return $this->HasMany(RejectedPayment::class, 'order_id', 'id');
    }

    public static function getAllOrder($id)
    {
        return Order::with(['status','status_history.status', 'rejected_history'])->find($id);
    }

    public static function newOrderAudit($order_number, $detail)
    {
        try {
            $order_audit = new OrderAudit();
            $order_audit->order_number = $order_number;
            $order_audit->detail = $detail;
            $order_audit->save();
        } catch (Exception $error) {
            Log::debug("Error al registrar auditoria: " . $error->getMessage() . ' line: ' . $error->getLine());
        }
    }

    public static function newOrderStatusHistory($status_id, $order_id)
    {
        try {
            $order_status_history = new OrderStatusHistory();
            $order_status_history->status_id = $status_id;
            $order_status_history->order_id = $order_id;
            $order_status_history->save();
        } catch (Exception $error) {
            Log::debug("Error al registrar historial de estado: " . $error->getMessage() . ' line: ' . $error->getLine());
            $order = Order::find($order_id);
            Order::newOrderAudit($order->order_number, [
                "info" => "Error al registar historial de estado",
                "data_sent" => ['status_id' => $status_id ],
                "error_message" => $error->getMessage(), 
                "error_line" => $error->getLine(),
            ]);}
    }

    public static function actionStatusOrder($status_id, $reason_rejection, $order_id)
    {
        switch ($status_id) {
            case 2: // Pago rechazado
                $rejected_payment = new RejectedPayment();
                $rejected_payment->order_id = $order_id;
                $rejected_payment->data = $reason_rejection;
                $rejected_payment->save();
                # code...
                break;
            
            default:
                # code...
                break;
        }
    }
}