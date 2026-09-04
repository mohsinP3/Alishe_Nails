@extends('layouts.app')

@section('title', 'Our Story — Alishe Nails')

@section('content')

    <div class="page-header">
        <h1>Our Story</h1>
        <p>Handmade with intention, designed for the way you actually live and dress up.</p>
    </div>

    <div class="container">
        <div class="about-story">
            <div>
                <span class="eyebrow" style="display:block;color:var(--rose);font-size:.8rem;letter-spacing:.12em;text-transform:uppercase;margin-bottom:10px;">Handmade Craftsmanship</span>
                <h2>Every set, hand-painted with care</h2>
                <p>Alishe Nails began as a love letter to the ritual of getting ready — the ten minutes before you walk out the door feeling like yourself. Every set is hand-painted, hand-shaped, and quality-checked before it reaches you, so a salon-level finish is never more than a few minutes away.</p>
                <p>We work in small batches out of Karachi, sourcing durable, reusable materials so a single set can be worn again and again with proper care.</p>
                <a href="{{ route('shop.index') }}" class="btn btn-primary">Shop the Collection</a>
            </div>
            <div class="about-story__image">
                @if (file_exists(public_path('images/brand/about-hands.jpg')))
                    <img src="{{ asset('images/brand/about-hands.jpg') }}" alt="Alishe Nails handmade press-on nails">
                @else
                    <div class="img-placeholder">Image missing: images/brand/about-hands.jpg</div>
                @endif
            </div>
        </div>
    </div>

    <section class="about-values container">
        <div class="value-card">
            <i class="fa-regular fa-heart"></i>
            <h4>Handmade with Love</h4>
            <p style="font-size:.88rem;color:rgba(43,29,29,.7);">Every nail is individually hand-painted — no two sets are ever quite the same.</p>
        </div>
        <div class="value-card">
            <i class="fa-solid fa-gem"></i>
            <h4>Premium Quality</h4>
            <p style="font-size:.88rem;color:rgba(43,29,29,.7);">Salon-grade materials chosen for comfort, durability, and a natural finish.</p>
        </div>
        <div class="value-card">
            <i class="fa-solid fa-recycle"></i>
            <h4>Reusable &amp; Durable</h4>
            <p style="font-size:.88rem;color:rgba(43,29,29,.7);">Cared for properly, each set can be worn multiple times.</p>
        </div>
    </section>

@endsection
