@extends('layouts.app')
@section('title', 'Policies — Alishe Nails')

@section('content')
    <div class="page-header">
        <h1>Our Policies</h1>
        <p>Clear information about returns, refunds, and how we handle your data.</p>
    </div>

    <div class="container" style="max-width:820px;padding-bottom:64px;">
        <section class="checkout-card" aria-labelledby="returns-policy-heading">
            <span class="eyebrow" style="display:block;color:var(--rose);font-size:.8rem;letter-spacing:.12em;text-transform:uppercase;margin-bottom:10px;">Returns &amp; Refunds</span>
            <h2 id="returns-policy-heading">No Returns or Exchanges</h2>
            <p style="font-size:1.05rem;font-weight:600;color:var(--rose-dark);">
                Due to the handmade and hygienic nature of our press-on nails, we do not accept returns, exchanges, or refunds on any order once it has been placed.
            </p>
            <p>
                Please review your size, shape, and shade selection carefully before ordering. Our
                <a href="{{ route('how-to-apply.index') }}" style="text-decoration:underline;">How to Apply guide</a>
                can help you prepare and care for your set.
            </p>

            <h3>Damaged, incorrect, or defective orders</h3>
            <p>
                If your order arrives damaged, contains the wrong item, or has a manufacturing defect due to our error, contact us within 48 hours of delivery. Include your order number and clear photos so we can review the issue and make it right.
            </p>
            <p>
                Please use our <a href="{{ route('contact.index') }}" style="text-decoration:underline;">Contact page</a> to reach us. This exception is for verified fulfilment or product faults only and does not change our no-returns, no-exchanges, and no-refunds policy for change-of-mind purchases.
            </p>
        </section>

        <section class="checkout-card" style="margin-top:24px;" aria-labelledby="privacy-policy-heading">
            <span class="eyebrow" style="display:block;color:var(--rose);font-size:.8rem;letter-spacing:.12em;text-transform:uppercase;margin-bottom:10px;">Privacy</span>
            <h2 id="privacy-policy-heading">Privacy Policy</h2>

            <h3>Information we collect and why</h3>
            <p>We collect the information you provide when you order or create an account, including your name, email address, phone number, shipping address, and order history. We use it to fulfil orders, provide account access, respond to customer support requests, and send marketing emails only when you subscribe to our newsletter.</p>

            <h3>Payments</h3>
            <p>We accept Cash on Delivery, Bank Transfer, JazzCash, and EasyPaisa. This site does not collect or store card numbers or other card payment data.</p>

            <h3>Cookies and sessions</h3>
            <p>The site uses browser sessions to keep your cart contents and login session available while you browse and check out. These sessions support normal site functionality; this policy does not claim tracking or advertising cookies that the site does not use.</p>

            <h3>Sharing and security</h3>
            <p>We do not sell your personal data. We share the information needed to fulfil your order with the delivery courier, such as your name, phone number, and shipping address. We may also share information when required to provide a service you request or to comply with a legal obligation.</p>

            <h3>Data removal requests</h3>
            <p>To request account deletion or removal of personal data that we are not required to retain for an order or legal purpose, contact us through the <a href="{{ route('contact.index') }}" style="text-decoration:underline;">Contact page</a> with the email address linked to your account.</p>

            <p style="margin:28px 0 0;font-size:.85rem;color:rgba(43,29,29,.65);"><strong>Last updated:</strong> September 6, 2026</p>
        </section>
    </div>
@endsection
