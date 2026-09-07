@extends('layouts.app')
@section('title', $product->name.' — Alishe Nails')
@section('og_type', 'product')
@section('og_title', $product->name.' — Alishe Nails')
@section('og_description', $product->short_description ?: 'Shop '.$product->name.' from Alishe Nails.')
@section('og_image', $product->cover_image_url ?: asset('images/logo.jpeg'))

@section('content')

    <div class="container">
        <div class="breadcrumb">
            <a href="{{ route('home') }}">Home</a> &nbsp;&gt;&nbsp;
            <a href="{{ route('shop.index') }}">Shop All</a> &nbsp;&gt;&nbsp;
            <span>{{ strtoupper($product->name) }}</span>
        </div>

        <div class="product-detail">
            {{-- ---------- Media gallery (images + videos, ordered) ---------- --}}
            <div class="product-gallery" data-media-gallery>
                @php
                    $mediaUrls = $product->media_urls;
                @endphp

                <div class="product-gallery__main" data-media-stage tabindex="0" role="button" aria-label="Zoom product media">
                    @forelse ($mediaUrls as $i => $media)
                        <div class="product-gallery__slide {{ $i === 0 ? 'is-active' : '' }}" data-media-slide data-index="{{ $i }}">
                            @if ($media['type'] === 'video')
                                <video src="{{ $media['url'] }}" controls muted loop playsinline preload="metadata"></video>
                            @else
                                <img src="{{ $media['url'] }}" alt="{{ $product->name }} media {{ $i + 1 }}" data-image-fallback>
                            @endif
                        </div>
                    @empty
                        <div class="img-placeholder">
                            Alishe Nails<br>Image unavailable
                        </div>
                    @endforelse
                </div>

                {{-- One thumb per ACTUAL media item — never an empty slot --}}
                @if (count($mediaUrls) > 1)
                    <div class="product-gallery__thumbs">
                        @foreach ($mediaUrls as $i => $media)
                            <button type="button" class="product-gallery__thumb {{ $i === 0 ? 'is-active' : '' }}"
                                    data-media-thumb data-index="{{ $i }}" aria-label="View media {{ $i + 1 }}">
                                @if ($media['type'] === 'video')
                                    <video src="{{ $media['url'] }}" muted preload="metadata"></video>
                                    <span class="product-gallery__thumb-play"><i class="fa-solid fa-play"></i></span>
                                @else
                                    <img src="{{ $media['url'] }}" alt="{{ $product->name }} thumbnail {{ $i + 1 }}">
                                @endif
                            </button>
                        @endforeach
                    </div>
                @endif
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
                @if ($product->seller)
                    <p class="seller-byline"><i class="fa-solid fa-sparkles"></i> Sold by {{ $product->seller->name }}@if($product->seller->instagram_handle) <span>{{ '@'.$product->seller->instagram_handle }}</span>@endif</p>
                @endif
                @php
                    $saved = auth('web')->check() && auth('web')->user()->wishlist()->where('products.id', $product->id)->exists();
                @endphp

                <div class="product-info__price">PKR {{ number_format($product->price, 0) }}</div>

                @auth('web')
                    <form action="{{ route('account.wishlist.toggle', $product) }}" method="POST" style="margin-bottom:16px;">
                        @csrf
                        <button type="submit" class="btn btn-outline btn-sm" style="display:inline-flex;align-items:center;gap:8px;">
                            <i class="{{ $saved ? 'fa-solid fa-heart' : 'fa-regular fa-heart' }}"></i>
                            {{ $saved ? 'Saved to Wishlist' : 'Save to Wishlist' }}
                        </button>
                    </form>
                @else
                    <a href="{{ route('login') }}" class="btn btn-outline btn-sm" style="display:inline-flex;align-items:center;gap:8px;margin-bottom:16px;">
                        <i class="fa-regular fa-heart"></i> Save to Wishlist
                    </a>
                @endauth

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
                            <button type="button" class="text-button" data-modal-open="size-guide-modal">Sizing Guide</button>
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
                        <li><i class="fa-solid fa-check"></i> Orders are not returnable, exchangeable, or refundable once placed.</li>
                        <li><a href="{{ route('policies.index') }}" style="text-decoration:underline;">Read our full Returns &amp; Privacy Policy</a></li>
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

    <div class="modal" id="size-guide-modal" data-modal aria-hidden="true" role="dialog" aria-modal="true" aria-labelledby="size-guide-title">
        <div class="modal__backdrop" data-modal-close></div>
        <div class="modal__panel">
            <button type="button" class="modal__close" data-modal-close aria-label="Close size guide"><i class="fa-solid fa-xmark"></i></button>
            <span class="eyebrow">Find your fit</span>
            <h2 id="size-guide-title">Press-on Nail Size Guide</h2>
            <p>Measure across the widest part of each natural nail in millimetres, then choose the closest size below.</p>
            <div class="size-guide-table" role="table" aria-label="Nail size chart">
                <div><strong>Size</strong><strong>Width</strong><strong>Best for</strong></div>
                <div><span>XS</span><span>10-12 mm</span><span>Petite nail beds</span></div>
                <div><span>S</span><span>12-14 mm</span><span>Small nail beds</span></div>
                <div><span>M</span><span>14-16 mm</span><span>Most nail beds</span></div>
                <div><span>L</span><span>16-18 mm</span><span>Wide nail beds</span></div>
            </div>
            <div class="size-guide-note"><i class="fa-solid fa-ruler"></i> No ruler? Place clear tape over the nail, mark both edges, then measure the tape against a ruler.</div>
        </div>
    </div>

@endsection
