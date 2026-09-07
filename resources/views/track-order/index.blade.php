@extends('layouts.app')
@section('title', 'Track Your Order — Alishe Nails')

@section('content')
    <div class="container" style="max-width:760px;padding-block:48px 64px;">
        <div class="page-header" style="margin-bottom:24px;">
            <h1>Track Your Order</h1>
            <p>Enter your order number and the phone number or email used at checkout to view the current status.</p>
        </div>

        <div class="checkout-card">
            <form action="{{ route('track-order.search') }}" method="POST">
                @csrf
                <div class="form-grid">
                    <div class="form-field full">
                        <label for="order_number">Order Number</label>
                        <input id="order_number" name="order_number" value="{{ old('order_number') }}" placeholder="ALN-ABC12345" required>
                        @error('order_number') <div class="error">{{ $message }}</div> @enderror
                    </div>

                    <div class="form-field full">
                        <label for="identifier">Phone or Email</label>
                        <input id="identifier" name="identifier" value="{{ old('identifier') }}" placeholder="+92 300 1234567 or you@example.com" required>
                        @error('identifier') <div class="error">{{ $message }}</div> @enderror
                    </div>
                </div>

                <button type="submit" class="btn btn-primary" style="margin-top:16px;">Track Order</button>
            </form>
        </div>

        @if (isset($order))
            <div class="checkout-card" style="margin-top:24px;">
                <h3>Order #{{ $order->order_number }}</h3>
                <p style="margin:8px 0 16px;">
                    Status: <span class="status-pill status-{{ $order->status }}">{{ ucwords(str_replace('_', ' ', $order->status)) }}</span>
                </p>

                <div class="summary-card">
                    @foreach ($order->items as $item)
                        <div class="summary-row">
                            <span>{{ $item->product_name }} &times; {{ $item->quantity }}</span>
                            <span>PKR {{ number_format($item->line_total, 0) }}</span>
                        </div>
                    @endforeach
                    <div class="summary-row"><span>Shipping</span><span>{{ $order->shipping == 0 ? 'Free' : 'PKR '.number_format($order->shipping, 0) }}</span></div>
                    <div class="summary-row total"><span>Total</span><span>PKR {{ number_format($order->total, 0) }}</span></div>
                </div>
            </div>
        @endif
    </div>
@endsection
