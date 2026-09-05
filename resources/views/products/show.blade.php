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

                @if ($product->isOutOfStock())
                    <p style="color:#b3261e;font-weight:600;font-size:.9rem;"><i class="fa-solid fa-circle-exclamation"></i> Out of Stock</p>
                @elseif ($product->isLowStock())
                    <p style="color:#b3261e;font-weight:600;font-size:.9rem;"><i class="fa-solid fa-triangle-exclamation"></i> Only {{ $product->stock }} left in stock</p>
                @endif

                @if ($product->description)
                    <p>{{ $product->description }}</p>
                @endif

                @if ($product->isOutOfStock())
                    <button type="button" class="btn btn-outline btn-block" disabled style="opacity:.5;cursor:not-allowed;">Out of Stock</button>
                @else
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
                            <input type="number" name="qty" value="1" min="1" max="{{ $product->stock }}" aria-label="Quantity">
                            <button type="button" data-action="increase" aria-label="Increase quantity">&plus;</button>
                        </div>

                        <button type="submit" class="btn btn-outline" style="flex:1;">
                            <i class="fa-solid fa-cart-shopping"></i> Add to Cart
                        </button>
                    </div>

                    <div style="margin-top:10px;">
                        <button type="submit" name="buy_now" value="1" class="btn btn-primary btn-block">
                            <i class="fa-solid fa-bolt"></i> Buy Now
                        </button>
                    </div>
                </form>
                @endif

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
        <section style="padding-block:40px 64px;max-width:760px;">
            <h3>Customer Reviews</h3>

            @forelse ($product->reviews as $review)
                <div style="border-top:1px solid var(--border-soft);padding:16px 0;">
                    <strong>{{ $review->customer_name }}</strong>
                    @if ($review->is_verified_purchase)
                        <span class="status-pill status-completed" style="margin-left:8px;">Verified Purchase</span>
                    @endif
                    <div style="color:var(--gold);font-size:.8rem;margin:4px 0;">
                        @for ($i = 1; $i <= 5; $i++)
                            <i class="fa-{{ $i <= $review->rating ? 'solid' : 'regular' }} fa-star"></i>
                        @endfor
                    </div>
                    <p style="margin:0;">{{ $review->comment }}</p>
                </div>
            @empty
                <p style="color:rgba(43,29,29,.6);">No reviews yet — be the first to share your experience.</p>
            @endforelse

            @auth('web')
                @if (! $userHasReviewed)
                    <div style="border-top:1px solid var(--border-soft);padding-top:20px;margin-top:8px;">
                        <h4 style="margin-bottom:12px;">Write a Review</h4>
                        <form action="{{ route('reviews.store', $product) }}" method="POST">
                            @csrf
                            <div class="form-field" style="margin-bottom:14px;max-width:200px;">
                                <label for="rating">Rating</label>
                                <select id="rating" name="rating" required>
                                    <option value="">Select...</option>
                                    @for ($i = 5; $i >= 1; $i--)
                                        <option value="{{ $i }}">{{ $i }} star{{ $i > 1 ? 's' : '' }}</option>
                                    @endfor
                                </select>
                                @error('rating') <div class="error">{{ $message }}</div> @enderror
                            </div>
                            <div class="form-field" style="margin-bottom:14px;">
                                <label for="comment">Your Review</label>
                                <textarea id="comment" name="comment" rows="4" required minlength="10" maxlength="1000"></textarea>
                                @error('comment') <div class="error">{{ $message }}</div> @enderror
                            </div>
                            <button type="submit" class="btn btn-primary">Submit Review</button>
                        </form>
                    </div>
                @else
                    <p style="font-size:.85rem;color:rgba(43,29,29,.6);margin-top:16px;">You've already reviewed this product.</p>
                @endif
            @else
                <p style="font-size:.85rem;margin-top:16px;">
                    <a href="{{ route('login') }}" style="text-decoration:underline;">Log in</a> to write a review.
                </p>
            @endauth
        </section>

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
