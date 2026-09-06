@extends('layouts.admin')
@section('title', 'Admin Login — Alishe Nails')

@section('content')
    <div class="login-box">
        <h2 style="text-align:center;margin-bottom:24px;">Admin Login</h2>

        <form action="{{ route('admin.login.attempt') }}" method="POST">
            @csrf
            <div class="form-field" style="margin-bottom:18px;">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" value="{{ old('email') }}" required autofocus>
                @error('email') <div class="error">{{ $message }}</div> @enderror
            </div>
            <div class="form-field" style="margin-bottom:18px;">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required>
                @error('password') <div class="error">{{ $message }}</div> @enderror
            </div>
            <button type="submit" class="btn btn-primary btn-block">Log In</button>
        </form>
    </div>
@endsection
