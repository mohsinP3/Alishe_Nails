<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/jpeg" href="{{ asset('images/logo.jpeg') }}">
    <title>@yield('title', 'Alishe Nails — Timeless Nails, Made for You')</title>
    <meta name="description" content="@yield('meta_description', 'Luxury handmade press-on nails for every occasion. Shop the Alishe Nails collection.')">
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

    <script src="{{ asset('js/app.js') }}"></script>
    @stack('scripts')
</body>
</html>
