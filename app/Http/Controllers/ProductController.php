<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function show(Request $request, Product $product)
    {
        if (! $product->is_active) {
            abort(404);
        }

        $product->load(['category']);
        $product->setRelation('reviews', $product->approvedReviews()->latest()->get());

        $related = Product::active()
            ->where('id', '!=', $product->id)
            ->when($product->category_id, fn ($q) => $q->where('category_id', $product->category_id))
            ->take(4)
            ->get();

        $userHasReviewed = $request->user()
            && $product->reviews()->where('user_id', $request->user()->id)->exists();

        return view('products.show', compact('product', 'related', 'userHasReviewed'));
    }
}
