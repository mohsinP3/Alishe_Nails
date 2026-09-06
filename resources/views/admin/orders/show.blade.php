@extends('layouts.admin')
@section('title', 'Order #'.$order->order_number.' — Alishe Nails Admin')

@section('content')

    <div class="admin-page-head">
        <div>
            <h1>Order #{{ $order->order_number }}</h1>
            <p>Placed {{ $order->created_at->format('M j, Y \a\t g:i A') }}</p>
        </div>
        <a href="{{ route('admin.orders.index') }}" class="btn btn-outline">&larr; Back to Orders</a>
    </div>

    <div class="dashboard-grid">
        <div class="admin-card">
            <div class="admin-card__head"><h3>Items</h3></div>
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Product</th>
                        <th>Shape / Size</th>
                        <th>Qty</th>
                        <th>Price</th>
                        <th>Line Total</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($order->items as $item)
                        <tr>
                            <td>{{ $item->product_name }}</td>
                            <td>{{ $item->shape }} @if($item->size) / {{ $item->size }} @endif</td>
                            <td>{{ $item->quantity }}</td>
                            <td>PKR {{ number_format($item->price, 0) }}</td>
                            <td>PKR {{ number_format($item->line_total, 0) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <div style="margin-top:20px;max-width:280px;margin-left:auto;">
                <div class="summary-row"><span>Subtotal</span><span>PKR {{ number_format($order->subtotal, 0) }}</span></div>
                <div class="summary-row"><span>Shipping</span><span>{{ $order->shipping == 0 ? 'Free' : 'PKR '.number_format($order->shipping, 0) }}</span></div>
                <div class="summary-row total"><span>Total</span><span>PKR {{ number_format($order->total, 0) }}</span></div>
            </div>
        </div>

        <div>
            <div class="admin-card" style="margin-bottom:20px;">
                <div class="admin-card__head"><h3>Customer</h3></div>
                <p style="margin:0 0 6px;"><strong>{{ $order->first_name }} {{ $order->last_name }}</strong></p>
                <p style="margin:0 0 6px;font-size:.85rem;"><i class="fa-solid fa-envelope"></i> {{ $order->email }}</p>
                <p style="margin:0 0 6px;font-size:.85rem;"><i class="fa-solid fa-phone"></i> {{ $order->phone }}</p>
                <p style="margin:0;font-size:.85rem;"><i class="fa-solid fa-location-dot"></i> {{ $order->address }}, {{ $order->city }}</p>
                @if ($order->area)
                    <p style="margin:6px 0 0;font-size:.85rem;"><i class="fa-solid fa-map-pin"></i> Area: {{ $order->area }}</p>
                @endif
                @if ($order->postal_code)
                    <p style="margin:6px 0 0;font-size:.85rem;"><i class="fa-solid fa-envelope-open-text"></i> Postal Code: {{ $order->postal_code }}</p>
                @endif
                @if ($order->transaction_reference)
                    <p style="margin:6px 0 0;font-size:.85rem;"><i class="fa-solid fa-receipt"></i> Transaction Reference: {{ $order->transaction_reference }}</p>
                @endif
            </div>

            <div class="admin-card">
                <div class="admin-card__head"><h3>Payment</h3></div>
                <p style="margin:0 0 6px;font-size:.85rem;text-transform:capitalize;">
                    <i class="fa-solid fa-credit-card"></i> {{ str_replace('_', ' ', $order->payment_method) }}
                </p>
                <p style="margin:0 0 16px;font-size:.85rem;">
                    <i class="fa-solid fa-circle-info"></i> Payment status:
                    <span class="status-pill status-{{ $order->payment_status }}">{{ ucfirst($order->payment_status) }}</span>
                </p>

                <form action="{{ route('admin.orders.updatePaymentStatus', $order) }}" method="POST">
                    @csrf
                    @method('PATCH')
                    <div class="form-field" style="margin-bottom:14px;">
                        <select name="payment_status" class="select-sort" style="width:100%;">
                            @foreach (\App\Models\Order::PAYMENT_STATUSES as $paymentStatus)
                                <option value="{{ $paymentStatus }}" {{ $order->payment_status === $paymentStatus ? 'selected' : '' }}>
                                    {{ ucfirst($paymentStatus) }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <button type="submit" class="btn btn-outline btn-block">Update Payment Status</button>
                </form>
            </div>

            <div class="admin-card">
                <div class="admin-card__head"><h3>Update Order Status</h3></div>
                <form action="{{ route('admin.orders.updateStatus', $order) }}" method="POST">
                    @csrf
                    @method('PATCH')
                    <div class="form-field" style="margin-bottom:14px;">
                        <select name="status" class="select-sort" style="width:100%;">
                            @foreach (\App\Models\Order::STATUSES as $status)
                                <option value="{{ $status }}" {{ $order->status === $status ? 'selected' : '' }}>
                                    {{ ucwords(str_replace('_', ' ', $status)) }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary btn-block">Update Status</button>
                </form>

                <p style="font-size:.8rem;color:rgba(43,29,29,.6);margin-top:14px;">
                    Changing the status to <strong>Cancelled</strong> automatically restores the reserved stock to inventory.
                </p>
            </div>
        </div>
    </div>

@endsection
