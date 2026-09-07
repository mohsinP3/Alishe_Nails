<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Seller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminMarketplaceController extends Controller
{
    public function index()
    {
        return redirect()->route('admin.subscriptions.index');
    }

    public function approve(Seller $seller)
    {
        $seller->update(['status' => 'approved', 'approved_at' => now()]);
        return back()->with('success', $seller->name.' is now an approved seller.');
    }

    public function reject(Seller $seller)
    {
        $seller->update(['status' => 'rejected']);
        return back()->with('success', $seller->name.' application was rejected.');
    }

    public function updateSettings(Request $request)
    {
        return redirect()->route('admin.subscriptions.index')->with('error', 'Commission billing has been replaced by seller subscriptions.');
    }

    public function markPaid(Seller $seller)
    {
        DB::transaction(function () use ($seller) {
            $seller->orderItems()->where('payout_status', 'pending')->update(['payout_status' => 'paid']);
        });
        return back()->with('success', 'Pending payout marked as paid for '.$seller->name.'.');
    }
}
