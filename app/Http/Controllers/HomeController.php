<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;

class HomeController extends Controller
{
    public function index()
    {
        $collections = Category::withCount('products')->get();

        $bestSellers = Product::where('is_best_seller', true)
            ->latest()
            ->take(4)
            ->get();

        $featured = Product::where('is_featured', true)
            ->take(4)
            ->get();

        return view('home.index', compact('collections', 'bestSellers', 'featured'));
    }
}
