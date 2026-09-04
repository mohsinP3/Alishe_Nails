{{-- Reusable navbar — included once via <x-navbar /> in layouts/app.blade.php --}}
<div class="announcement-bar">
    Free delivery on orders over PKR 5,000 &middot; Handcrafted with love
</div>

<header class="navbar" data-navbar>
    <div class="container navbar__inner">
        <a href="{{ route('home') }}" class="navbar__brand">
            <span class="navbar__logo" role="img" aria-label="Alishe Nails logo"></span>
            Alishe Nails
        </a>

        <nav aria-label="Primary">
            <ul class="navbar__links">
                <li><a href="{{ route('home') }}" class="{{ request()->routeIs('home') ? 'is-active' : '' }}">Home</a></li>
                <li><a href="{{ route('shop.index') }}" class="{{ request()->routeIs('shop.*') || request()->routeIs('products.*') ? 'is-active' : '' }}">Shop</a></li>
                <li><a href="{{ route('about.index') }}" class="{{ request()->routeIs('about.*') ? 'is-active' : '' }}">About Us</a></li>
                <li><a href="{{ route('how-to-apply.index') }}" class="{{ request()->routeIs('how-to-apply.*') ? 'is-active' : '' }}">How to Apply</a></li>
                <li><a href="{{ route('contact.index') }}" class="{{ request()->routeIs('contact.*') ? 'is-active' : '' }}">Contact</a></li>
            </ul>
        </nav>

        <div class="navbar__actions">
            <button class="navbar__icon-btn" type="button" aria-label="Search">
                <i class="fa-solid fa-magnifying-glass"></i>
            </button>

            <a href="{{ route('cart.index') }}" class="navbar__icon-btn" aria-label="Cart">
                <i class="fa-solid fa-bag-shopping"></i>
                <span class="navbar__cart-count">{{ $cartCount ?? 0 }}</span>
            </a>

            <span class="navbar__avatar" role="img" aria-label="Account"></span>

            <button class="navbar__toggle" type="button" aria-label="Toggle menu" data-navbar-toggle>
                <i class="fa-solid fa-bars"></i>
            </button>
        </div>
    </div>

    <div class="container">
        <ul class="navbar__mobile-links">
            <li><a href="{{ route('home') }}">Home</a></li>
            <li><a href="{{ route('shop.index') }}">Shop</a></li>
            <li><a href="{{ route('about.index') }}">About Us</a></li>
            <li><a href="{{ route('how-to-apply.index') }}">How to Apply</a></li>
            <li><a href="{{ route('contact.index') }}">Contact</a></li>
        </ul>
    </div>
</header>
