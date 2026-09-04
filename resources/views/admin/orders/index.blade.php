@extends('layouts.admin')

@section('title', 'Orders — Alishe Nails Admin')

@section('content')

    <div class="admin-page-head">
        <div>
            <h1>Orders</h1>
            <p>{{ $orders->total() }} total orders</p>
        </div>
    </div>

    <div class="admin-card">
        <form method="GET" action="{{ route('admin.orders.index') }}" style="display:flex;gap:12px;margin-bottom:20px;flex-wrap:wrap;">
            <input class="input-search" type="text" name="q" value="{{ request('q') }}" placeholder="Search order #, name, email...">
            <select class="select-sort" name="status" onchange="this.form.requestSubmit()">
                <option value="">All Statuses</option>
                @foreach (['pending', 'processing', 'completed', 'cancelled'] as $status)
                    <option value="{{ $status }}" {{ request('status') == $status ? 'selected' : '' }}>{{ ucfirst($status) }}</option>
                @endforeach
            </select>
            <button type="submit" class="btn btn-outline btn-sm">Filter</button>
        </form>

        <table class="data-table">
            <thead>
                <tr>
                    <th>Order ID</th>
                    <th>Customer</th>
                    <th>Date</th>
                    <th>Payment</th>
                    <th>Amount</th>
                    <th>Status</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse ($orders as $order)
                    <tr>
                        <td><a href="{{ route('admin.orders.show', $order) }}">#{{ $order->order_number }}</a></td>
                        <td>
                            <span class="table-avatar">{{ strtoupper(substr($order->first_name, 0, 1).substr($order->last_name, 0, 1)) }}</span>
                            {{ $order->first_name }} {{ $order->last_name }}
                        </td>
                        <td>{{ $order->created_at->format('M j, Y') }}</td>
                        <td style="text-transform:capitalize;">{{ str_replace('_', ' ', $order->payment_method) }}</td>
                        <td>PKR {{ number_format($order->total, 0) }}</td>
                        <td><span class="status-pill status-{{ $order->status }}">{{ ucfirst($order->status) }}</span></td>
                        <td><a href="{{ route('admin.orders.show', $order) }}" class="btn btn-outline btn-sm">View</a></td>
                    </tr>
                @empty
                    <tr><td colspan="7" style="text-align:center;padding:30px;">No orders match your filters.</td></tr>
                @endforelse
            </tbody>
        </table>

        <div class="pagination" style="justify-content:flex-start;margin-top:20px;">
            {{ $orders->links() }}
        </div>
    </div>

@endsection
