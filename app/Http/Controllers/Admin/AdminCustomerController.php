<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;

class AdminCustomerController extends Controller
{
    /**
     * There is no separate customers table (no account/login system was
     * requested for shoppers) — every checkout captures name/email/phone on
     * the Order itself, so "customers" here are grouped from that order
     * history. If real customer accounts are added later, swap this query
     * for Customer::query() without touching the view.
     */
    public function index(Request $request)
    {
        $query = Order::selectRaw('
                email,
                MAX(first_name) as first_name,
                MAX(last_name) as last_name,
                MAX(phone) as phone,
                MAX(city) as city,
                COUNT(*) as orders_count,
                SUM(total) as total_spent,
                MAX(created_at) as last_order_at
            ')
            ->groupBy('email')
            ->orderByDesc('total_spent');

        if ($search = $request->string('q')->trim()->toString()) {
            $query->having('first_name', 'like', "%{$search}%")
                ->orHaving('last_name', 'like', "%{$search}%")
                ->orHaving('email', 'like', "%{$search}%");
        }

        $customers = $query->paginate(15)->withQueryString();

        return view('admin.customers.index', compact('customers'));
    }
}
