<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use App\Services\InstagramFeedService;

class HomeController extends Controller
{
    public function index(InstagramFeedService $instagram)
    {
        $collections = Category::withCount(['products' => fn ($q) => $q->where('is_active', true)])->get();

        $bestSellers = Product::active()
            ->where('is_best_seller', true)
            ->latest()
            ->take(4)
            ->get();

        $featured = Product::active()
            ->where('is_featured', true)
            ->take(4)
            ->get();

        // Reads only from the local instagram_posts table (the sync cache),
        // so API/token problems can never crash or slow the homepage.
        $instagramPosts = $instagram->latestFeed((int) config('services.instagram.feed_size', 8));

        return view('home.index', compact('collections', 'bestSellers', 'featured', 'instagramPosts'));
    }
}
