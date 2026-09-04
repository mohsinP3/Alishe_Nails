@extends('layouts.admin')

@section('title', 'Customers — Alishe Nails Admin')

@section('content')

    <div class="admin-page-head">
        <div>
            <h1>Customers</h1>
            <p>{{ $customers->total() }} customers, based on order history</p>
        </div>
    </div>

    <div class="admin-card">
        <form method="GET" action="{{ route('admin.customers.index') }}" style="margin-bottom:20px;">
            <input class="input-search" type="text" name="q" value="{{ request('q') }}" placeholder="Search by name or email...">
        </form>

        <table class="data-table">
            <thead>
                <tr>
                    <th>Customer</th>
                    <th>Contact</th>
                    <th>City</th>
                    <th>Orders</th>
                    <th>Total Spent</th>
                    <th>Last Order</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($customers as $customer)
                    <tr>
                        <td>
                            <span class="table-avatar">{{ strtoupper(substr($customer->first_name, 0, 1).substr($customer->last_name, 0, 1)) }}</span>
                            {{ $customer->first_name }} {{ $customer->last_name }}
                        </td>
                        <td>
                            <div>{{ $customer->email }}</div>
                            <div style="font-size:.78rem;color:rgba(43,29,29,.6);">{{ $customer->phone }}</div>
                        </td>
                        <td>{{ $customer->city }}</td>
                        <td>{{ $customer->orders_count }}</td>
                        <td>PKR {{ number_format($customer->total_spent, 0) }}</td>
                        <td>{{ \Illuminate\Support\Carbon::parse($customer->last_order_at)->format('M j, Y') }}</td>
                    </tr>
                @empty
                    <tr><td colspan="6" style="text-align:center;padding:30px;">No customers yet — they'll appear here after the first order.</td></tr>
                @endforelse
            </tbody>
        </table>

        <div class="pagination" style="justify-content:flex-start;margin-top:20px;">
            {{ $customers->links() }}
        </div>
    </div>

@endsection
