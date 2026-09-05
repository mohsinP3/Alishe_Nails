@extends('layouts.admin')

@section('title', ($shipping->exists ? 'Edit' : 'Add').' Shipping Rate — Alishe Nails Admin')

@section('content')

    <div class="admin-page-head">
        <div>
            <h1>{{ $shipping->exists ? 'Edit Shipping Rate' : 'Add Shipping Rate' }}</h1>
            <p>Configure delivery fees and free shipping conditions for specific areas</p>
        </div>
        <a href="{{ route('admin.shipping.index') }}" class="btn btn-outline">
            <i class="fa-solid fa-arrow-left"></i> Back to Shipping Rates
        </a>
    </div>

    <div class="admin-card" style="max-width:600px;">
        <form action="{{ $shipping->exists ? route('admin.shipping.update', $shipping) : route('admin.shipping.store') }}" method="POST">
            @csrf
            @if ($shipping->exists)
                @method('PUT')
            @endif

            <div class="form-field" style="margin-bottom:16px;">
                <label for="city">City *</label>
                <input type="text" id="city" name="city" value="{{ old('city', $shipping->city ?? 'Karachi') }}" placeholder="e.g. Karachi, Lahore" required>
                @error('city') <div class="error">{{ $message }}</div> @errorEnd
            </div>

            <div class="form-field" style="margin-bottom:16px;">
                <label for="area">Area / Location (Optional)</label>
                <input type="text" id="area" name="area" value="{{ old('area', $shipping->area) }}" placeholder="e.g. Korangi 2 1/2, DHA, PECHS (leave blank for City default)">
                <small style="color:rgba(43,29,29,.6);font-size:.78rem;">Leave empty if this applies to all unlisted areas in the city.</small>
                @error('area') <div class="error">{{ $message }}</div> @errorEnd
            </div>

            <div class="form-field" style="margin-bottom:16px;">
                <label for="delivery_fee">Delivery Fee (PKR) *</label>
                <input type="number" step="0.01" min="0" id="delivery_fee" name="delivery_fee" value="{{ old('delivery_fee', $shipping->delivery_fee ?? 200) }}" required>
                @error('delivery_fee') <div class="error">{{ $message }}</div> @errorEnd
            </div>

            <div class="form-field" style="margin-bottom:16px;">
                <label for="free_shipping_threshold">Free Shipping Threshold (PKR)</label>
                <input type="number" step="0.01" min="0" id="free_shipping_threshold" name="free_shipping_threshold" value="{{ old('free_shipping_threshold', $shipping->free_shipping_threshold ?? 5000) }}" placeholder="e.g. 5000">
                <small style="color:rgba(43,29,29,.6);font-size:.78rem;">Orders with subtotal at or above this amount will get FREE delivery.</small>
                @error('free_shipping_threshold') <div class="error">{{ $message }}</div> @errorEnd
            </div>

            <div class="form-field" style="margin-bottom:24px;">
                <label style="display:flex;align-items:center;gap:8px;cursor:pointer;">
                    <input type="checkbox" name="is_active" value="1" {{ old('is_active', $shipping->is_active ?? true) ? 'checked' : '' }}>
                    <span>Active Shipping Rate</span>
                </label>
            </div>

            <button type="submit" class="btn btn-primary">
                {{ $shipping->exists ? 'Update Rate' : 'Create Rate' }}
            </button>
        </form>
    </div>

@endsection
