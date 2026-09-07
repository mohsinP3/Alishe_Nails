@extends('layouts.app')
@section('title', 'Book a Custom Design — Alishe Nails')

@section('content')
    <div class="page-header">
        <h1>Book a Custom Design</h1>
        <p>Bring your idea, reference, or mood board. We will turn it into a one-of-a-kind set.</p>
    </div>

    <div class="container">
        <div class="custom-design-layout">
            <div>
                <span class="eyebrow">Made around you</span>
                <h2>Tell us what you are imagining.</h2>
                <p>Share your preferred shape, colours, occasion, and any details that matter. We will reply with availability and a quote.</p>
                <div class="custom-design-points">
                    <div><i class="fa-solid fa-wand-magic-sparkles"></i><span>Personalised nail art</span></div>
                    <div><i class="fa-solid fa-image"></i><span>Reference image welcome</span></div>
                    <div><i class="fa-solid fa-comments"></i><span>Quote before we begin</span></div>
                </div>
            </div>

            <form action="{{ route('custom-design.store') }}" method="POST" enctype="multipart/form-data" class="checkout-card">
                @csrf
                <input type="text" name="website" value="" tabindex="-1" autocomplete="off" style="position:absolute;left:-9999px;" aria-hidden="true">
                <div class="form-grid">
                    <div class="form-field"><label for="custom-name">Name</label><input id="custom-name" name="name" value="{{ old('name') }}" required>@error('name')<div class="error">{{ $message }}</div>@enderror</div>
                    <div class="form-field"><label for="custom-email">Email</label><input id="custom-email" type="email" name="email" value="{{ old('email') }}" required>@error('email')<div class="error">{{ $message }}</div>@enderror</div>
                    <div class="form-field full"><label for="custom-phone">WhatsApp / Phone</label><input id="custom-phone" name="phone" value="{{ old('phone') }}" required>@error('phone')<div class="error">{{ $message }}</div>@enderror</div>
                    <div class="form-field"><label for="custom-style">Preferred shape / style</label><input id="custom-style" name="style" placeholder="e.g. almond, chrome" value="{{ old('style') }}" required>@error('style')<div class="error">{{ $message }}</div>@enderror</div>
                    <div class="form-field"><label for="custom-budget">Budget <span>(optional)</span></label><input id="custom-budget" name="budget" placeholder="e.g. PKR 5,000" value="{{ old('budget') }}">@error('budget')<div class="error">{{ $message }}</div>@enderror</div>
                    <div class="form-field full"><label for="custom-details">Design details</label><textarea id="custom-details" name="details" rows="6" placeholder="Colours, occasion, deadline, and anything else we should know..." required>{{ old('details') }}</textarea>@error('details')<div class="error">{{ $message }}</div>@enderror</div>
                    <div class="form-field full"><label for="reference-image">Reference image <span>(optional, JPG/PNG/WebP up to 4MB)</span></label><input id="reference-image" type="file" name="reference_image" accept="image/jpeg,image/png,image/webp">@error('reference_image')<div class="error">{{ $message }}</div>@enderror</div>
                </div>
                <button type="submit" class="btn btn-primary"><i class="fa-solid fa-paper-plane"></i> Send Design Request</button>
            </form>
        </div>
    </div>
@endsection
