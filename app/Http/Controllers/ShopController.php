<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class ShopController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::query()->with('category');

        if ($search = $request->string('q')->trim()->toString()) {
            $query->where('name', 'like', "%{$search}%");
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

        // Facet counts for the filter sidebar, computed once from all products.
        $shapeCounts = Product::selectRaw('shape, count(*) as total')->groupBy('shape')->pluck('total', 'shape');
        $lengthCounts = Product::selectRaw('length, count(*) as total')->groupBy('length')->pluck('total', 'length');
        $finishCounts = Product::selectRaw('finish, count(*) as total')->groupBy('finish')->pluck('total', 'finish');

        return view('shop.index', compact('products', 'shapeCounts', 'lengthCounts', 'finishCounts'));
    }
}
