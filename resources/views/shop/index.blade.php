@extends('layouts.app')

@section('title', 'Shop the Collection — Alishe Nails')

@section('content')

    <div class="page-header">
        <h1>Shop the Collection</h1>
        <p>Discover our handcrafted press-on nails, designed for effortless elegance. Find your perfect shape, length, and finish.</p>
    </div>

    <div class="container">
        <div class="shop-layout">

            {{-- ---------- Filters sidebar ---------- --}}
            <aside class="filters">
                <div class="filters__head">
                    <span>Filters</span>
                    <a href="{{ route('shop.index') }}">Clear All</a>
                </div>

                <form method="GET" action="{{ route('shop.index') }}" id="filter-form">
                    <input type="hidden" name="q" value="{{ request('q') }}">
                    <input type="hidden" name="sort" value="{{ request('sort') }}">

                    <div class="filter-group">
                        <h6>Shape</h6>
                        @foreach (['Almond', 'Coffin', 'Square', 'Stiletto'] as $shape)
                            <label class="filter-option">
                                <input type="checkbox" name="shape[]" value="{{ $shape }}"
                                    onchange="this.form.requestSubmit()"
                                    {{ in_array($shape, request('shape', [])) ? 'checked' : '' }}>
                                {{ $shape }} ({{ $shapeCounts[$shape] ?? 0 }})
                            </label>
                        @endforeach
                    </div>

                    <div class="filter-group">
                        <h6>Length</h6>
                        @foreach (['Short', 'Medium', 'Long', 'Extra Long'] as $length)
                            <label class="filter-option">
                                <input type="checkbox" name="length[]" value="{{ $length }}"
                                    onchange="this.form.requestSubmit()"
                                    {{ in_array($length, request('length', [])) ? 'checked' : '' }}>
                                {{ $length }} ({{ $lengthCounts[$length] ?? 0 }})
                            </label>
                        @endforeach
                    </div>

                    <div class="filter-group">
                        <h6>Finish</h6>
                        @foreach (['Glossy', 'Matte', 'Glitter/Chrome'] as $finish)
                            <label class="filter-option">
                                <input type="checkbox" name="finish[]" value="{{ $finish }}"
                                    onchange="this.form.requestSubmit()"
                                    {{ in_array($finish, request('finish', [])) ? 'checked' : '' }}>
                                {{ $finish }} ({{ $finishCounts[$finish] ?? 0 }})
                            </label>
                        @endforeach
                    </div>
                </form>

                <div class="promo-box">
                    <strong>Nail Care Kit</strong>
                    <p style="margin:8px 0;">Get a free prep kit with orders over PKR 5,000.</p>
                    <a href="{{ route('contact.index') }}">Learn More &rarr;</a>
                </div>
            </aside>

            {{-- ---------- Product listing ---------- --}}
            <div>
                <div class="shop-toolbar">
                    <div class="shop-toolbar__count">
                        Showing {{ $products->firstItem() ?? 0 }}-{{ $products->lastItem() ?? 0 }} of {{ $products->total() }} products
                    </div>
                    <div class="shop-toolbar__actions">
                        <form method="GET" action="{{ route('shop.index') }}" style="display:flex;gap:12px;">
                            @foreach (['shape', 'length', 'finish'] as $key)
                                @foreach (request($key, []) as $value)
                                    <input type="hidden" name="{{ $key }}[]" value="{{ $value }}">
                                @endforeach
                            @endforeach
                            <input type="hidden" name="sort" value="{{ request('sort') }}">
                            <input class="input-search" type="text" name="q" value="{{ request('q') }}" placeholder="Search styles...">
                        </form>

                        <form method="GET" action="{{ route('shop.index') }}">
                            @foreach (['shape', 'length', 'finish'] as $key)
                                @foreach (request($key, []) as $value)
                                    <input type="hidden" name="{{ $key }}[]" value="{{ $value }}">
                                @endforeach
                            @endforeach
                            <input type="hidden" name="q" value="{{ request('q') }}">
                            <select class="select-sort" name="sort" onchange="this.form.requestSubmit()">
                                <option value="" {{ request('sort') == '' ? 'selected' : '' }}>Sort by: Newest</option>
                                <option value="price_low" {{ request('sort') == 'price_low' ? 'selected' : '' }}>Price: Low to High</option>
                                <option value="price_high" {{ request('sort') == 'price_high' ? 'selected' : '' }}>Price: High to Low</option>
                                <option value="best_selling" {{ request('sort') == 'best_selling' ? 'selected' : '' }}>Best Selling</option>
                            </select>
                        </form>
                    </div>
                </div>

                @if (request()->hasAny(['shape', 'length', 'finish']))
                    <div class="active-filters">
                        @foreach (['shape', 'length', 'finish'] as $key)
                            @foreach (request($key, []) as $value)
                                <span class="filter-chip">
                                    {{ ucfirst($key) }}: {{ $value }}
                                    <a href="{{ route('shop.index', array_merge(request()->except($key), [$key => array_diff(request($key, []), [$value])])) }}">
                                        <button type="button">&times;</button>
                                    </a>
                                </span>
                            @endforeach
                        @endforeach
                    </div>
                @endif

                <div class="product-grid">
                    @forelse ($products as $product)
                        <x-product-card :product="$product" />
                    @empty
                        <p>No products match your filters. Try clearing them.</p>
                    @endforelse
                </div>

                @if ($products->hasPages())
                    <nav class="pagination" aria-label="Product pages">
                        @if ($products->onFirstPage())
                            <span><i class="fa-solid fa-chevron-left"></i></span>
                        @else
                            <a href="{{ $products->previousPageUrl() }}"><i class="fa-solid fa-chevron-left"></i></a>
                        @endif

                        @foreach ($products->getUrlRange(1, $products->lastPage()) as $page => $url)
                            <a href="{{ $url }}" class="{{ $page == $products->currentPage() ? 'active' : '' }}">{{ $page }}</a>
                        @endforeach

                        @if ($products->hasMorePages())
                            <a href="{{ $products->nextPageUrl() }}"><i class="fa-solid fa-chevron-right"></i></a>
                        @else
                            <span><i class="fa-solid fa-chevron-right"></i></span>
                        @endif
                    </nav>
                @endif
            </div>
        </div>
    </div>

@endsection
