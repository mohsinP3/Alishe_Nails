@extends('layouts.admin')

@section('title', 'Settings — Alishe Nails Admin')

@section('content')

    <div class="admin-page-head">
        <div>
            <h1>Settings</h1>
            <p>Store contact info and admin account.</p>
        </div>
    </div>

    <div class="dashboard-grid">
        <div class="admin-card">
            <div class="admin-card__head"><h3>Store &amp; Payment Configuration</h3></div>
            <p style="font-size:.85rem;color:rgba(43,29,29,.65);margin-bottom:16px;">
                These values come from your <code>.env</code> file (e.g. <code>BANK_NAME</code>, <code>JAZZCASH_NUMBER</code>, <code>WHATSAPP_NUMBER</code>). Edit your <code>.env</code> file directly to update them.
            </p>

            <div class="form-field" style="margin-bottom:12px;">
                <label>WhatsApp Number</label>
                <input type="text" value="{{ config('services.whatsapp.number') }}" disabled>
            </div>
            <div class="form-field" style="margin-bottom:12px;">
                <label>Instagram Handle</label>
                <input type="text" value="{{ '@'.config('services.instagram.handle') }}" disabled>
            </div>
            <div class="form-field" style="margin-bottom:12px;">
                <label>Bank Account ({{ config('services.payment.bank_name') }})</label>
                <input type="text" value="{{ config('services.payment.bank_account_title') }} &middot; {{ config('services.payment.bank_account_number') }}" disabled>
            </div>
            <div class="form-field" style="margin-bottom:12px;">
                <label>JazzCash Account</label>
                <input type="text" value="{{ config('services.payment.jazzcash_account_title') }} &middot; {{ config('services.payment.jazzcash_number') }}" disabled>
            </div>
            <div class="form-field">
                <label>EasyPaisa Account</label>
                <input type="text" value="{{ config('services.payment.easypaisa_account_title') }} &middot; {{ config('services.payment.easypaisa_number') }}" disabled>
            </div>
        </div>

        <div class="admin-card">
            <div class="admin-card__head"><h3>Change Admin Password</h3></div>
            <form action="{{ route('admin.settings.password') }}" method="POST">
                @csrf
                <div class="form-field" style="margin-bottom:14px;">
                    <label for="current_password">Current Password</label>
                    <input type="password" id="current_password" name="current_password" required>
                    @error('current_password') <div class="error">{{ $message }}</div> @enderror
                </div>
                <div class="form-field" style="margin-bottom:14px;">
                    <label for="new_password">New Password</label>
                    <input type="password" id="new_password" name="new_password" required>
                    @error('new_password') <div class="error">{{ $message }}</div> @enderror
                </div>
                <div class="form-field" style="margin-bottom:18px;">
                    <label for="new_password_confirmation">Confirm New Password</label>
                    <input type="password" id="new_password_confirmation" name="new_password_confirmation" required>
                </div>
                <button type="submit" class="btn btn-primary btn-block">Update Password</button>
            </form>
        </div>
    </div>

@endsection
