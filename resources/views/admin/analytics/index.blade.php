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
