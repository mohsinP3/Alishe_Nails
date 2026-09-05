<footer class="site-footer">
    <div class="container footer-grid">
        <div class="footer-brand">
            <a href="{{ route('home') }}" class="navbar__brand" style="margin-bottom:14px;">
                <span class="navbar__logo" role="img" aria-label="Alishe Nails logo"></span>
                Alishe
            </a>
            <p>Exquisite handmade press-on nails crafted for elegance and ease.</p>
            <div class="footer-social">
                <i class="fa-brands fa-instagram"></i>
                <a href="https://instagram.com/{{ config('services.instagram.handle', 'alishe_nails') }}" target="_blank" rel="noopener">
                    {{ '@'.config('services.instagram.handle', 'alishe_nails') }}
                </a>
            </div>
        </div>

        <div>
            <h5>Quick Links</h5>
            <ul>
                <li><a href="{{ route('shop.index') }}">New Arrivals</a></li>
                <li><a href="{{ route('shop.index') }}">Best Sellers</a></li>
                <li><a href="{{ route('shop.index') }}">Nail Care Sets</a></li>
                <li><a href="{{ route('shop.index') }}">Gift Cards</a></li>
            </ul>
        </div>

        <div>
            <h5>Help</h5>
            <ul>
                <li><a href="{{ route('contact.index') }}">FAQs</a></li>
                <li><a href="{{ route('contact.index') }}">Shipping &amp; Returns</a></li>
                <li><a href="{{ route('how-to-apply.index') }}">Sizing Guide</a></li>
                <li><a href="{{ route('contact.index') }}">Track Order</a></li>
            </ul>
        </div>

        <div>
            <h5>Stay Inspired</h5>
            <p style="font-size:.85rem;color:rgba(43,29,29,.7);">Join our newsletter for exclusive drops.</p>
            <form action="{{ route('newsletter.subscribe') }}" method="POST" class="newsletter-form">
                @csrf
                <input type="email" name="email" placeholder="Your email" required>
                <button type="submit" class="btn btn-primary" aria-label="Subscribe">
                    <i class="fa-solid fa-paper-plane"></i>
                </button>
            </form>
        </div>
    </div>

    <div class="footer-bottom">
        &copy; {{ date('Y') }} Alishe Nails. Handcrafted with love.
    </div>
</footer>
