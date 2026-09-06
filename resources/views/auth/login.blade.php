@extends('layouts.app')
@section('title', 'Log In — Alishe Nails')

@section('content')
<div class="container" style="max-width:420px;padding-block:56px;">
    <h1 style="text-align:center;">Welcome Back</h1>
    <p style="text-align:center;color:rgba(43,29,29,.65);margin-bottom:28px;">Log in to view your orders and account.</p>

    <form action="{{ route('login') }}" method="POST" class="checkout-card">
        @csrf
        <div class="form-field" style="margin-bottom:16px;">
            <label for="email">Email Address</label>
            <input type="email" id="email" name="email" value="{{ old('email') }}" required autofocus>
            @error('email') <div class="error">{{ $message }}</div> @enderror
        </div>
        <div class="form-field" style="margin-bottom:12px;">
            <label for="password">Password</label>
            <input type="password" id="password" name="password" required>
        </div>
        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:20px;font-size:.85rem;">
            <label style="display:flex;align-items:center;gap:6px;">
                <input type="checkbox" name="remember"> Remember me
            </label>
            <a href="{{ route('password.request') }}" style="text-decoration:underline;">Forgot password?</a>
        </div>
        <button type="submit" class="btn btn-primary btn-block">Log In</button>
    </form>

    <p style="text-align:center;margin-top:20px;font-size:.9rem;">
        New here? <a href="{{ route('register') }}" style="text-decoration:underline;color:var(--rose-dark);">Create an account</a>
    </p>
</div>
@endsection
