@extends('layouts.app')
@section('title', 'Forgot Password — Alishe Nails')

@section('content')
<div class="container" style="max-width:420px;padding-block:56px;">
    <h1 style="text-align:center;">Reset Your Password</h1>
    <p style="text-align:center;color:rgba(43,29,29,.65);margin-bottom:28px;">Enter your email and we'll send you a reset link.</p>

    <form action="{{ route('password.email') }}" method="POST" class="checkout-card">
        @csrf
        <div class="form-field" style="margin-bottom:20px;">
            <label for="email">Email Address</label>
            <input type="email" id="email" name="email" value="{{ old('email') }}" required autofocus>
            @error('email') <div class="error">{{ $message }}</div> @enderror
        </div>
        <button type="submit" class="btn btn-primary btn-block">Send Reset Link</button>
    </form>
</div>
@endsection
