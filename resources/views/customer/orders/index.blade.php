@extends('layouts.app')
@section('title', 'My Orders — Alishe Nails')

@section('content')
    <div class="container" style="padding-block:48px 64px;">
        <div class="breadcrumb"><a href="{{ route('home') }}">Home</a> &nbsp;&gt;&nbsp; <span>My Orders</span></div>

        <h1 style="font-size:1.7rem;margin-bottom:24px;">My Orders</h1>

        @if ($orders->isEmpty())
            <div class="cart-empty">
                <i class="fa-solid fa-receipt" style="font-size:2.2rem;color:var(--rose);"></i>
                <h3 style="margin-top:16px;">No orders yet</h3>
                <p>When you place an order, it'll show up here.</p>
                <a href="{{ route('shop.index') }}" class="btn btn-primary">Shop the Collection</a>
            </div>
        @else
            <div class="admin-card" style="background:#fff;border-radius:var(--radius-md);">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Order</th>
                            <th>Date</th>
                            <th>Total</th>
                            <th>Status</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($orders as $order)
                            <tr>
                                <td>#{{ $order->order_number }}</td>
                                <td>{{ $order->created_at->format('M j, Y') }}</td>
                                <td>PKR {{ number_format($order->total, 0) }}</td>
                                <td><span class="status-pill status-{{ $order->status }}">{{ ucwords(str_replace('_', ' ', $order->status)) }}</span></td>
                                <td><a href="{{ route('account.orders.show', $order) }}" class="btn btn-outline btn-sm">View</a></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                <div class="pagination" style="justify-content:flex-start;margin-top:20px;">
                    {{ $orders->links() }}
                </div>
            </div>
        @endif
    </div>
@endsection
