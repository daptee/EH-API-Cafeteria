<?php

namespace App\Http\Controllers;

use App\Mail\PaymentNotification;
use App\Models\Order;
use App\Models\OrderStatus;
use App\Models\PaymentAttemptHistory;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rule;

class PaymentController extends Controller
{

    public $model = PaymentAttemptHistory::class;
    public $s = "pago";
    public $sp = "pagos";
    public $ss = "pago/s";
    public $v = "o"; 
    public $pr = "el"; 
    public $prp = "los";

    public function store(Request $request)
    {
        $request->validate([
            'order_id' => ['required', Rule::exists('orders', 'id')],
        ]);

        try {
            $data = $request->data; 
            $data_order = $request->data_order;

            $payment = new $this->model();
            $payment->order_id = $request->order_id;
            $payment->data = $data;
            $payment->save();

            $order = Order::find($request->order_id);
            
            $payment_order_status_id = $data['payment_status'];
            $order->status_id = $payment_order_status_id;
            $order->save();
            Order::newOrderStatusHistory($payment_order_status_id, $order->id);

            // if($payment_order_status_id == OrderStatus::CONFIRMADO){
            //     $client_email = $data_order['email'];
            //     Log::debug($client_email);
            //     try {
            //     //  ver informacion de data de compra
            //         Mail::to("slarramendy@daptee.com.ar")->send(new PaymentNotification($data_order, $order));                        
            //     } catch (Exception $error) {
            //         Log::debug(print_r(["message" => $error->getMessage() . " error en envio de mail a $client_email en notificacion de orden confirmada", "order_number" => $order->order_number, $error->getLine()],  true));
            //     }
            // }
            
            Order::newOrderAudit($request->order_number, [
                "info" => "Registro de pago exitoso",
                "data_sent" => $request->all(),
                "error_message" => null, 
                "error_line" => null,
            ]);

        } catch (Exception $error) {
            Log::debug("Error al registrar pago: " . $error->getMessage() . ' line: ' . $error->getLine());
            Order::newOrderAudit($order->order_number, [
                "info" => "Error al registar pago",
                "data_sent" => $request->all(),
                "error_message" => $error->getMessage(), 
                "error_line" => $error->getLine(),
            ]);
            return response(["message" => "Error al registrar pago", "error" => $error->getMessage()], 500);
        }
       
        return response()->json(['message' => 'Informacion de pago guardada con exito.', 'payment' => $payment], 200);
    }
}
