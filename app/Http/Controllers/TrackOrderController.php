<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;

class TrackOrderController extends Controller
{
    public function index()
    {
        return view('track-order.index');
    }

    public function search(Request $request)
    {
        $validated = $request->validate([
            'order_number' => ['required', 'string', 'max:50'],
            'identifier' => ['required', 'string', 'max:150'],
        ]);

        $identifier = trim($validated['identifier']);
        $order = Order::where('order_number', trim($validated['order_number']))
            ->where(function ($query) use ($identifier) {
                $query->where('email', $identifier)
                    ->orWhere('phone', $identifier);
            })
            ->first();

        if (! $order) {
            return back()->withErrors([
                'order_number' => 'We could not find an order matching that order number and contact detail.',
            ]);
        }

        $order->load('items');

        if ($request->expectsJson()) {
            return response()->json(['order' => $order]);
        }

        return view('track-order.index', compact('order'));
    }
}
