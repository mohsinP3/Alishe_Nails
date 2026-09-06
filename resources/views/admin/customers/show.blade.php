@extends('layouts.admin')
@section('title', $customer->name.' — Alishe Nails Admin')

@section('content')

    <div class="admin-page-head">
        <div>
            <h1>{{ $customer->name }}</h1>
            <p>Customer since {{ $customer->created_at->format('M j, Y') }}</p>
        </div>
        <a href="{{ route('admin.customers.index') }}" class="btn btn-outline">&larr; Back to Customers</a>
    </div>

    <div class="dashboard-grid">
        <div class="admin-card">
            <div class="admin-card__head"><h3>Order History</h3></div>
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Order</th>
                        <th>Date</th>
                        <th>Total</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($customer->orders as $order)
                        <tr>
                            <td><a href="{{ route('admin.orders.show', $order) }}">#{{ $order->order_number }}</a></td>
                            <td>{{ $order->created_at->format('M j, Y') }}</td>
                            <td>PKR {{ number_format($order->total, 0) }}</td>
                            <td><span class="status-pill status-{{ $order->status }}">{{ ucfirst($order->status) }}</span></td>
                        </tr>
                    @empty
                        <tr><td colspan="4" style="text-align:center;padding:24px;">No orders yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="admin-card">
            <div class="admin-card__head"><h3>Contact Info</h3></div>
            {{-- Never display password hashes or tokens — only safe, non-secret fields. --}}
            <p style="margin:0 0 6px;font-size:.85rem;"><i class="fa-solid fa-envelope"></i> {{ $customer->email }}</p>
            <p style="margin:0 0 6px;font-size:.85rem;"><i class="fa-solid fa-phone"></i> {{ $customer->phone ?? '—' }}</p>
            <p style="margin:0;font-size:.85rem;"><i class="fa-solid fa-cart-shopping"></i> {{ $customer->orders->count() }} total orders</p>
        </div>
    </div>
@endsection
