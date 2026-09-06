@extends('layouts.admin')
@section('title', 'Dashboard Overview — Alishe Nails Admin')

@section('content')

    <div class="admin-page-head">
        <div>
            <h1>Dashboard Overview</h1>
            <p>Welcome back to the command center.</p>
        </div>
        <a href="{{ route('admin.products.create') }}" class="btn btn-primary">
            <i class="fa-solid fa-plus"></i> New Product
        </a>
    </div>

    {{-- ---------- Stat cards ---------- --}}
    <div class="stat-grid">
        <div class="stat-card">
            <div class="stat-card__head">
                <span class="stat-card__label">Total Orders</span>
                <span class="stat-card__icon" style="background:var(--blush);color:var(--rose-dark);"><i class="fa-solid fa-cart-shopping"></i></span>
            </div>
            <div class="stat-card__value">{{ number_format($totalOrders) }}</div>
            <div class="stat-card__trend up"><i class="fa-solid fa-arrow-trend-up"></i> All-time orders</div>
        </div>

        <div class="stat-card">
            <div class="stat-card__head">
                <span class="stat-card__label">Total Sales</span>
                <span class="stat-card__icon" style="background:#F6D6DC;color:var(--rose-dark);"><i class="fa-solid fa-sack-dollar"></i></span>
            </div>
            <div class="stat-card__value">PKR {{ number_format($totalSales, 0) }}</div>
            <div class="stat-card__trend up"><i class="fa-solid fa-arrow-trend-up"></i> All-time revenue</div>
        </div>

        <div class="stat-card">
            <div class="stat-card__head">
                <span class="stat-card__label">Active Customers</span>
                <span class="stat-card__icon" style="background:#FBEBB5;color:#8A6D00;"><i class="fa-solid fa-users"></i></span>
            </div>
            <div class="stat-card__value">{{ number_format($activeCustomers) }}</div>
            <div class="stat-card__trend up"><i class="fa-solid fa-arrow-trend-up"></i> Unique buyers</div>
        </div>

        <div class="stat-card">
            <div class="stat-card__head">
                <span class="stat-card__label">Low Stock Alerts</span>
                <span class="stat-card__icon" style="background:#F7D6D6;color:#a12a2a;"><i class="fa-solid fa-triangle-exclamation"></i></span>
            </div>
            <div class="stat-card__value">{{ number_format($lowStockCount) }}</div>
            <div class="stat-card__trend warn">
                @if ($lowStockCount > 0)
                    <i class="fa-solid fa-circle-exclamation"></i> Requires attention
                @else
                    <i class="fa-solid fa-circle-check"></i> All stocked up
                @endif
            </div>
        </div>
    </div>

    {{-- ---------- Recent orders + Top performing ---------- --}}
    <div class="dashboard-grid">
        <div class="admin-card">
            <div class="admin-card__head">
                <h3>Recent Orders</h3>
                <a href="{{ route('admin.orders.index') }}">View All &rarr;</a>
            </div>

            <table class="data-table">
                <thead>
                    <tr>
                        <th>Order ID</th>
                        <th>Customer</th>
                        <th>Date</th>
                        <th>Amount</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($recentOrders as $order)
                        <tr>
                            <td>
                                <a href="{{ route('admin.orders.show', $order) }}">#{{ $order->order_number }}</a>
                            </td>
                            <td>
                                <span class="table-avatar">{{ strtoupper(substr($order->first_name, 0, 1).substr($order->last_name, 0, 1)) }}</span>
                                {{ $order->first_name }} {{ $order->last_name }}
                            </td>
                            <td>{{ $order->created_at->format('M j, Y') }}</td>
                            <td>PKR {{ number_format($order->total, 0) }}</td>
                            <td><span class="status-pill status-{{ $order->status }}">{{ ucfirst($order->status) }}</span></td>
                        </tr>
                    @empty
                        <tr><td colspan="5" style="text-align:center;padding:24px;">No orders yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="admin-card">
            <div class="admin-card__head">
                <h3>Top Performing</h3>
            </div>

            <div class="top-performing__total">
                <div class="value">{{ $totalUnitsSold > 999 ? round($totalUnitsSold / 1000, 1).'k' : $totalUnitsSold }}</div>
                <div class="label">Units Sold</div>
            </div>

            @php
                $dotColors = ['var(--espresso)', 'var(--rose)', 'var(--blush)'];
            @endphp

            @forelse ($topPerforming as $i => $product)
                <div class="legend-item">
                    <span><span class="legend-dot" style="background:{{ $dotColors[$i] ?? 'var(--blush)' }};"></span>{{ $product->product_name }}</span>
                    <span>{{ $totalUnitsSold > 0 ? round(($product->units_sold / $totalUnitsSold) * 100) : 0 }}%</span>
                </div>
            @empty
                <p style="font-size:.85rem;color:rgba(43,29,29,.6);">No sales data yet.</p>
            @endforelse
        </div>
    </div>

    {{-- ---------- Campaign + Tasks ---------- --}}
    <div class="dashboard-grid">
        <div class="campaign-banner">
            @if (file_exists(public_path('images/brand/about-hands.jpg')))
                <img src="{{ asset('images/brand/about-hands.jpg') }}" alt="Spring Collection">
            @endif
            <div class="campaign-banner__content">
                <span class="campaign-banner__tag">Campaign</span>
                <h3>Spring Collection Setup</h3>
                <p>Prepare inventory and marketing materials for the upcoming seasonal drop.</p>
            </div>
        </div>

        <div class="admin-card">
            <div class="admin-card__head">
                <h3>Tasks &amp; Alerts</h3>
            </div>

            @if ($lowStockCount > 0)
                <div class="task-item">
                    <div class="task-item__icon"><i class="fa-solid fa-box-open"></i></div>
                    <div class="task-item__body">
                        <strong>Restock Required</strong>
                        <small>
                            {{ $lowStockProducts->pluck('name')->take(1)->first() }} set is below threshold
                            ({{ $lowStockProducts->first()->stock }} left).
                        </small>
                    </div>
                    <a href="{{ route('admin.products.index') }}" class="task-item__action">View</a>
                </div>
            @endif

            @php($pendingOrders = \App\Models\Order::where('status', 'pending')->count())
            @if ($pendingOrders > 0)
                <div class="task-item">
                    <div class="task-item__icon"><i class="fa-solid fa-truck"></i></div>
                    <div class="task-item__body">
                        <strong>Pending Orders</strong>
                        <small>{{ $pendingOrders }} order(s) awaiting processing.</small>
                    </div>
                    <a href="{{ route('admin.orders.index', ['status' => 'pending']) }}" class="task-item__action">View</a>
                </div>
            @endif

            @if ($lowStockCount == 0 && $pendingOrders == 0)
                <p style="font-size:.85rem;color:rgba(43,29,29,.6);">All caught up — no pending tasks.</p>
            @endif
        </div>
    </div>

@endsection
