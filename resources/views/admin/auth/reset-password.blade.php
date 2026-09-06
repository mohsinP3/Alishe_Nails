@extends('layouts.app')
@section('title', 'Reset Password — Alishe Nails')

@section('content')
<div class="container" style="max-width:420px;padding-block:56px;">
    <h1 style="text-align:center;">Set a New Password</h1>

    <form action="{{ route('password.store') }}" method="POST" class="checkout-card">
        @csrf
        <input type="hidden" name="token" value="{{ $token }}">
        <div class="form-field" style="margin-bottom:16px;">
            <label for="email">Email Address</label>
            <input type="email" id="email" name="email" value="{{ old('email', $email) }}" required autofocus>
            @error('email') <div class="error">{{ $message }}</div> @enderror
        </div>
        <div class="form-field" style="margin-bottom:16px;">
            <label for="password">New Password</label>
            <input type="password" id="password" name="password" required>
            @error('password') <div class="error">{{ $message }}</div> @enderror
        </div>
        <div class="form-field" style="margin-bottom:20px;">
            <label for="password_confirmation">Confirm New Password</label>
            <input type="password" id="password_confirmation" name="password_confirmation" required>
        </div>
        <button type="submit" class="btn btn-primary btn-block">Reset Password</button>
    </form>
</div>
@endsection
