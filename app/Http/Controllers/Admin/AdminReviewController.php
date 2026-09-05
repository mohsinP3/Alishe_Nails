<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Review;
use Illuminate\Http\Request;

class AdminReviewController extends Controller
{
    public function index(Request $request)
    {
        $query = Review::with('product')->latest();

        if ($request->string('status')->toString() === 'pending') {
            $query->where('is_approved', false);
        } elseif ($request->string('status')->toString() === 'approved') {
            $query->where('is_approved', true);
        }

        $reviews = $query->paginate(15)->withQueryString();

        return view('admin.reviews.index', compact('reviews'));
    }

    public function approve(Review $review)
    {
        $review->update(['is_approved' => true]);

        return back()->with('success', 'Review approved and now visible on the product page.');
    }

    public function reject(Review $review)
    {
        $review->update(['is_approved' => false]);

        return back()->with('success', 'Review rejected / hidden.');
    }

    public function destroy(Review $review)
    {
        $review->delete();

        return back()->with('success', 'Review deleted.');
    }
}
