@extends('layouts.app')
@section('title', 'Seller Dashboard — Alishe Nails')
@section('content')
<div class="page-header"><h1>Welcome, {{ $seller->name }}</h1><p>Your marketplace workspace.</p></div>
<div class="container" style="padding-block:36px 80px;">
    <div class="seller-stats"><div><span>Total sales</span><strong>PKR {{ number_format($sales, 0) }}</strong></div><div><span>Your earnings</span><strong>PKR {{ number_format($earnings, 0) }}</strong></div><div><span>Subscription status</span><strong>{{ $seller->activeSubscription ? 'Active' : 'Inactive' }}</strong></div></div>
    <div class="seller-subscription-card"><div><span class="eyebrow">My Subscription</span>@if($seller->activeSubscription)<h2>{{ $seller->activeSubscription->plan->name }}</h2><p>Valid until {{ $seller->activeSubscription->expires_at->format('d M Y') }}</p>@else<h2>No active subscription</h2><p>Your products are hidden until a subscription payment is verified.</p>@endif</div><a href="{{ route('seller.subscription') }}" class="btn btn-primary">{{ $seller->activeSubscription ? 'Renew Now' : 'Choose a Plan' }}</a></div>
    <div class="admin-page-head"><div><h2>Your Products</h2><p>Products are listed under your seller name.</p></div><a href="{{ route('seller.products.create') }}" class="btn btn-primary"><i class="fa-solid fa-plus"></i> Add Product</a></div>
    <div class="admin-card" style="overflow-x:auto;"><table class="admin-table"><thead><tr><th>Product</th><th>Price</th><th>Stock</th><th>Status</th><th></th></tr></thead><tbody>@forelse($products as $product)<tr><td>{{ $product->name }}</td><td>PKR {{ number_format($product->price, 0) }}</td><td>{{ $product->stock }}</td><td>{{ $product->is_active ? 'Live' : 'Hidden' }}</td><td><a href="{{ route('seller.products.edit', $product) }}" class="btn btn-outline btn-sm">Edit</a></td></tr>@empty<tr><td colspan="5">No products yet.</td></tr>@endforelse</tbody></table>{{ $products->links() }}</div>
    <div class="admin-page-head" style="margin-top:42px;"><div><h2>Recent Orders</h2><p>Your sales are shown without a platform commission deduction.</p></div></div>
    <div class="admin-card" style="overflow-x:auto;"><table class="admin-table"><thead><tr><th>Order</th><th>Product</th><th>Qty</th><th>Sale</th></tr></thead><tbody>@forelse($items as $item)<tr><td>#{{ $item->order->order_number }}</td><td>{{ $item->product_name }}</td><td>{{ $item->quantity }}</td><td>PKR {{ number_format($item->line_total, 0) }}</td></tr>@empty<tr><td colspan="4">No orders yet.</td></tr>@endforelse</tbody></table>{{ $items->links() }}</div>
    <form action="{{ route('seller.logout') }}" method="POST" style="margin-top:22px;">@csrf<button class="btn btn-outline" type="submit">Sign out</button></form>
</div>
@endsection
