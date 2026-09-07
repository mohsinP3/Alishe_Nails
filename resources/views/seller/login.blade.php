@extends('layouts.app')
@section('title', 'Seller Login — Alishe Nails')
@section('content')
<div class="page-header"><h1>Seller Dashboard</h1><p>Manage your designs, orders, and earnings.</p></div>
<div class="container" style="max-width:480px;padding-block:40px 80px;">
    <form action="{{ route('seller.login.attempt') }}" method="POST" class="checkout-card">
        @csrf
        <div class="form-field"><label for="email">Email</label><input id="email" type="email" name="email" value="{{ old('email') }}" required>@error('email')<div class="error">{{ $message }}</div>@enderror</div>
        <div class="form-field"><label for="password">Password</label><input id="password" type="password" name="password" required></div>
        <label style="display:flex;gap:8px;align-items:center;margin:14px 0;font-size:.85rem;"><input type="checkbox" name="remember" value="1"> Remember me</label>
        <button class="btn btn-primary btn-block" type="submit">Sign In</button>
        <p style="font-size:.85rem;margin-top:14px;">Not a seller yet? <a href="{{ route('seller.apply') }}" style="text-decoration:underline;">Apply to sell</a></p>
    </form>
</div>
@endsection
