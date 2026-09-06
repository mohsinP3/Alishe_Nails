@extends('layouts.app')
@section('title', 'Create Account — Alishe Nails')

@section('content')
<div class="container" style="max-width:480px;padding-block:56px;">
    <h1 style="text-align:center;">Create an Account</h1>
    <p style="text-align:center;color:rgba(43,29,29,.65);margin-bottom:28px;">Join Alishe Nails to track your orders and save your details.</p>

    <form action="{{ route('register') }}" method="POST" class="checkout-card">
        @csrf
        <div class="form-field" style="margin-bottom:16px;">
            <label for="name">Full Name</label>
            <input type="text" id="name" name="name" value="{{ old('name') }}" required autofocus>
            @error('name') <div class="error">{{ $message }}</div> @enderror
        </div>
        <div class="form-field" style="margin-bottom:16px;">
            <label for="email">Email Address</label>
            <input type="email" id="email" name="email" value="{{ old('email') }}" required>
            @error('email') <div class="error">{{ $message }}</div> @enderror
        </div>
        <div class="form-field" style="margin-bottom:16px;">
            <label for="phone">Phone (optional)</label>
            <input type="text" id="phone" name="phone" value="{{ old('phone') }}">
        </div>
        <div class="form-field" style="margin-bottom:16px;">
            <label for="password">Password</label>
            <input type="password" id="password" name="password" required>
            <div style="font-size:.75rem;color:rgba(43,29,29,.55);margin-top:4px;">At least 8 characters, with letters and numbers.</div>
            @error('password') <div class="error">{{ $message }}</div> @enderror
        </div>
        <div class="form-field" style="margin-bottom:20px;">
            <label for="password_confirmation">Confirm Password</label>
            <input type="password" id="password_confirmation" name="password_confirmation" required>
        </div>
        <button type="submit" class="btn btn-primary btn-block">Create Account</button>
    </form>

    <p style="text-align:center;margin-top:20px;font-size:.9rem;">
        Already have an account? <a href="{{ route('login') }}" style="text-decoration:underline;color:var(--rose-dark);">Log in</a>
    </p>
</div>
@endsection
