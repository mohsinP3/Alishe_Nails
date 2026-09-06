<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class ShopController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::active()->with('category');

        if ($search = $request->string('q')->trim()->toString()) {
            $query->where('name', 'like', "%{$search}%");
        }

        if ($categories = $request->input('category')) {
            $categories = (array) $categories;

            if (count($categories) > 0) {
                $query->whereHas('category', function ($q) use ($categories) {
                    $q->whereIn('slug', $categories)->orWhereIn('id', $categories);
                });
            }
        }

        if ($shapes = $request->array('shape')) {
            $query->whereIn('shape', $shapes);
        }

        if ($lengths = $request->array('length')) {
            $query->whereIn('length', $lengths);
        }

        if ($finishes = $request->array('finish')) {
            $query->whereIn('finish', $finishes);
        }

        match ($request->string('sort')->toString()) {
            'price_low' => $query->orderBy('price', 'asc'),
            'price_high' => $query->orderBy('price', 'desc'),
            'best_selling' => $query->orderByDesc('is_best_seller'),
            default => $query->latest(),
        };

        $products = $query->paginate(12)->withQueryString();

        $categories = \App\Models\Category::withCount(['products' => fn ($q) => $q->where('is_active', true)])->get();

        // Facet counts for the filter sidebar, computed once from all active products.
        $shapeCounts = Product::active()->selectRaw('shape, count(*) as total')->groupBy('shape')->pluck('total', 'shape');
        $lengthCounts = Product::active()->selectRaw('length, count(*) as total')->groupBy('length')->pluck('total', 'length');
        $finishCounts = Product::active()->selectRaw('finish, count(*) as total')->groupBy('finish')->pluck('total', 'finish');

        return view('shop.index', compact('products', 'categories', 'shapeCounts', 'lengthCounts', 'finishCounts'));
    }
}
