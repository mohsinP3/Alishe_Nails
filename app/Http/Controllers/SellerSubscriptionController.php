<?php

namespace App\Http\Controllers;

use App\Http\Requests\SellerSubscriptionRequest;
use App\Models\SellerSubscription;
use App\Models\SubscriptionPlan;
use Illuminate\Support\Facades\Auth;

class SellerSubscriptionController extends Controller
{
    public function create()
    {
        $plans = SubscriptionPlan::where('is_active', true)->orderBy('duration_days')->get();
        return view('seller.subscription', compact('plans'));
    }

    public function store(SellerSubscriptionRequest $request)
    {
        $seller = Auth::guard('seller')->user();
        $plan = SubscriptionPlan::where('is_active', true)->findOrFail($request->validated()['subscription_plan_id']);

        SellerSubscription::create([
            'seller_id' => $seller->id,
            'subscription_plan_id' => $plan->id,
            'price' => $plan->price,
            'duration_days' => $plan->duration_days,
            'status' => 'pending',
            'payment_method' => $request->validated()['payment_method'],
            'transaction_reference' => $request->validated()['transaction_reference'],
            'payment_notes' => $request->validated()['payment_notes'] ?? null,
        ]);

        return redirect()->route('seller.dashboard')->with('success', 'Payment submitted. Your subscription will activate after admin verification.');
    }
}
