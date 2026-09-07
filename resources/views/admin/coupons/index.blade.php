@extends('layouts.admin')
@section('title', 'Coupons — Alishe Nails Admin')

@section('content')
    <div class="admin-page-head">
        <div>
            <h1>Coupons</h1>
            <p>Manage store discount codes.</p>
        </div>
    </div>

    <div class="admin-grid" style="grid-template-columns: minmax(0, 360px) 1fr; gap: 24px;">
        <div class="admin-card">
            <div class="admin-card__head">
                <h3>Create Coupon</h3>
            </div>

            <form action="{{ route('admin.coupons.store') }}" method="POST" class="form-grid">
                @csrf
                <div class="form-field full">
                    <label for="code">Code</label>
                    <input id="code" name="code" value="{{ old('code') }}" placeholder="SAVE10" required>
                    @error('code') <div class="error">{{ $message }}</div> @enderror
                </div>

                <div class="form-field full">
                    <label for="type">Discount Type</label>
                    <select id="type" name="type" required>
                        <option value="fixed" {{ old('type') === 'fixed' ? 'selected' : '' }}>Fixed amount</option>
                        <option value="percent" {{ old('type') === 'percent' ? 'selected' : '' }}>Percent off</option>
                    </select>
                    @error('type') <div class="error">{{ $message }}</div> @enderror
                </div>

                <div class="form-field full">
                    <label for="value">Value</label>
                    <input id="value" type="number" step="0.01" min="0.01" name="value" value="{{ old('value') }}" required>
                    @error('value') <div class="error">{{ $message }}</div> @enderror
                </div>

                <div class="form-field full">
                    <label for="expires_at">Expiry Date</label>
                    <input id="expires_at" type="date" name="expires_at" value="{{ old('expires_at') }}">
                    @error('expires_at') <div class="error">{{ $message }}</div> @enderror
                </div>

                <div class="form-field full">
                    <label for="usage_limit">Usage Limit</label>
                    <input id="usage_limit" type="number" min="1" name="usage_limit" value="{{ old('usage_limit') }}" placeholder="Optional">
                    @error('usage_limit') <div class="error">{{ $message }}</div> @enderror
                </div>

                <button type="submit" class="btn btn-primary">Create Coupon</button>
            </form>
        </div>

        <div class="admin-card">
            <div class="admin-card__head">
                <h3>All Coupons</h3>
            </div>

            <table class="data-table">
                <thead>
                    <tr>
                        <th>Code</th>
                        <th>Type</th>
                        <th>Value</th>
                        <th>Expiry</th>
                        <th>Uses</th>
                        <th>Status</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($coupons as $coupon)
                        <tr>
                            <td><strong>{{ $coupon->code }}</strong></td>
                            <td>{{ $coupon->type === 'fixed' ? 'Fixed' : 'Percent' }}</td>
                            <td>{{ $coupon->type === 'fixed' ? 'PKR '.number_format($coupon->value, 0) : $coupon->value.'%' }}</td>
                            <td>{{ $coupon->expires_at ? $coupon->expires_at->format('M j, Y') : 'No expiry' }}</td>
                            <td>{{ $coupon->used_count }} / {{ $coupon->usage_limit ?? '∞' }}</td>
                            <td>
                                @if ($coupon->is_active)
                                    <span class="status-pill status-completed">Active</span>
                                @else
                                    <span class="status-pill status-cancelled">Inactive</span>
                                @endif
                            </td>
                            <td>
                                <form action="{{ route('admin.coupons.destroy', $coupon) }}" method="POST" onsubmit="return confirm('Delete this coupon?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm" style="background:#B3261E;color:#fff;border:none;">Delete</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="7" style="text-align:center;padding:30px;">No coupons created yet.</td></tr>
                    @endforelse
                </tbody>
            </table>

            <div class="pagination" style="justify-content:flex-start;margin-top:20px;">
                {{ $coupons->links() }}
            </div>
        </div>
    </div>
@endsection
