@extends('layouts.app')

@section('title', 'Contact — Alishe Nails')

@section('content')

    <div class="page-header">
        <h1>Get in Touch</h1>
        <p>Questions about sizing, an order, or a custom set? We'd love to hear from you.</p>
    </div>

    <div class="container">
        <div class="contact-layout">
            {{-- ---------- Contact form ---------- --}}
            <div>
                <h3 style="margin-bottom:20px;">Send us a message</h3>

                <form action="{{ route('contact.store') }}" method="POST">
                    {{-- Honeypot: hidden from real visitors via CSS; ContactRequest rejects any submission that fills this in. --}}
                    <input type="text" name="website" value="" tabindex="-1" autocomplete="off" style="position:absolute;left:-9999px;" aria-hidden="true">
                    @csrf

                    <div class="form-grid">
                        <div class="form-field">
                            <label for="name">Name</label>
                            <input type="text" id="name" name="name" value="{{ old('name') }}" required>
                            @error('name') <div class="error">{{ $message }}</div> @enderror
                        </div>

                        <div class="form-field">
                            <label for="phone">Phone</label>
                            <input type="text" id="phone" name="phone" value="{{ old('phone') }}">
                            @error('phone') <div class="error">{{ $message }}</div> @enderror
                        </div>

                        <div class="form-field full">
                            <label for="email">Email</label>
                            <input type="email" id="email" name="email" value="{{ old('email') }}" required>
                            @error('email') <div class="error">{{ $message }}</div> @enderror
                        </div>

                        <div class="form-field full">
                            <label for="message">Message</label>
                            <textarea id="message" name="message" rows="6" required>{{ old('message') }}</textarea>
                            @error('message') <div class="error">{{ $message }}</div> @enderror
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary" style="margin-top:16px;">
                        <i class="fa-solid fa-paper-plane"></i> Send Message
                    </button>
                </form>
            </div>

            {{-- ---------- Contact info ---------- --}}
            <div>
                <div class="contact-info-card">
                    <h5>WhatsApp</h5>
                    <p style="margin:8px 0 12px;">Chat with us directly for quick questions or sizing help.</p>
                    <a href="https://wa.me/{{ str_replace(['+', ' '], '', config('services.whatsapp.number')) }}"
                       class="btn btn-primary btn-sm" target="_blank" rel="noopener">
                        <i class="fa-brands fa-whatsapp"></i> Message Us
                    </a>
                </div>

                <div class="contact-info-card">
                    <h5>Instagram</h5>
                    <p style="margin:8px 0 12px;">Follow along for new drops and behind-the-scenes.</p>
                    <a href="https://instagram.com/{{ config('services.instagram.handle') }}"
                       class="btn btn-outline btn-sm" target="_blank" rel="noopener">
                        <i class="fa-brands fa-instagram"></i> {{ '@'.config('services.instagram.handle') }}
                    </a>
                </div>

                <div class="contact-info-card">
                    <h5>Contact Information</h5>
                    <p style="margin:8px 0 4px;"><i class="fa-solid fa-envelope"></i> hello@alishenails.com</p>
                    <p style="margin:0 0 4px;"><i class="fa-solid fa-phone"></i> {{ config('services.whatsapp.number') }}</p>
                    <p style="margin:0;"><i class="fa-solid fa-location-dot"></i> Karachi, Pakistan</p>
                </div>
            </div>
        </div>
    </div>

@endsection
