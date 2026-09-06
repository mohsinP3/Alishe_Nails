@extends('layouts.admin')
@section('title', 'Analytics — Alishe Nails Admin')

@section('content')

    <div class="admin-page-head">
        <div>
            <h1>Analytics</h1>
            <p>Store performance over the last 7 days.</p>
        </div>
    </div>

    <div class="dashboard-grid">
        <div class="admin-card">
            <div class="admin-card__head"><h3>Revenue — Last 7 Days</h3></div>
            <canvas id="revenueChart" height="110"></canvas>
        </div>

        <div class="admin-card">
            <div class="admin-card__head"><h3>Orders by Status</h3></div>
            @forelse ($ordersByStatus as $status => $count)
                <div class="legend-item">
                    <span><span class="status-pill status-{{ $status }}">{{ ucfirst($status) }}</span></span>
                    <span>{{ $count }}</span>
                </div>
            @empty
                <p style="font-size:.85rem;color:rgba(43,29,29,.6);">No orders yet.</p>
            @endforelse

            <div style="margin-top:20px;padding-top:16px;border-top:1px solid var(--border-soft);">
                <div class="stat-card__label" style="margin-bottom:6px;">Average Order Value</div>
                <div class="stat-card__value" style="font-size:1.3rem;">PKR {{ number_format($averageOrderValue, 0) }}</div>
            </div>
        </div>
    </div>

    <div class="admin-card" style="margin-bottom:20px;">
        <div class="admin-card__head">
            <h3><i class="fa-brands fa-instagram" style="color:#e1306c;"></i> Instagram Business Analytics</h3>
            <a href="https://instagram.com/{{ $instagramHandle }}" target="_blank" rel="noopener" class="btn btn-outline btn-sm">
                {{ '@'.$instagramHandle }} <i class="fa-solid fa-arrow-up-right-from-square"></i>
            </a>
        </div>

        @if ($instagramConnected && $instagramData)
            <div style="display:grid;grid-template-columns:repeat(auto-fit, minmax(140px, 1fr));gap:16px;margin-bottom:20px;">
                <div class="stat-card" style="border:1px solid var(--border-soft);padding:16px;">
                    <div class="stat-card__label">Followers</div>
                    <div class="stat-card__value" style="font-size:1.4rem;">{{ number_format($instagramData['followers_count'] ?? 0) }}</div>
                </div>
                <div class="stat-card" style="border:1px solid var(--border-soft);padding:16px;">
                    <div class="stat-card__label">Following</div>
                    <div class="stat-card__value" style="font-size:1.4rem;">{{ number_format($instagramData['follows_count'] ?? 0) }}</div>
                </div>
                <div class="stat-card" style="border:1px solid var(--border-soft);padding:16px;">
                    <div class="stat-card__label">Media Posts</div>
                    <div class="stat-card__value" style="font-size:1.4rem;">{{ number_format($instagramData['media_count'] ?? 0) }}</div>
                </div>
            </div>

            @if (!empty($instagramData['media']['data']))
                <h4 style="font-size:.9rem;margin-bottom:12px;">Recent Instagram Posts</h4>
                <div style="display:grid;grid-template-columns:repeat(auto-fill, minmax(120px, 1fr));gap:12px;">
                    @foreach (array_slice($instagramData['media']['data'], 0, 6) as $post)
                        <a href="{{ $post['permalink'] ?? '#' }}" target="_blank" rel="noopener" style="display:block;position:relative;border-radius:8px;overflow:hidden;">
                            @if (!empty($post['media_url']))
                                <img src="{{ $post['media_url'] }}" alt="Instagram post" style="width:100%;height:100px;object-fit:cover;">
                            @else
                                <div style="height:100px;background:var(--blush);display:flex;align-items:center;justify-content:center;font-size:.75rem;color:var(--espresso);">Post</div>
                            @endif
                            <div style="position:absolute;bottom:0;inset-x:0;background:rgba(0,0,0,.6);color:#fff;font-size:.7rem;padding:4px 6px;display:flex;justify-content:space-between;">
                                <span><i class="fa-solid fa-heart"></i> {{ $post['like_count'] ?? 0 }}</span>
                                <span><i class="fa-solid fa-comment"></i> {{ $post['comments_count'] ?? 0 }}</span>
                            </div>
                        </a>
                    @endforeach
                </div>
            @endif
        @else
            <div style="background:var(--ivory);border:1px solid var(--border-soft);border-radius:8px;padding:20px;text-align:left;">
                <div style="display:flex;align-items:center;gap:10px;margin-bottom:8px;">
                    <i class="fa-solid fa-circle-info" style="color:var(--rose-dark);font-size:1.2rem;"></i>
                    <strong style="font-size:.95rem;">Instagram API Credentials Not Connected</strong>
                </div>
                <p style="font-size:.85rem;color:rgba(43,29,29,.7);margin-bottom:12px;max-width:650px;">
                    Live Instagram insights require an authorized Meta Graph API connection. No synthetic or fake metrics are displayed.
                </p>
                <div style="font-size:.82rem;background:#fff;padding:12px 14px;border-radius:6px;border:1px solid var(--border-soft);font-family:monospace;">
                    INSTAGRAM_BUSINESS_ACCOUNT_ID="your-instagram-business-account-id"<br>
                    INSTAGRAM_ACCESS_TOKEN="your-meta-user-access-token"
                </div>
                <p style="font-size:.78rem;color:rgba(43,29,29,.55);margin-top:10px;margin-bottom:0;">
                    Target Instagram handle: <strong>@{{ $instagramHandle }}</strong> (<a href="https://instagram.com/{{ $instagramHandle }}" target="_blank" rel="noopener" style="text-decoration:underline;">View Profile</a>)
                </p>
            </div>
        @endif
    </div>

    <div class="admin-card">
        <div class="admin-card__head"><h3>Top Products by Units Sold</h3></div>
        <table class="data-table">
            <thead>
                <tr>
                    <th>Product</th>
                    <th>Units Sold</th>
                    <th>Revenue</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($topProducts as $product)
                    <tr>
                        <td>{{ $product->product_name }}</td>
                        <td>{{ $product->units_sold }}</td>
                        <td>PKR {{ number_format($product->revenue, 0) }}</td>
                    </tr>
                @empty
                    <tr><td colspan="3" style="text-align:center;padding:24px;">No sales data yet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @push('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.4/chart.umd.min.js"></script>
    <script>
        const ctx = document.getElementById('revenueChart');
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: @json($revenueByDay->pluck('label')),
                datasets: [{
                    label: 'Revenue (PKR)',
                    data: @json($revenueByDay->pluck('total')),
                    backgroundColor: '#B97A87',
                    borderRadius: 6,
                }]
            },
            options: {
                plugins: { legend: { display: false } },
                scales: { y: { beginAtZero: true } }
            }
        });
    </script>
    @endpush

@endsection
