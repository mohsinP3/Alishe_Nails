@extends('layouts.app')
@section('title', 'Checkout — Alishe Nails')

@section('content')

    <div class="container">
        <div class="breadcrumb">
            <a href="{{ route('home') }}">Home</a> &nbsp;&gt;&nbsp;
            <a href="{{ route('cart.index') }}">Cart</a> &nbsp;&gt;&nbsp;
            <strong>Checkout</strong>
        </div>

        <form action="{{ route('checkout.store') }}" method="POST">
            @csrf

            <div class="checkout-layout">
                <div>
                    {{-- ---------- Shipping details ---------- --}}
                    <div class="checkout-card">
                        <h3><i class="fa-solid fa-truck"></i> Shipping Details</h3>

                        @guest('web')
                            <p style="font-size:.85rem;background:var(--ivory);padding:12px 14px;border-radius:8px;margin-top:14px;">
                                <a href="{{ route('login') }}" style="text-decoration:underline;font-weight:600;">Log in</a>
                                or <a href="{{ route('register') }}" style="text-decoration:underline;font-weight:600;">create an account</a>
                                to track this order, or continue as a guest below.
                            </p>
                        @endguest

                        <div class="form-grid">
                            <div class="form-field full">
                                <label for="email">Email Address</label>
                                <input type="email" id="email" name="email" placeholder="you@example.com" value="{{ old('email', $user?->email) }}" required>
                                @error('email') <div class="error">{{ $message }}</div> @enderror
                            </div>

                            <div class="form-field">
                                <label for="first_name">First Name</label>
                                <input type="text" id="first_name" name="first_name" placeholder="Jane" value="{{ old('first_name', $user ? explode(' ', $user->name)[0] : '') }}" required>
                                @error('first_name') <div class="error">{{ $message }}</div> @enderror
                            </div>

                            <div class="form-field">
                                <label for="last_name">Last Name</label>
                                <input type="text" id="last_name" name="last_name" placeholder="Doe" value="{{ old('last_name') }}" required>
                                @error('last_name') <div class="error">{{ $message }}</div> @enderror
                            </div>

                            <div class="form-field full">
                                <label for="address">Street Address</label>
                                <input type="text" id="address" name="address" placeholder="123 Blossom Lane, Suite 4B" value="{{ old('address') }}" required>
                                @error('address') <div class="error">{{ $message }}</div> @enderror
                            </div>

                            <div class="form-field">
                                <label for="city">City</label>
                                <input type="text" id="city" name="city" placeholder="Karachi" value="{{ old('city', 'Karachi') }}" required>
                                @error('city') <div class="error">{{ $message }}</div> @enderror
                            </div>

                            <div class="form-field">
                                <label for="area">Area</label>
                                <input type="text" id="area" name="area" placeholder="e.g. DHA, Gulshan" value="{{ old('area') }}">
                                @error('area') <div class="error">{{ $message }}</div> @enderror
                            </div>

                            <div class="form-field">
                                <label for="postal_code">Postal Code <span style="font-weight:400;opacity:.65;">(Optional)</span></label>
                                <input type="text" id="postal_code" name="postal_code" placeholder="e.g. 75500" value="{{ old('postal_code') }}">
                                @error('postal_code') <div class="error">{{ $message }}</div> @enderror
                            </div>

                            <div class="form-field">
                                <label for="phone">Phone Number</label>
                                <input type="text" id="phone" name="phone" placeholder="+92 300 1234567" value="{{ old('phone', $user?->phone) }}" required>
                                @error('phone') <div class="error">{{ $message }}</div> @enderror
                            </div>
                        </div>
                    </div>

                    {{-- ---------- Payment method ---------- --}}
                    <div class="checkout-card">
                        <h3><i class="fa-regular fa-credit-card"></i> Payment Method</h3>

                        @php($selectedPayment = old('payment_method', 'cod'))
                        <label class="payment-option {{ $selectedPayment === 'cod' ? 'is-selected' : '' }}">
                            <div class="payment-option__left">
                                <input type="radio" name="payment_method" value="cod" {{ $selectedPayment === 'cod' ? 'checked' : '' }}>
                                <div>
                                    <strong>Cash on Delivery</strong>
                                    <small>Pay when your order arrives.</small>
                                </div>
                            </div>
                            <i class="fa-solid fa-truck"></i>
                        </label>

                        <label class="payment-option {{ $selectedPayment === 'bank_transfer' ? 'is-selected' : '' }}">
                            <div class="payment-option__left">
                                <input type="radio" name="payment_method" value="bank_transfer" {{ $selectedPayment === 'bank_transfer' ? 'checked' : '' }}>
                                <div>
                                    <strong>Bank Transfer</strong>
                                    <small>Direct transfer to our account.</small>
                                </div>
                            </div>
                            <i class="fa-solid fa-building-columns"></i>
                        </label>

                        <label class="payment-option {{ $selectedPayment === 'jazzcash_easypaisa' ? 'is-selected' : '' }}">
                            <div class="payment-option__left">
                                <input type="radio" name="payment_method" value="jazzcash_easypaisa" {{ $selectedPayment === 'jazzcash_easypaisa' ? 'checked' : '' }}>
                                <div>
                                    <strong>JazzCash / EasyPaisa</strong>
                                    <small>Mobile wallet transfer.</small>
                                </div>
                            </div>
                            <i class="fa-solid fa-mobile-screen"></i>
                        </label>

                        @error('payment_method') <div class="error">{{ $message }}</div> @enderror

                        <div class="form-field" data-transaction-reference style="display:none;margin-top:16px;">
                            <label for="transaction_reference">Transaction Reference / Screenshot ID</label>
                            <input type="text" id="transaction_reference" name="transaction_reference" value="{{ old('transaction_reference') }}" placeholder="Enter your transfer reference">
                            @error('transaction_reference') <div class="error">{{ $message }}</div> @enderror
                        </div>
                    </div>
                </div>

                <div>
                    {{-- ---------- Order summary ---------- --}}
                    <div class="checkout-card">
                        <h3 style="margin-bottom:20px;">Order Summary</h3>

                        @foreach ($items as $item)
                            <div class="order-summary-item">
                                <div class="order-summary-item__image">
                                    @php($url = $item['image'] && file_exists(public_path('images/products/'.$item['image'])) ? asset('images/products/'.$item['image']) : null)
                                    @if ($url)
                                        <img src="{{ $url }}" alt="{{ $item['name'] }}">
                                    @else
                                        <div class="img-placeholder" style="font-size:.55rem;">No image</div>
                                    @endif
                                </div>
                                <div style="flex:1;">
                                    <div class="order-summary-item__name">{{ $item['name'] }}</div>
                                    <div class="order-summary-item__meta">
                                        @if ($item['size']) Size: {{ $item['size'] }} @endif
                                        @if ($item['shape']) | Shape: {{ $item['shape'] }} @endif
                                    </div>
                                    <div class="order-summary-item__meta">Qty: {{ $item['qty'] }}</div>
                                </div>
                                <strong>PKR {{ number_format($item['price'] * $item['qty'], 0) }}</strong>
                            </div>
                        @endforeach

                        <div class="summary-row" style="border-top:1px solid rgba(43,29,29,.1);padding-top:14px;">
                            <span>Subtotal</span><span>PKR {{ number_format($subtotal, 0) }}</span>
                        </div>
                        <div class="summary-row">
                            <span>Shipping</span>
                            <span id="checkout-shipping-amount">{{ $shipping == 0 ? 'Free' : 'PKR '.number_format($shipping, 0) }}</span>
                        </div>
                        <div class="summary-row total">
                            <span>Total</span><span id="checkout-total-amount">PKR {{ number_format($total, 0) }}</span>
                        </div>

                        <button type="submit" class="btn btn-primary btn-block" style="margin-top:20px;">
                            <i class="fa-solid fa-lock"></i> Place Order Securely
                        </button>
                    </div>

                    <div class="trust-badges">
                        <div class="trust-badge">
                            <i class="fa-solid fa-shield-halved"></i>
                            <div>
                                <strong>Secure Checkout</strong>
                                <small>256-bit SSL encrypted connection.</small>
                            </div>
                        </div>
                        <div class="trust-badge">
                            <i class="fa-solid fa-award"></i>
                            <div>
                                <strong>Satisfaction Guarantee</strong>
                                <small>Careful quality check before dispatch.</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>

@endsection
