@extends('layouts.app')
@section('title', 'Your Cart — Alishe Nails')

@section('content')

    <div class="container">
        <div class="breadcrumb">
            <a href="{{ route('home') }}">Home</a> &nbsp;&gt;&nbsp; <span>Cart</span>
        </div>

        @if (empty($items))
            <div class="cart-empty">
                <i class="fa-solid fa-bag-shopping" style="font-size:2.4rem;color:var(--rose);"></i>
                <h2 style="margin-top:16px;">Your cart is empty</h2>
                <p>Looks like you haven't added anything yet.</p>
                <a href="{{ route('shop.index') }}" class="btn btn-primary">Shop the Collection</a>
            </div>
        @else
            <div class="cart-layout">
                <div>
                    <h1 style="font-size:1.6rem;">Shopping Cart</h1>

                    @foreach ($items as $rowId => $item)
                        <div class="cart-item">
                            <div class="cart-item__image">
                                @php($url = $item['image'] && file_exists(public_path('images/products/'.$item['image'])) ? asset('images/products/'.$item['image']) : null)
                                @if ($url)
                                    <img src="{{ $url }}" alt="{{ $item['name'] }}">
                                @else
                                    <div class="img-placeholder" style="font-size:.6rem;">No image</div>
                                @endif
                            </div>

                            <div>
                                <div class="cart-item__name">{{ $item['name'] }}</div>
                                <div class="cart-item__meta">
                                    @if ($item['shape']) Shape: {{ $item['shape'] }} @endif
                                    @if ($item['size']) &middot; Size: {{ $item['size'] }} @endif
                                </div>
                                <div class="cart-item__meta">PKR {{ number_format($item['price'], 0) }}</div>

                                <form action="{{ route('cart.remove', $rowId) }}" method="POST" style="margin-top:8px;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="cart-item__remove">Remove</button>
                                </form>
                            </div>

                            <form action="{{ route('cart.update', $rowId) }}" method="POST" class="cart-item__qty">
                                @csrf
                                @method('PATCH')
                                <div class="qty-selector" data-qty-selector data-auto-submit="true">
                                    <button type="button" data-action="decrease" aria-label="Decrease quantity">&minus;</button>
                                    <input type="number" name="qty" value="{{ $item['qty'] }}" min="0" max="20" aria-label="Quantity">
                                    <button type="button" data-action="increase" aria-label="Increase quantity">&plus;</button>
                                </div>
                            </form>

                            <div class="cart-item__price" style="font-weight:600;">
                                PKR {{ number_format($item['price'] * $item['qty'], 0) }}
                            </div>
                        </div>
                    @endforeach

                    <a href="{{ route('shop.index') }}" style="display:inline-block;margin-top:20px;text-decoration:underline;">&larr; Continue Shopping</a>
                </div>

                <div class="summary-card">
                    <h3 style="margin-bottom:20px;">Order Summary</h3>
                    <div class="summary-row"><span>Subtotal</span><span>PKR {{ number_format($subtotal, 0) }}</span></div>
                    <div class="summary-row">
                        <span>Shipping</span>
                        <span style="font-size:.85rem;text-align:right;">At checkout</span>
                    </div>
                    <p style="font-size:.8rem;color:rgba(43,29,29,.65);margin:12px 0 0;">Delivery is calculated in PKR after you enter your Pakistan city and optional area.</p>

                    <a href="{{ route('checkout.index') }}" class="btn btn-primary btn-block" style="margin-top:20px;">Proceed to Checkout</a>
                </div>
            </div>
        @endif
    </div>

@endsection
