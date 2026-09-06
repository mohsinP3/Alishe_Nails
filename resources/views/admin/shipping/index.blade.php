@extends('layouts.admin')
@section('title', 'Shipping Rates — Alishe Nails Admin')

@section('content')

    <div class="admin-page-head">
        <div>
            <h1>Shipping Rates &amp; Zones</h1>
            <p>Location-based delivery fees and free shipping thresholds</p>
        </div>
        <a href="{{ route('admin.shipping.create') }}" class="btn btn-primary">
            <i class="fa-solid fa-plus"></i> Add Shipping Rate
        </a>
    </div>

    <div class="admin-card">
        <form method="GET" action="{{ route('admin.shipping.index') }}" style="margin-bottom:20px;display:flex;gap:12px;">
            <input type="text" name="q" value="{{ request('q') }}" placeholder="Search city or area..." class="input-search" style="max-width:300px;">
            <button type="submit" class="btn btn-outline btn-sm">Search</button>
        </form>

        <table class="data-table">
            <thead>
                <tr>
                    <th>City</th>
                    <th>Area / Location</th>
                    <th>Delivery Fee</th>
                    <th>Free Shipping Above</th>
                    <th>Status</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse ($shippingRates as $rate)
                    <tr>
                        <td><strong>{{ $rate->city }}</strong></td>
                        <td>{{ $rate->area ?: 'All Areas (City Default)' }}</td>
                        <td>PKR {{ number_format($rate->delivery_fee, 0) }}</td>
                        <td>
                            @if ($rate->free_shipping_threshold !== null)
                                PKR {{ number_format($rate->free_shipping_threshold, 0) }}
                            @else
                                &mdash;
                            @endif
                        </td>
                        <td>
                            @if ($rate->is_active)
                                <span class="status-pill status-completed">Active</span>
                            @else
                                <span class="status-pill status-cancelled">Inactive</span>
                            @endif
                        </td>
                        <td style="display:flex;gap:8px;">
                            <a href="{{ route('admin.shipping.edit', $rate) }}" class="btn btn-outline btn-sm">Edit</a>
                            <form action="{{ route('admin.shipping.destroy', $rate) }}" method="POST"
                                  onsubmit="return confirm('Delete rate for {{ $rate->city }} {{ $rate->area }}?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm" style="background:#B3261E;color:#fff;border:none;">Delete</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" style="text-align:center;padding:30px;">No shipping rates configured.</td></tr>
                @endforelse
            </tbody>
        </table>

        <div class="pagination" style="justify-content:flex-start;margin-top:20px;">
            {{ $shippingRates->links() }}
        </div>
    </div>

@endsection
