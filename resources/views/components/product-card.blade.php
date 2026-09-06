@props(['product'])
<div class="product-card">
    <a href="{{ route('products.show', $product) }}" class="product-card__image" style="display:block;">
        @if ($product->isOutOfStock())
            <span class="product-card__badge" style="background:#8a8a8a;">Out of Stock</span>
        @elseif ($product->badge)
            <span class="product-card__badge">{{ $product->badge }}</span>
        @elseif ($product->isLowStock())
            <span class="product-card__badge" style="background:#b3261e;">Only {{ $product->stock }} left</span>
        @endif

        <button type="button" class="product-card__wishlist" aria-label="Add to wishlist" onclick="event.preventDefault()">
            <i class="fa-regular fa-heart"></i>
        </button>

        @if ($product->image_url)
            <img src="{{ $product->image_url }}" alt="{{ $product->name }}" loading="lazy">
        @else
            <div class="img-placeholder">
                Image missing:<br>images/products/{{ $product->image ?? $product->slug.'.jpg' }}
            </div>
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
