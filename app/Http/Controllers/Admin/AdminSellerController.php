<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Seller;
use Illuminate\Http\Request;

class AdminSellerController extends Controller
{
    public function index(Request $request)
    {
        $sellers = Seller::withCount('products')
            ->when($request->status, function ($q, $status) {
                if (! in_array($status, ['pending', 'approved', 'rejected'], true)) {
                    abort(422, 'Invalid status filter.');
                }
                $q->where('status', $status);
            })
            ->latest()->paginate(20)->withQueryString();
        return view('admin.marketplace.sellers', compact('sellers'));
    }

    public function show(Seller $seller)
    {
        $seller->load('products');
        $items = $seller->orderItems()->with('order')->latest()->paginate(20);
        return view('admin.marketplace.seller-show', compact('seller', 'items'));
    }
}
