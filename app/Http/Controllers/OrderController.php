<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderAudit;
use App\Models\OrderStatus;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{

    public $model = Order::class;
    public $s = "orden";
    public $sp = "ordenes";
    public $ss = "orden/s";
    public $v = "n"; 
    public $pr = "la"; 
    public $prp = "las";

    public function store(Request $request)
    {
        $request->validate([
            'order_number' => 'required',
        ]);

        $order = $this->model::where('order_number', $request->order_number)->first();
        if($order)
            return response()->json(['message' => 'Orden ya existente.'], 400);
        
        try {
            $order = new $this->model();
            $order->order_number = $request->order_number;
            $order->save();

            $this->model::newOrderAudit($request->order_number, [
                "info" => "Creación de orden exitosa",
                "data_sent" => $request->all(),
                "error_message" => null, 
                "error_line" => null,
            ]);

            $this->model::newOrderStatusHistory(OrderStatus::PENDIENTE, $order->id);
            
        } catch (Exception $error) {
            Log::debug("Error al guardar orden: " . $error->getMessage() . ' line: ' . $error->getLine());
            $this->model::newOrderAudit($request->order_number, [
                "info" => "Error al guardar orden",
                "data_sent" => $request->all(),
                "error_message" => $error->getMessage(), 
                "error_line" => $error->getLine(),
            ]);
            return response(["message" => "Error al guardar orden", "error" => $error->getMessage()], 500);
        }

        $order = $this->model::getAllOrder($order->id);

        return response()->json(['message' => 'Orden guardada con exito.', 'order' => $order], 200);
    }

    public function show($order_id)
    {   
        $order = $this->model::getAllOrder($order_id);

        return response()->json(['order' => $order]);
    }

    public function order_change_status(Request $request)
    {
        $request->validate([
            'order_id' => ['required', Rule::exists('orders', 'id')],
            'status_id' => ['required', Rule::exists('orders_status', 'id')]
        ]);

        try {
            // Update status order
            $order = $this->model::find($request->order_id);
            $order->status_id = $request->status_id;
            $order->save();
            
            $this->model::newOrderAudit($request->order_number, [
                "info" => "Cambio de estado",
                "data_sent" => $request->all(),
                "error_message" => null, 
                "error_line" => null,
            ]);
            
            $this->model::newOrderStatusHistory($request->status_id, $order->id);

        } catch (Exception $error) {
            Log::debug("Error al actualizar estado de la orden: " . $error->getMessage() . ' line: ' . $error->getLine());
            $this->model::newOrderAudit($order->order_number, [
                "info" => "Error al actualizar estado de la orden",
                "data_sent" => $request->all(),
                "error_message" => $error->getMessage(), 
                "error_line" => $error->getLine(),
            ]);
            return response(["message" => "Error al actualizar estado de la orden", "error" => $error->getMessage()], 500);
        }


        return response()->json(['message' => 'Estado de la orden actualizada con exito.']);
    }

    public function order_get_by_number($order_number)
    {
        $order = $this->model::with(['status','status_history.status', 'rejected_history'])->where('order_number', $order_number)->first();
        
        if(!$order)
            return response()->json(['message' => 'Orden no existente.'], 400);

        return response()->json(['order' => $order]);
    }

    public function get_status_list()
    {
        $order_status_list = null;
        try {
            $order_status_list = OrderStatus::all();
        } catch (Exception $error) {
            Log::debug([
                "error al obtener listado de estados: " . $error->getMessage(),
                "line: " . $error->getLine()
            ]);
            return response(["error" => $error->getMessage()], 500);
        }

        return response()->json(['order_status_list' => $order_status_list], 200);
    }
}
