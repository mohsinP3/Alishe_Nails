<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin — Alishe Nails')</title>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@500;600;700&family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <link rel="stylesheet" href="{{ asset('css/admin.css') }}">
</head>
<body class="admin-body">

    @if (session('is_admin'))
        <div class="admin-shell">
            {{-- ---------- Sidebar ---------- --}}
            <aside class="admin-sidebar">
                <a href="{{ route('admin.dashboard') }}" class="admin-sidebar__brand">
                    <span class="navbar__logo" role="img" aria-label="Alishe Nails logo"></span>
                    Alishe Nails
                </a>

                <div class="admin-sidebar__label">Main Menu</div>
                <nav>
                    <a href="{{ route('admin.dashboard') }}" class="admin-nav-link {{ request()->routeIs('admin.dashboard') ? 'is-active' : '' }}">
                        <i class="fa-solid fa-chart-pie"></i> Overview
                    </a>
                    <a href="{{ route('admin.orders.index') }}" class="admin-nav-link {{ request()->routeIs('admin.orders.*') ? 'is-active' : '' }}">
                        <i class="fa-solid fa-receipt"></i> Orders
                    </a>
                    <a href="{{ route('admin.products.index') }}" class="admin-nav-link {{ request()->routeIs('admin.products.*') ? 'is-active' : '' }}">
                        <i class="fa-solid fa-box"></i> Products
                    </a>
                    <a href="{{ route('admin.customers.index') }}" class="admin-nav-link {{ request()->routeIs('admin.customers.*') ? 'is-active' : '' }}">
                        <i class="fa-solid fa-users"></i> Customers
                    </a>
                    <a href="{{ route('admin.analytics.index') }}" class="admin-nav-link {{ request()->routeIs('admin.analytics.*') ? 'is-active' : '' }}">
                        <i class="fa-solid fa-chart-line"></i> Analytics
                    </a>
                </nav>

                <div class="admin-sidebar__bottom">
                    <a href="{{ route('admin.settings.index') }}" class="admin-nav-link {{ request()->routeIs('admin.settings.*') ? 'is-active' : '' }}">
                        <i class="fa-solid fa-gear"></i> Settings
                    </a>
                    <form action="{{ route('admin.logout') }}" method="POST">
                        @csrf
                        <button type="submit" class="admin-nav-link" style="width:100%;text-align:left;background:none;border:none;cursor:pointer;font-family:inherit;">
                            <i class="fa-solid fa-right-from-bracket"></i> Logout
                        </button>
                    </form>
                </div>
            </aside>

            {{-- ---------- Main content ---------- --}}
            <div class="admin-main">
                <header class="admin-topbar">
                    <div>
                        <button class="admin-topbar__menu-toggle" type="button" data-sidebar-toggle aria-label="Toggle menu">
                            <i class="fa-solid fa-bars"></i>
                        </button>
                    </div>
                    <div class="admin-topbar__actions">
                        <a href="{{ route('home') }}" target="_blank" class="btn btn-outline btn-sm">
                            <i class="fa-solid fa-arrow-up-right-from-square"></i> View Store
                        </a>
                        <span class="navbar__avatar" role="img" aria-label="Admin"></span>
                    </div>
                </header>

                <main class="admin-content">
                    <x-alert />
                    @yield('content')
                </main>
            </div>
        </div>
    @else
        {{-- Not logged in (e.g. the login page itself) — no sidebar, just the page content --}}
        <x-alert />
        @yield('content')
    @endif

    <script src="{{ asset('js/app.js') }}"></script>
    <script src="{{ asset('js/admin.js') }}"></script>
    @stack('scripts')
</body>
</html>
