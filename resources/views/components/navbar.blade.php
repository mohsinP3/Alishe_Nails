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
            <button class="navbar__icon-btn" type="button" data-search-toggle aria-label="Search">
                <i class="fa-solid fa-magnifying-glass"></i>
            </button>

            <a href="{{ route('cart.index') }}" class="navbar__icon-btn" aria-label="Cart">
                <i class="fa-solid fa-bag-shopping"></i>
                <span class="navbar__cart-count">{{ $cartCount ?? 0 }}</span>
            </a>

            @auth('web')
                <div class="navbar__account" style="position:relative;">
                    <button class="navbar__icon-btn" type="button" data-account-toggle aria-label="Account menu">
                        <span class="navbar__avatar" role="img" aria-label="{{ auth('web')->user()->name }}" style="display:inline-flex;align-items:center;justify-content:center;font-size:.75rem;color:var(--rose-dark);font-weight:700;">
                            {{ strtoupper(substr(auth('web')->user()->name, 0, 1)) }}
                        </span>
                    </button>
                    <div class="navbar__account-menu" data-account-menu style="display:none;position:absolute;right:0;top:38px;background:#fff;border-radius:8px;box-shadow:0 8px 24px rgba(0,0,0,.12);min-width:170px;padding:8px;z-index:60;">
                        <a href="{{ route('account.profile') }}" style="display:block;padding:8px 10px;font-size:.85rem;border-radius:6px;">My Account</a>
                        <a href="{{ route('account.orders') }}" style="display:block;padding:8px 10px;font-size:.85rem;border-radius:6px;">My Orders</a>
                        <form action="{{ route('logout') }}" method="POST">
                            @csrf
                            <button type="submit" style="display:block;width:100%;text-align:left;padding:8px 10px;font-size:.85rem;border-radius:6px;background:none;border:none;cursor:pointer;font-family:inherit;color:#B3261E;">Logout</button>
                        </form>
                    </div>
                </div>
            @else
                <a href="{{ route('login') }}" class="navbar__icon-btn" aria-label="Login" title="Login / Register">
                    <i class="fa-solid fa-user" style="font-size:1.1rem;color:var(--espresso);"></i>
                </a>
            @endauth

            <button class="navbar__toggle" type="button" aria-label="Toggle menu" data-navbar-toggle>
                <i class="fa-solid fa-bars"></i>
            </button>
        </div>
    </div>

    {{-- Search Overlay Bar --}}
    <div class="container" data-search-form style="display:none;padding-block:8px 12px;">
        <form action="{{ route('shop.index') }}" method="GET" style="display:flex;align-items:center;gap:8px;width:100%;background:#fff;border:1px solid var(--rose);border-radius:8px;padding:6px 12px;box-shadow:0 4px 12px rgba(0,0,0,.08);">
            <i class="fa-solid fa-magnifying-glass" style="color:var(--rose-dark);"></i>
            <input type="text" name="q" value="{{ request('q') }}" placeholder="Search press-on nails (e.g. Ombre, Mauve, Coffin)..." style="flex:1;border:none;outline:none;background:transparent;font-size:.9rem;font-family:inherit;" required>
            <button type="submit" class="btn btn-primary btn-sm" style="padding:6px 14px;">Search</button>
            <button type="button" data-search-close style="background:none;border:none;font-size:1.1rem;cursor:pointer;color:#8a8a8a;padding:4px;" aria-label="Close search">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </form>
    </div>

    <div class="container">
        <ul class="navbar__mobile-links">
            <li><a href="{{ route('home') }}">Home</a></li>
            <li><a href="{{ route('shop.index') }}">Shop</a></li>
            <li><a href="{{ route('about.index') }}">About Us</a></li>
            <li><a href="{{ route('how-to-apply.index') }}">How to Apply</a></li>
            <li><a href="{{ route('contact.index') }}">Contact</a></li>
            @auth('web')
                <li><a href="{{ route('account.profile') }}">My Account</a></li>
                <li><a href="{{ route('account.orders') }}">My Orders</a></li>
                <li>
                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button type="submit" style="background:none;border:none;padding:10px 4px;font-family:inherit;font-size:1rem;cursor:pointer;color:#B3261E;">Logout</button>
                    </form>
                </li>
            @else
                <li><a href="{{ route('login') }}">Login</a></li>
                <li><a href="{{ route('register') }}">Create Account</a></li>
            @endauth
        </ul>
    </div>
</header>
