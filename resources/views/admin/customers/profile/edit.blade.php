@extends('layouts.app')

@section('title', 'My Account — Alishe Nails')

@section('content')
    <div class="container" style="max-width:640px;padding-block:48px 64px;">
        <div class="breadcrumb"><a href="{{ route('home') }}">Home</a> &nbsp;&gt;&nbsp; <span>My Account</span></div>

        <h1 style="font-size:1.7rem;margin-bottom:24px;">My Account</h1>

        <div class="checkout-card">
            <h3 style="margin-bottom:18px;">Profile Details</h3>
            <form action="{{ route('account.profile.update') }}" method="POST">
                @csrf
                @method('PATCH')
                <div class="form-grid">
                    <div class="form-field full">
                        <label for="name">Name</label>
                        <input type="text" id="name" name="name" value="{{ old('name', $user->name) }}" required>
                        @error('name') <div class="error">{{ $message }}</div> @enderror
                    </div>
                    <div class="form-field full">
                        <label for="email">Email</label>
                        <input type="email" id="email" name="email" value="{{ old('email', $user->email) }}" required>
                        @error('email') <div class="error">{{ $message }}</div> @enderror
                    </div>
                    <div class="form-field full">
                        <label for="phone">Phone</label>
                        <input type="text" id="phone" name="phone" value="{{ old('phone', $user->phone) }}">
                    </div>
                </div>
                <button type="submit" class="btn btn-primary" style="margin-top:16px;">Save Changes</button>
            </form>
        </div>

        <div class="checkout-card">
            <h3 style="margin-bottom:18px;">Change Password</h3>
            <form action="{{ route('account.profile.password') }}" method="POST">
                @csrf
                @method('PATCH')
                <div class="form-grid">
                    <div class="form-field full">
                        <label for="current_password">Current Password</label>
                        <input type="password" id="current_password" name="current_password" required>
                        @error('current_password') <div class="error">{{ $message }}</div> @enderror
                    </div>
                    <div class="form-field full">
                        <label for="password">New Password</label>
                        <input type="password" id="password" name="password" required>
                        @error('password') <div class="error">{{ $message }}</div> @enderror
                    </div>
                    <div class="form-field full">
                        <label for="password_confirmation">Confirm New Password</label>
                        <input type="password" id="password_confirmation" name="password_confirmation" required>
                    </div>
                </div>
                <button type="submit" class="btn btn-primary" style="margin-top:16px;">Update Password</button>
            </form>
        </div>

        <a href="{{ route('account.orders') }}" style="text-decoration:underline;">View My Orders &rarr;</a>
    </div>
@endsection
