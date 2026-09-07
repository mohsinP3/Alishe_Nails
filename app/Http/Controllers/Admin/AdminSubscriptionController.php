<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SellerSubscription;
use App\Models\SubscriptionPlan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminSubscriptionController extends Controller
{
    public function index()
    {
        $plans = SubscriptionPlan::withCount('sellerSubscriptions')->orderBy('duration_days')->get();
        $subscriptions = SellerSubscription::with(['seller', 'plan'])->latest()->paginate(20);
        return view('admin.subscriptions.index', compact('plans', 'subscriptions'));
    }

    public function storePlan(Request $request)
    {
        SubscriptionPlan::create($request->validate([
            'name' => ['required', 'string', 'max:100'],
            'duration_days' => ['required', 'integer', 'min:1', 'max:3660'],
            'price' => ['required', 'numeric', 'min:0', 'max:9999999.99'],
            'description' => ['nullable', 'string', 'max:1000'],
        ]));
        return back()->with('success', 'Subscription plan created.');
    }

    public function updatePlan(Request $request, SubscriptionPlan $plan)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'duration_days' => ['required', 'integer', 'min:1', 'max:3660'],
            'price' => ['required', 'numeric', 'min:0', 'max:9999999.99'],
            'description' => ['nullable', 'string', 'max:1000'],
            'is_active' => ['nullable', 'boolean'],
        ]);
        $data['is_active'] = $request->boolean('is_active');
        $plan->update($data);
        return back()->with('success', 'Subscription plan updated.');
    }

    public function activate(Request $request, SellerSubscription $subscription)
    {
        DB::transaction(function () use ($request, $subscription) {
            SellerSubscription::where('seller_id', $subscription->seller_id)
                ->where('status', 'active')->update(['status' => 'expired']);
            $startsAt = now();
            $subscription->update([
                'status' => 'active',
                'starts_at' => $startsAt,
                'expires_at' => $startsAt->copy()->addDays($subscription->duration_days),
                'reviewed_at' => now(),
                'reviewed_by' => $request->user('admin')->id,
            ]);
            $subscription->seller->products()->update(['is_active' => true]);
        });

        return back()->with('success', 'Subscription activated and seller products made visible.');
    }

    public function reject(Request $request, SellerSubscription $subscription)
    {
        $subscription->update(['status' => 'rejected', 'reviewed_at' => now(), 'reviewed_by' => $request->user('admin')->id]);
        return back()->with('success', 'Subscription payment rejected.');
    }
}
