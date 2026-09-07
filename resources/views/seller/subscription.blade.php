@extends('layouts.app')
@section('title', 'Seller Subscription — Alishe Nails')
@section('content')
<div class="page-header"><h1>Choose Your Subscription</h1><p>Keep your designs visible and continue selling on Alishe Nails.</p></div>
<div class="container" style="max-width:900px;padding-block:36px 80px;">
    @if($plans->isEmpty())<div class="checkout-card"><p>There are no active plans right now. Please check back soon.</p></div>@else
    <div class="subscription-plans">@foreach($plans as $plan)<div class="subscription-plan"><span class="eyebrow">{{ $plan->duration_days >= 365 ? 'Yearly' : 'Flexible plan' }}</span><h2>{{ $plan->name }}</h2><strong>PKR {{ number_format($plan->price, 0) }}</strong><p>{{ $plan->description ?: $plan->duration_days.' days of seller access.' }}</p><form action="{{ route('seller.subscription.store') }}" method="POST">@csrf<input type="hidden" name="subscription_plan_id" value="{{ $plan->id }}"><div class="form-field"><label for="payment_method_{{ $plan->id }}">Payment method</label><select id="payment_method_{{ $plan->id }}" name="payment_method" required><option value="bank_transfer">Bank Transfer</option><option value="jazzcash_easypaisa">JazzCash / EasyPaisa</option></select></div><div class="form-field"><label for="reference_{{ $plan->id }}">Transaction reference</label><input id="reference_{{ $plan->id }}" name="transaction_reference" required placeholder="Your payment reference"></div><div class="form-field"><label for="notes_{{ $plan->id }}">Payment notes <span>(optional)</span></label><textarea id="notes_{{ $plan->id }}" name="payment_notes" rows="2"></textarea></div><button class="btn btn-primary btn-block" type="submit">Submit Payment</button></form></div>@endforeach</div>@endif
    <p style="margin-top:24px;font-size:.85rem;"><a href="{{ route('seller.dashboard') }}" style="text-decoration:underline;">Back to dashboard</a></p>
</div>
@endsection
