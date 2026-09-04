@extends('layouts.app')

@section('title', $product->name.' — Alishe Nails')

@section('content')

    <div class="container">
        <div class="breadcrumb">
            <a href="{{ route('home') }}">Home</a> &nbsp;&gt;&nbsp;
            <a href="{{ route('shop.index') }}">Shop All</a> &nbsp;&gt;&nbsp;
            <span>{{ strtoupper($product->name) }}</span>
        </div>

        <div class="product-detail">
            {{-- ---------- Gallery ---------- --}}
            <div class="product-gallery">
                <div class="product-gallery__thumbs">
                    @forelse ($product->gallery_urls as $i => $url)
                        <div class="product-gallery__thumb {{ $i === 0 ? 'is-active' : '' }}" data-gallery-thumb data-full-image="{{ $url }}">
                            <img src="{{ $url }}" alt="{{ $product->name }} thumbnail {{ $i + 1 }}">
                        </div>
                    @empty
                        <div class="product-gallery__thumb is-active">
                            <div class="img-placeholder">No image</div>
                        </div>
                    @endforelse
                </div>

                <div class="product-gallery__main" data-gallery-main>
                    @if ($product->image_url)
                        <img src="{{ $product->image_url }}" alt="{{ $product->name }}">
                    @else
                        <div class="img-placeholder">
                            Image missing:<br>images/products/{{ $product->image ?? $product->slug.'.jpg' }}
                        </div>
                    @endif
                </div>
            </div>

            {{-- ---------- Info ---------- --}}
            <div class="product-info">
                @if ($product->badge)
                    <span class="product-card__badge" style="position:static;display:inline-block;margin-bottom:12px;">{{ $product->badge }}</span>
                @endif

                <div class="product-info__rating">
                    @for ($i = 1; $i <= 5; $i++)
                        <i class="fa-{{ $i <= round($product->average_rating) ? 'solid' : 'regular' }} fa-star"></i>
                    @endfor
                    <span style="color:rgba(43,29,29,.6);">({{ $product->reviews_count }} Reviews)</span>
                </div>

                <h1>{{ $product->name }}</h1>
                <div class="product-info__price">PKR {{ number_format($product->price, 0) }}</div>

                @if ($product->description)
                    <p>{{ $product->description }}</p>
                @endif

                <form action="{{ route('cart.add', $product) }}" method="POST" id="add-to-cart-form">
                    @csrf

                    @if ($product->shape)
                        <div class="option-group" data-option-group>
                            <div class="option-group__head"><h6>Shape &amp; Length</h6></div>
                            <div class="option-pills">
                                <div class="option-pill is-selected" data-value="{{ $product->shape }} {{ $product->length }}">{{ $product->length }} {{ $product->shape }}</div>
                                <div class="option-pill" data-value="Short Square">Short Square</div>
                                <div class="option-pill" data-value="Long Coffin">Long Coffin</div>
                            </div>
                            <input type="hidden" name="shape" value="{{ $product->shape }} {{ $product->length }}">
                        </div>
                    @endif

                    <div class="option-group" data-option-group>
                        <div class="option-group__head">
                            <h6>Size</h6>
                            <a href="#">Sizing Guide</a>
                        </div>
                        <div class="option-pills">
                            @foreach (['XS', 'S', 'M', 'L', 'Custom'] as $i => $size)
                                <div class="option-pill {{ $size === 'S' ? 'is-selected' : '' }}" data-value="{{ $size }}">{{ $size }}</div>
                            @endforeach
                        </div>
                        <input type="hidden" name="size" value="S">
                    </div>

                    <div class="add-to-cart-row">
                        <div class="qty-selector" data-qty-selector>
                            <button type="button" data-action="decrease" aria-label="Decrease quantity">&minus;</button>
                            <input type="number" name="qty" value="1" min="1" max="20" aria-label="Quantity">
                            <button type="button" data-action="increase" aria-label="Increase quantity">&plus;</button>
                        </div>

                        <button type="submit" class="btn btn-outline" style="flex:1;">
                            <i class="fa-solid fa-cart-shopping"></i> Add to Cart
                        </button>
                    </div>
                </form>

                <form action="{{ route('cart.add', $product) }}" method="POST">
                    @csrf
                    <input type="hidden" name="qty" value="1">
                    <input type="hidden" name="shape" value="{{ $product->shape }} {{ $product->length }}">
                    <input type="hidden" name="size" value="S">
                    <button type="submit" class="btn btn-primary btn-block">Buy Now</button>
                </form>

                @if (!empty($product->whats_included))
                    <details class="accordion-item" open>
                        <summary>What's Included <i class="fa-solid fa-chevron-down chevron"></i></summary>
                        <ul>
                            @foreach ($product->whats_included as $item)
                                <li><i class="fa-solid fa-check"></i> {{ $item }}</li>
                            @endforeach
                        </ul>
                    </details>
                @endif

                <details class="accordion-item">
                    <summary>How to Apply <i class="fa-solid fa-chevron-down chevron"></i></summary>
                    <ul>
                        <li><i class="fa-solid fa-check"></i> Select the correct nail size for each finger.</li>
                        <li><i class="fa-solid fa-check"></i> Prep natural nails and apply the included adhesive.</li>
                        <li><i class="fa-solid fa-check"></i> Press and hold for 20–30 seconds per nail.</li>
                    </ul>
                    <p style="font-size:.85rem;margin-top:10px;">
                        <a href="{{ route('how-to-apply.index') }}" style="text-decoration:underline;">See the full guide &rarr;</a>
                    </p>
                </details>

                <details class="accordion-item">
                    <summary>Shipping &amp; Returns <i class="fa-solid fa-chevron-down chevron"></i></summary>
                    <ul>
                        <li><i class="fa-solid fa-check"></i> Free delivery on orders over PKR 5,000.</li>
                        <li><i class="fa-solid fa-check"></i> Dispatched within 1–3 business days.</li>
                        <li><i class="fa-solid fa-check"></i> Unworn sets may be exchanged within 7 days.</li>
                    </ul>
                </details>
            </div>
        </div>

        {{-- ---------- Reviews ---------- --}}
        @if ($product->reviews->isNotEmpty())
            <section style="padding-block:40px 64px;max-width:760px;">
                <h3>Customer Reviews</h3>
                @foreach ($product->reviews as $review)
                    <div style="border-top:1px solid var(--border-soft);padding:16px 0;">
                        <strong>{{ $review->customer_name }}</strong>
                        <div style="color:var(--gold);font-size:.8rem;margin:4px 0;">
                            @for ($i = 1; $i <= 5; $i++)
                                <i class="fa-{{ $i <= $review->rating ? 'solid' : 'regular' }} fa-star"></i>
                            @endfor
                        </div>
                        <p style="margin:0;">{{ $review->comment }}</p>
                    </div>
                @endforeach
            </section>
        @endif

        {{-- ---------- Related products ---------- --}}
        @if ($related->isNotEmpty())
            <section style="padding-block:20px 64px;">
                <div class="section-heading">
                    <span class="eyebrow">You May Also Like</span>
                </div>
                <div class="product-grid">
                    @foreach ($related as $item)
                        <x-product-card :product="$item" />
                    @endforeach
                </div>
            </section>
        @endif
    </div>

@endsection
