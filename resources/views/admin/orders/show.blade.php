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
            </div>

            <div class="admin-card">
                <div class="admin-card__head"><h3>Update Status</h3></div>
                <form action="{{ route('admin.orders.updateStatus', $order) }}" method="POST">
                    @csrf
                    @method('PATCH')
                    <div class="form-field" style="margin-bottom:14px;">
                        <select name="status" class="select-sort" style="width:100%;">
                            @foreach (['pending', 'processing', 'completed', 'cancelled'] as $status)
                                <option value="{{ $status }}" {{ $order->status === $status ? 'selected' : '' }}>{{ ucfirst($status) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary btn-block">Update Status</button>
                </form>

                <p style="font-size:.8rem;color:rgba(43,29,29,.6);margin-top:14px;">
                    Payment method: <strong style="text-transform:capitalize;">{{ str_replace('_', ' ', $order->payment_method) }}</strong>
                </p>
            </div>
        </div>
    </div>

@endsection
