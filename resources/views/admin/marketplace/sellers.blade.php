@extends('layouts.admin')
@section('title', 'Sellers — Alishe Nails Admin')
@section('content')
<div class="admin-page-head"><div><h1>Sellers</h1><p>All influencer seller applications.</p></div><a href="{{ route('admin.subscriptions.index') }}" class="btn btn-outline">Subscriptions</a></div>
<div class="admin-card" style="overflow-x:auto;"><table class="admin-table"><thead><tr><th>Name</th><th>Instagram</th><th>Status</th><th>Products</th><th></th></tr></thead><tbody>@foreach($sellers as $seller)<tr><td>{{ $seller->name }}<small style="display:block;opacity:.65;">{{ $seller->email }}</small></td><td>{{ $seller->instagram_handle ?: '—' }}</td><td>{{ ucfirst($seller->status) }}</td><td>{{ $seller->products_count }}</td><td><a href="{{ route('admin.marketplace.sellers.show',$seller) }}" class="btn btn-outline btn-sm">Details</a></td></tr>@endforeach</tbody></table>{{ $sellers->links() }}</div>
@endsection
