<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\User;
use Illuminate\Http\Request;

class AdminCustomerController extends Controller
{
    /**
     * Real registered customer accounts (App\Models\User), each with their
     * order count/total spent. Guest checkouts (no account) are counted
     * separately below since they're not tied to a customer record.
     */
    public function index(Request $request)
    {
        $query = User::withCount('orders')
            ->withSum('orders', 'total')
            ->orderByDesc('orders_sum_total');

        if ($search = $request->string('q')->trim()->toString()) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $customers = $query->paginate(15)->withQueryString();

        $guestOrderCount = Order::whereNull('user_id')->count();

        return view('admin.customers.index', compact('customers', 'guestOrderCount'));
    }

    public function show(User $customer)
    {
        $customer->load(['orders' => fn ($q) => $q->latest()]);

        return view('admin.customers.show', compact('customer'));
    }
}
