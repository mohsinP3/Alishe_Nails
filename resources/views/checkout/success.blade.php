@extends('layouts.app')

@section('title', 'Order Confirmed — Alishe Nails')

@section('content')
    <div class="container" style="max-width:640px;text-align:center;padding-block:80px;">
        <i class="fa-solid fa-circle-check" style="font-size:3rem;color:var(--rose);"></i>
        <h1 style="margin-top:20px;">Thank you, {{ $order->first_name }}!</h1>
        <p>Your order <strong>#{{ $order->order_number }}</strong> has been placed successfully. A confirmation has been logged and our team will reach out to confirm delivery.</p>

        <div class="summary-card" style="text-align:left;margin-top:32px;">
            @foreach ($order->items as $item)
                <div class="summary-row">
                    <span>{{ $item->product_name }} &times; {{ $item->quantity }}</span>
                    <span>PKR {{ number_format($item->line_total, 0) }}</span>
                </div>
            @endforeach
            <div class="summary-row total">
                <span>Total</span><span>PKR {{ number_format($order->total, 0) }}</span>
            </div>
        </div>

        <a href="{{ route('shop.index') }}" class="btn btn-primary" style="margin-top:32px;">Continue Shopping</a>
    </div>
@endsection
