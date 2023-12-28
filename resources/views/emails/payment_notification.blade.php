<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Resumen de compra - Tienda Sukha</title>
</head>
<body>
    <p>
        Hola {{ $data['name'] }}, muchas gracias por su compra. A continuación le dejamos un detalle de la misma. <br>
        Fecha: {{ $order->created_at->format('d/m/Y') }} <br>
        Hora: {{ $order->created_at->format('H:i') }} <br>
        Numero de compra: {{ $order->order_number }} <br>
        @foreach ($data['products']['items'] as $item)
            {{ $item['quantity'] . 'x ' . $item['title'] . ' ' . $item['unit_price']}} <br>
        @endforeach
        El total fue de {{ $data['products']['total_price'] }} <br>
        Muchas gracias. <br>
        El equipo de Sukha  
    </p>
</body>
</html>