<?php

namespace App\Http\Controllers;

use App\Models\Product;

class ProductController extends Controller
{
    public function show(Product $product)
    {
        $product->load(['reviews', 'category']);

        $related = Product::where('id', '!=', $product->id)
            ->when($product->category_id, fn ($q) => $q->where('category_id', $product->category_id))
            ->take(4)
            ->get();

        return view('products.show', compact('product', 'related'));
    }
}
