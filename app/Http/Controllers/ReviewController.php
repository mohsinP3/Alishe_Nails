<?php

namespace App\Http\Controllers;

use App\Http\Requests\ReviewRequest;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Review;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    /**
     * Requires auth:web (route middleware) — only logged-in customers can
     * submit. The unique(product_id, user_id) DB constraint plus this check
     * stops duplicate reviews from the same customer on the same product.
     */
    public function store(ReviewRequest $request, Product $product)
    {
        $user = $request->user();

        if (Review::where('product_id', $product->id)->where('user_id', $user->id)->exists()) {
            return back()->with('error', 'You have already reviewed this product.');
        }

        // Verified-purchase badge: true only if this customer has an order
        // containing this exact product.
        $isVerifiedPurchase = OrderItem::where('product_id', $product->id)
            ->whereHas('order', fn ($q) => $q->where('user_id', $user->id))
            ->exists();

        Review::create([
            'product_id' => $product->id,
            'user_id' => $user->id,
            'customer_name' => $user->name,
            'rating' => $request->validated()['rating'],
            'comment' => $request->validated()['comment'],
            'is_approved' => false, // goes to admin moderation queue first
            'is_verified_purchase' => $isVerifiedPurchase,
        ]);

        return back()->with('success', 'Thanks! Your review has been submitted and will appear once approved.');
    }

    public function destroy(Request $request, Review $review)
    {
        $this->authorize('delete', $review);

        $review->delete();

        return back()->with('success', 'Review deleted.');
    }
}
