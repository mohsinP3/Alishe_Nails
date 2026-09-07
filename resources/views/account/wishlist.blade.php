@extends('layouts.app')
@section('title', 'My Wishlist — Alishe Nails')

@section('content')
    <div class="container" style="padding-block:48px 64px;">
        <div class="breadcrumb">
            <a href="{{ route('home') }}">Home</a> &nbsp;&gt;&nbsp;
            <a href="{{ route('account.profile') }}">My Account</a> &nbsp;&gt;&nbsp;
            <span>My Wishlist</span>
        </div>

        <h1 style="font-size:1.7rem;margin-bottom:24px;">My Wishlist</h1>

        @if ($products->isEmpty())
            <div class="checkout-card">
                <p style="margin:0;">You have not saved any products yet.</p>
                <a href="{{ route('shop.index') }}" class="btn btn-primary" style="margin-top:16px;">Browse Products</a>
            </div>
        @else
            <div class="product-grid">
                @foreach ($products as $product)
                    <x-product-card :product="$product" />
                @endforeach
            </div>

            @if ($products->hasPages())
                <nav class="pagination" aria-label="Wishlist pages">
                    {{ $products->links() }}
                </nav>
            @endif
        @endif
    </div>
@endsection
