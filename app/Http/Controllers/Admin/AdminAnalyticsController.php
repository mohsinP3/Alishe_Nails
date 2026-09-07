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
        // Revenue for each of the last 7 days, oldest first — used for a simple
        // bar chart. One grouped query instead of one SUM query per day.
        $revenueRows = Order::query()
            ->where('status', '!=', 'cancelled')
            ->whereBetween('created_at', [
                Carbon::today()->subDays(6)->startOfDay(),
                Carbon::today()->endOfDay(),
            ])
            ->groupBy('date')
            ->selectRaw('DATE(created_at) as date, SUM(total) as total')
            ->pluck('total', 'date');

        $revenueByDay = collect(range(6, 0))->map(fn ($i) => [
            'label' => Carbon::today()->subDays($i)->format('D'),
            'total' => (float) ($revenueRows[Carbon::today()->subDays($i)->toDateString()] ?? 0),
        ]);

        $ordersByStatus = Order::selectRaw('status, count(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        $topProducts = OrderItem::selectRaw('product_name, SUM(quantity) as units_sold, SUM(line_total) as revenue')
            ->whereHas('order', fn ($query) => $query->where('status', '!=', 'cancelled'))
            ->groupBy('product_name')
            ->orderByDesc('units_sold')
            ->take(5)
            ->get();

        $activeOrders = Order::where('status', '!=', 'cancelled');
        $averageOrderValue = (clone $activeOrders)->count() > 0 ? $activeOrders->avg('total') : 0;

        // Instagram Analytics Integration
        $token = config('services.instagram.access_token');
        $businessId = config('services.instagram.business_account_id');
        $instagramHandle = config('services.instagram.handle', 'alishe_nails');
        $instagramData = null;
        $instagramConnected = false;

        if ($token && $businessId) {
            try {
                $response = \Illuminate\Support\Facades\Http::timeout(10)->get("https://graph.facebook.com/v19.0/{$businessId}", [
                    'fields' => 'name,username,profile_picture_url,followers_count,follows_count,media_count,media{id,caption,media_type,media_url,permalink,like_count,comments_count,timestamp}',
                    'access_token' => $token,
                ]);
                if ($response->successful()) {
                    $instagramData = $response->json();
                    $instagramConnected = true;
                }
            } catch (\Throwable $e) {
                \Illuminate\Support\Facades\Log::warning('Instagram API fetch error: '.$e->getMessage());
            }
        }

        return view('admin.analytics.index', compact(
            'revenueByDay',
            'ordersByStatus',
            'topProducts',
            'averageOrderValue',
            'instagramData',
            'instagramConnected',
            'instagramHandle'
        ));
    }
}
