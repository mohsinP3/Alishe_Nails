<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/jpeg" href="{{ asset('images/logo.jpeg') }}">
    <title>@yield('title', 'Alishe Nails — Timeless Nails, Made for You')</title>
    <meta name="description" content="@yield('meta_description', 'Luxury handmade press-on nails for every occasion. Shop the Alishe Nails collection.')">
    <meta property="og:type" content="@yield('og_type', 'website')">
    <meta property="og:site_name" content="Alishe Nails">
    <meta property="og:title" content="@yield('og_title', 'Alishe Nails — Timeless Nails, Made for You')">
    <meta property="og:description" content="@yield('og_description', 'Luxury handmade press-on nails for every occasion.')">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:image" content="@yield('og_image', asset('images/logo.jpeg'))">
    <meta name="twitter:card" content="summary_large_image">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@500;600;700&family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    @stack('styles')
</head>
<body>
    <x-alert />

    <x-navbar />

    <main>
        @yield('content')
    </main>

    <x-footer />

    <a href="https://wa.me/{{ str_replace(['+', ' '], '', config('services.whatsapp.number')) }}?text={{ rawurlencode('Hi Alishe Nails, I have a question about your press-on nails.') }}"
       class="floating-whatsapp" target="_blank" rel="noopener" aria-label="Chat with Alishe Nails on WhatsApp" title="Chat on WhatsApp">
        <i class="fa-brands fa-whatsapp"></i>
    </a>

    <script src="{{ asset('js/app.js') }}"></script>
    @stack('scripts')
</body>
</html>
