<!DOCTYPE html>
<html>
<body style="font-family: Arial, sans-serif; background:#FFF6F1; padding:24px; color:#2B1D1D;">
    <div style="max-width:560px;margin:0 auto;background:#fff;border-radius:12px;padding:32px;">
        <h2 style="color:#B97A87;">Order Update</h2>
        <p>Hi {{ $order->first_name }}, your order <strong>#{{ $order->order_number }}</strong> status has changed to:</p>
        <p style="font-size:1.2rem;font-weight:bold;text-transform:capitalize;">{{ $order->status }}</p>
        <p style="margin-top:20px;">Total: PKR {{ number_format($order->total, 0) }}</p>
    </div>
</body>
</html>
