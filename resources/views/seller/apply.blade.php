@extends('layouts.app')
@section('title', 'Sell on Alishe Nails')
@section('content')
<div class="page-header"><h1>Sell Your Designs</h1><p>Bring your community and your nail art to Alishe Nails.</p></div>
<div class="container" style="max-width:820px;padding-block:40px 80px;">
    <form action="{{ route('seller.apply.store') }}" method="POST" class="checkout-card">
        @csrf
        <div class="form-grid">
            <div class="form-field"><label for="name">Your name / brand</label><input id="name" name="name" value="{{ old('name') }}" required>@error('name')<div class="error">{{ $message }}</div>@enderror</div>
            <div class="form-field"><label for="email">Email</label><input id="email" type="email" name="email" value="{{ old('email') }}" required>@error('email')<div class="error">{{ $message }}</div>@enderror</div>
            <div class="form-field"><label for="instagram_handle">Instagram handle</label><input id="instagram_handle" name="instagram_handle" placeholder="@yourhandle" value="{{ old('instagram_handle') }}"></div>
            <div class="form-field"><label for="phone">WhatsApp / phone</label><input id="phone" name="phone" value="{{ old('phone') }}"></div>
            <div class="form-field full"><label for="product_details">What designs would you like to sell?</label><textarea id="product_details" name="product_details" rows="5" required>{{ old('product_details') }}</textarea>@error('product_details')<div class="error">{{ $message }}</div>@enderror</div>
            <div class="form-field"><label for="password">Dashboard password</label><input id="password" type="password" name="password" required>@error('password')<div class="error">{{ $message }}</div>@enderror</div>
            <div class="form-field"><label for="password_confirmation">Confirm password</label><input id="password_confirmation" type="password" name="password_confirmation" required></div>
        </div>
        <button class="btn btn-primary" type="submit"><i class="fa-solid fa-paper-plane"></i> Submit Application</button>
        <p style="font-size:.85rem;margin-top:14px;">Already approved? <a href="{{ route('seller.login') }}" style="text-decoration:underline;">Seller login</a></p>
    </form>
</div>
@endsection
