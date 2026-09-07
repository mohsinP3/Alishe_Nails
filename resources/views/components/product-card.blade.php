@props(['product'])
@php
    $saved = auth('web')->check() && auth('web')->user()->wishlist()->where('products.id', $product->id)->exists();
@endphp
<div class="product-card">
    <a href="{{ route('products.show', $product) }}" class="product-card__image" style="display:block;">
        @if ($product->isOutOfStock())
            <span class="product-card__badge" style="background:#8a8a8a;">Out of Stock</span>
        @elseif ($product->badge)
            <span class="product-card__badge">{{ $product->badge }}</span>
        @elseif ($product->isLowStock())
            <span class="product-card__badge" style="background:#b3261e;">Only {{ $product->stock }} left</span>
        @endif

        @auth('web')
            <form action="{{ route('account.wishlist.toggle', $product) }}" method="POST" class="product-card__wishlist-form">
                @csrf
                <button type="submit" class="product-card__wishlist" data-wishlist-id="{{ $product->id }}" aria-label="{{ $saved ? 'Remove' : 'Add' }} {{ $product->name }} from wishlist" aria-pressed="{{ $saved ? 'true' : 'false' }}">
                    <i class="{{ $saved ? 'fa-solid fa-heart' : 'fa-regular fa-heart' }}"></i>
                </button>
            </form>
        @else
            <a href="{{ route('login') }}" class="product-card__wishlist" data-wishlist-id="{{ $product->id }}" aria-label="Add {{ $product->name }} to wishlist" aria-pressed="false" title="Login to save this product">
                <i class="fa-regular fa-heart"></i>
            </a>
        @endauth

        @php($cover = $product->media_urls[0] ?? null)
        @if ($cover)
            @if ($cover['type'] === 'video')
                <video src="{{ $cover['url'] }}" muted loop playsinline controls preload="metadata"></video>
            @else
                <img src="{{ $cover['url'] }}" alt="{{ $product->name }}" loading="lazy" data-image-fallback>
            @endif
        @else
            <div class="img-placeholder">
                Alishe Nails<br>Image unavailable
            </div>
        @endif

        @if ($product->seller)
            <span class="seller-badge"><i class="fa-solid fa-sparkles"></i> Sold by {{ $product->seller->name }}</span>
        @endif
    </a>

    <div class="product-card__body">
        <div class="product-card__rating">
            @for ($i = 1; $i <= 5; $i++)
                <i class="fa-{{ $i <= round($product->average_rating) ? 'solid' : 'regular' }} fa-star"></i>
            @endfor
            <span>({{ $product->reviews_count }})</span>
        </div>

        <a href="{{ route('products.show', $product) }}">
            <div class="product-card__name">{{ $product->name }}</div>
        </a>
        <div class="product-card__price">PKR {{ number_format($product->price, 0) }}</div>

        <div style="display:flex;gap:8px;margin-top:12px;">
            <a href="{{ route('products.show', $product) }}" class="btn btn-outline btn-sm" style="flex:1;text-align:center;">View Details</a>
            @if ($product->isOutOfStock())
                <button type="button" class="btn btn-sm" disabled style="opacity:.6;cursor:not-allowed;background:var(--ivory);">Out of Stock</button>
            @else
                <form action="{{ route('cart.add', $product) }}" method="POST" style="display:inline;">
                    @csrf
                    <button type="submit" class="btn btn-primary btn-sm" title="Add to Cart">
                        <i class="fa-solid fa-cart-shopping"></i>
                    </button>
                </form>
            @endif
        </div>
    </div>
</div>
