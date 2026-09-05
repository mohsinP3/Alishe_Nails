@extends('layouts.app')

@section('title', 'Order #'.$order->order_number.' — Alishe Nails')

@section('content')
    <div class="container" style="max-width:720px;padding-block:48px 64px;">
        <div class="breadcrumb">
            <a href="{{ route('home') }}">Home</a> &nbsp;&gt;&nbsp;
            <a href="{{ route('account.orders') }}">My Orders</a> &nbsp;&gt;&nbsp;
            <span>#{{ $order->order_number }}</span>
        </div>

        <h1 style="font-size:1.6rem;">Order #{{ $order->order_number }}</h1>
        <p style="color:rgba(43,29,29,.6);">Placed {{ $order->created_at->format('M j, Y') }} &middot;
            <span class="status-pill status-{{ $order->status }}">{{ ucfirst($order->status) }}</span>
        </p>

        <div class="summary-card" style="margin-top:20px;">
            @foreach ($order->items as $item)
                <div class="summary-row">
                    <span>{{ $item->product_name }} &times; {{ $item->quantity }}</span>
                    <span>PKR {{ number_format($item->line_total, 0) }}</span>
                </div>
            @endforeach
            <div class="summary-row"><span>Shipping</span><span>{{ $order->shipping == 0 ? 'Free' : 'PKR '.number_format($order->shipping, 0) }}</span></div>
            <div class="summary-row total"><span>Total</span><span>PKR {{ number_format($order->total, 0) }}</span></div>
        </div>

        <div class="checkout-card" style="margin-top:20px;">
            <h3>Shipping Address</h3>
            <p style="margin:0;">{{ $order->address }}, {{ $order->city }}</p>
        </div>
    </div>
@endsection
