<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Confirmation</title>
</head>
<body style="font-family: Arial, sans-serif; background:#FFF6F1; padding:24px; color:#2B1D1D;">
    <div style="max-width:560px;margin:0 auto;background:#fff;border-radius:12px;padding:32px;">
        <h2 style="color:#B97A87;">Thank you for your order, {{ $order->first_name }}!</h2>
        <p>Your order <strong>#{{ $order->order_number }}</strong> has been received and is being prepared.</p>

        <table style="width:100%;border-collapse:collapse;margin-top:20px;">
            @foreach ($order->items as $item)
                <tr>
                    <td style="padding:8px 0;border-bottom:1px solid #eee;">{{ $item->product_name }} &times; {{ $item->quantity }}</td>
                    <td style="padding:8px 0;border-bottom:1px solid #eee;text-align:right;">PKR {{ number_format($item->line_total, 0) }}</td>
                </tr>
            @endforeach
            <tr>
                <td style="padding:8px 0;font-weight:bold;">Total</td>
                <td style="padding:8px 0;text-align:right;font-weight:bold;">PKR {{ number_format($order->total, 0) }}</td>
            </tr>
        </table>

        <p style="margin-top:24px;">Shipping to: {{ $order->address }}, {{ $order->city }}</p>
        <p style="font-size:.85rem;color:#777;">We'll email you again when your order status changes.</p>
    </div>
</body>
</html>
