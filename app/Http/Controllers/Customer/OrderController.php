<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        // Scoped to the logged-in user only — this is the IDOR protection:
        // there is no way to pass an arbitrary user_id here.
        $orders = $request->user()->orders()->latest()->paginate(10);

        return view('customer.orders.index', compact('orders'));
    }

    public function show(Request $request, Order $order)
    {
        // Authorization check (backed by OrderPolicy) — a customer can never
        // view another customer's order by guessing/incrementing the ID.
        $this->authorize('view', $order);

        $order->load('items');

        return view('customer.orders.show', compact('order'));
    }
}
