<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Support\Carbon;

class AdminAnalyticsController extends Controller
{
    public function index()
    {
        // Revenue for each of the last 7 days, oldest first — used for a simple bar chart.
        $days = collect(range(6, 0))->map(fn ($i) => Carbon::today()->subDays($i));

        $revenueByDay = $days->map(function (Carbon $day) {
            return [
                'label' => $day->format('D'),
                'total' => (float) Order::whereDate('created_at', $day)->sum('total'),
            ];
        });

        $ordersByStatus = Order::selectRaw('status, count(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        $topProducts = OrderItem::selectRaw('product_name, SUM(quantity) as units_sold, SUM(line_total) as revenue')
            ->groupBy('product_name')
            ->orderByDesc('units_sold')
            ->take(5)
            ->get();

        $averageOrderValue = Order::count() > 0 ? Order::avg('total') : 0;

        return view('admin.analytics.index', compact(
            'revenueByDay',
            'ordersByStatus',
            'topProducts',
            'averageOrderValue'
        ));
    }
}
