<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;

class AdminDashboardController extends Controller
{
    /**
     * Low-stock threshold. Products at or below this count show up in the
     * "Low Stock Alerts" card and the Tasks & Alerts list.
     */
    private const LOW_STOCK_THRESHOLD = 5;

    public function index()
    {
        $totalOrders = Order::count();
        $totalSales = Order::sum('total');
        $activeCustomers = Order::distinct('email')->count('email');
        $lowStockProducts = Product::where('stock', '<=', self::LOW_STOCK_THRESHOLD)->get();

        $recentOrders = Order::latest()->take(4)->get();

        // "Top Performing" = products with the most units sold across all orders.
        $topPerforming = OrderItem::selectRaw('product_name, SUM(quantity) as units_sold')
            ->groupBy('product_name')
            ->orderByDesc('units_sold')
            ->take(3)
            ->get();

        $totalUnitsSold = OrderItem::sum('quantity');

        $lowStockCount = $lowStockProducts->count();

        return view('admin.dashboard.index', compact(
            'totalOrders',
            'totalSales',
            'activeCustomers',
            'lowStockCount',
            'lowStockProducts',
            'recentOrders',
            'topPerforming',
            'totalUnitsSold'
        ));
    }
}
