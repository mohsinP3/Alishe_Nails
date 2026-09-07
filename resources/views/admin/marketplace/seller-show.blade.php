@extends('layouts.admin')
@section('title', $seller->name.' — Seller')
@section('content')
<div class="admin-page-head"><div><h1>{{ $seller->name }}</h1><p>{{ $seller->email }} · {{ $seller->instagram_handle ?: 'No Instagram handle' }}</p></div><a href="{{ route('admin.marketplace.sellers.index') }}" class="btn btn-outline">Back to Sellers</a></div>
<div class="admin-card"><h3>Application details</h3><p style="white-space:pre-line;">{{ $seller->product_details }}</p><p><strong>Status:</strong> {{ ucfirst($seller->status) }}</p><a href="{{ route('admin.subscriptions.index') }}" class="btn btn-primary">Manage Subscription</a></div>
<div class="admin-card" style="margin-top:24px;overflow-x:auto;"><h3>Seller orders</h3><table class="admin-table"><thead><tr><th>Order</th><th>Product</th><th>Quantity</th><th>Sale total</th></tr></thead><tbody>@forelse($items as $item)<tr><td>#{{ $item->order->order_number }}</td><td>{{ $item->product_name }}</td><td>{{ $item->quantity }}</td><td>PKR {{ number_format($item->line_total,0) }}</td></tr>@empty<tr><td colspan="4">No seller orders yet.</td></tr>@endforelse</tbody></table>{{ $items->links() }}</div>
@endsection
