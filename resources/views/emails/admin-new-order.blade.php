<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>New Order Received</title>
</head>
<body style="font-family: Arial, sans-serif; padding:24px;">
    <h2>New order received — #{{ $order->order_number }}</h2>
    <p><strong>Customer:</strong> {{ $order->first_name }} {{ $order->last_name }} ({{ $order->email }})</p>
    <p><strong>Total:</strong> PKR {{ number_format($order->total, 0) }}</p>
    <ul>
        @foreach ($order->items as $item)
            <li>{{ $item->product_name }} &times; {{ $item->quantity }}</li>
        @endforeach
    </ul>
</body>
</html>
