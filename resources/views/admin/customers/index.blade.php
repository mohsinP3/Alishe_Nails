@extends('layouts.admin')
@section('title', 'Customers — Alishe Nails Admin')

@section('content')

    <div class="admin-page-head">
        <div>
            <h1>Customers</h1>
            <p>{{ $customers->total() }} registered customers @if($guestOrderCount > 0)&middot; {{ $guestOrderCount }} guest checkout(s) with no account @endif</p>
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
                    <th>Orders</th>
                    <th>Total Spent</th>
                    <th>Joined</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse ($customers as $customer)
                    <tr>
                        <td>
                            <span class="table-avatar">{{ strtoupper(substr($customer->name, 0, 2)) }}</span>
                            {{ $customer->name }}
                        </td>
                        <td>
                            <div>{{ $customer->email }}</div>
                            <div style="font-size:.78rem;color:rgba(43,29,29,.6);">{{ $customer->phone ?? '—' }}</div>
                        </td>
                        <td>{{ $customer->orders_count }}</td>
                        <td>PKR {{ number_format($customer->orders_sum_total ?? 0, 0) }}</td>
                        <td>{{ $customer->created_at->format('M j, Y') }}</td>
                        <td><a href="{{ route('admin.customers.show', $customer) }}" class="btn btn-outline btn-sm">View</a></td>
                    </tr>
                @empty
                    <tr><td colspan="6" style="text-align:center;padding:30px;">No registered customers yet.</td></tr>
                @endforelse
            </tbody>
        </table>

        <div class="pagination" style="justify-content:flex-start;margin-top:20px;">
            {{ $customers->links() }}
        </div>
    </div>

@endsection
