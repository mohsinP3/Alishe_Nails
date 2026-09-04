@props(['product'])

<div class="product-card">
    <a href="{{ route('products.show', $product) }}" class="product-card__image" style="display:block;">
        @if ($product->badge)
            <span class="product-card__badge">{{ $product->badge }}</span>
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
        <div class="product-card__meta">{{ $product->shape }} &middot; {{ $product->length }}</div>
        <div class="product-card__price">PKR {{ number_format($product->price, 0) }}</div>
    </div>
</div>
