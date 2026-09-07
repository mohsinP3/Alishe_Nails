@extends('layouts.app')

@section('title', 'Work With Us — Alishe Nails')
@section('meta_description', 'Paid campaign placements for creators on Alishe Nails — homepage features, product page placements and social shout-outs to a highly engaged Pakistani nail-care audience.')

@section('content')

    <div class="page-header">
        <h1>Your Content. Our Spotlight.</h1>
        <p>Alishe Nails now offers paid campaign placements for creators — your reel, your post, your brand moment, featured where our customers already are.</p>
    </div>

    <div class="container">
        <div class="contact-layout">
            {{-- ---------- Campaign pitch form ---------- --}}
            <div id="pitch-form">
                <h3 style="margin-bottom:12px;">Pitch Your Campaign</h3>
                <p style="margin-bottom:20px;font-size:.9rem;color:rgba(43,29,29,.7);">Tell us about your campaign — we'll get back with pricing and available slots.</p>

                <form action="{{ route('work-with-us.store') }}" method="POST">
                    {{-- Honeypot: hidden from real visitors via CSS; CampaignPitchRequest rejects any submission that fills this in. --}}
                    <input type="text" name="website" value="" tabindex="-1" autocomplete="off" style="position:absolute;left:-9999px;" aria-hidden="true">
                    @csrf

                    <div class="form-grid">
                        <div class="form-field">
                            <label for="name">Name / Brand</label>
                            <input type="text" id="name" name="name" value="{{ old('name') }}" required>
                            @error('name') <div class="error">{{ $message }}</div> @enderror
                        </div>

                        <div class="form-field">
                            <label for="handle">Instagram / TikTok Handle</label>
                            <input type="text" id="handle" name="handle" value="{{ old('handle') }}" placeholder="@yourhandle" required>
                            @error('handle') <div class="error">{{ $message }}</div> @enderror
                        </div>

                        <div class="form-field">
                            <label for="follower_count">Follower Count</label>
                            <input type="number" id="follower_count" name="follower_count" value="{{ old('follower_count') }}" min="0" step="1" required>
                            @error('follower_count') <div class="error">{{ $message }}</div> @enderror
                        </div>

                        <div class="form-field">
                            <label for="campaign_type">Campaign Type</label>
                            <select id="campaign_type" name="campaign_type" required>
                                <option value="">Select a campaign type…</option>
                                @foreach ($campaignTypes as $value => $label)
                                    <option value="{{ $value }}" {{ old('campaign_type') === $value ? 'selected' : '' }}>{{ $label }}</option>
                                @endforeach
                            </select>
                            @error('campaign_type') <div class="error">{{ $message }}</div> @enderror
                        </div>

                        <div class="form-field">
                            <label for="budget_range">Budget Range</label>
                            <select id="budget_range" name="budget_range" required>
                                <option value="">Select a budget range…</option>
                                @foreach ($budgetRanges as $value => $label)
                                    <option value="{{ $value }}" {{ old('budget_range') === $value ? 'selected' : '' }}>{{ $label }}</option>
                                @endforeach
                            </select>
                            @error('budget_range') <div class="error">{{ $message }}</div> @enderror
                        </div>
                        <div class="form-field full">
                            <label for="portfolio_links">Portfolio Links <span style="font-weight:400;color:rgba(43,29,29,.55);">(optional)</span></label>
                            <textarea id="portfolio_links" name="portfolio_links" rows="3" placeholder="Links to your best reels, posts, or media kit">{{ old('portfolio_links') }}</textarea>
                            @error('portfolio_links') <div class="error">{{ $message }}</div> @enderror
                        </div>

                        <div class="form-field full">
                            <label for="message">Tell Us About Your Campaign</label>
                            <textarea id="message" name="message" rows="5" required>{{ old('message') }}</textarea>
                            @error('message') <div class="error">{{ $message }}</div> @enderror
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary" style="margin-top:16px;">
                        <i class="fa-solid fa-paper-plane"></i> Pitch Your Campaign
                    </button>
                </form>
            </div>

            <div>
                <div class="contact-info-card">
                    <h5>We're Not a Marketplace — We're a Stage</h5>
                    <p style="margin:8px 0 12px;font-size:.9rem;">If you create beauty, nail-art, or lifestyle content and want direct visibility to a highly engaged Pakistani nail-care audience, Alishe Nails offers paid campaign spots on our homepage, product pages, and social shout-outs. Tell us about your campaign — we'll get back with pricing and available slots.</p>
                    <a href="#pitch-form" class="btn btn-primary btn-sm"><i class="fa-solid fa-bullhorn"></i> Pitch Your Campaign</a>
                </div>

                <div class="contact-info-card">
                    <h5>Homepage Feature</h5>
                    <p style="margin:8px 0 0;font-size:.88rem;color:rgba(43,29,29,.7);">Your campaign front and centre on the page our customers land on first.</p>
                </div>

                <div class="contact-info-card">
                    <h5>Product Page Placement</h5>
                    <p style="margin:8px 0 0;font-size:.88rem;color:rgba(43,29,29,.7);">Featured beside the sets shoppers are already deciding to buy.</p>
                </div>

                <div class="contact-info-card">
                    <h5>Social Shout-out</h5>
                    <p style="margin:8px 0 0;font-size:.88rem;color:rgba(43,29,29,.7);">A dedicated spotlight to our Instagram community, driving straight to your profile.</p>
                </div>

                <div class="contact-info-card">
                    <h5>Bundle</h5>
                    <p style="margin:8px 0 0;font-size:.88rem;color:rgba(43,29,29,.7);">Combine placements into one campaign for sustained visibility across the store.</p>
                </div>
            </div>
        </div>
    </div>

@endsection