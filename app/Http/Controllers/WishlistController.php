<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class WishlistController extends Controller
{
    public function index(Request $request)
    {
        $products = $request->user()->wishlist()
            ->with('seller')
            ->withCount('approvedReviews')
            ->withAvg('approvedReviews', 'rating')
            ->latest()
            ->paginate(12);

        return view('account.wishlist', compact('products'));
    }

    public function toggle(Request $request, Product $product)
    {
        $user = $request->user();

        if (! $user) {
            return redirect()->route('login');
        }

        $exists = $user->wishlist()->whereKey($product->id)->exists();

        if ($exists) {
            $user->wishlist()->detach($product->id);
            $message = 'Removed from your wishlist.';
            $saved = false;
        } else {
            $user->wishlist()->attach($product->id);
            $message = 'Added to your wishlist.';
            $saved = true;
        }

        if ($request->expectsJson()) {
            return response()->json([
                'saved' => $saved,
                'message' => $message,
            ]);
        }

        return back()->with('success', $message);
    }
}
