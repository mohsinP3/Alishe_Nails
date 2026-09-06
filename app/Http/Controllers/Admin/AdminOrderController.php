<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\OrderStatusUpdateMail;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\ValidationException;

class AdminOrderController extends Controller
{
    public function index(Request $request)
    {
        $query = Order::query()->latest();

        if ($status = $request->string('status')->toString()) {
            $query->where('status', $status);
        }

        if ($search = $request->string('q')->trim()->toString()) {
            $query->where(function ($q) use ($search) {
                $q->where('order_number', 'like', "%{$search}%")
                    ->orWhere('first_name', 'like', "%{$search}%")
                    ->orWhere('last_name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $orders = $query->paginate(15)->withQueryString();

        return view('admin.orders.index', compact('orders'));
    }

    public function show(Order $order)
    {
        $order->load('items.product');

        return view('admin.orders.show', compact('order'));
    }

    public function updateStatus(Request $request, Order $order)
    {
        $validated = $request->validate([
            // Allowlist — a status value can never be anything the client invents.
            'status' => ['required', 'in:'.implode(',', Order::STATUSES)],
        ]);

        $previousStatus = $order->status;

        DB::transaction(function () use ($order, $validated, $previousStatus) {
            $newStatus = $validated['status'];

            // Track restoration so cancelling/reopening remains idempotent.
            if ($newStatus === 'cancelled' && $previousStatus !== 'cancelled' && ! $order->stock_restored_at) {
                foreach ($order->items as $item) {
                    if ($item->product_id) {
                        $item->product()->lockForUpdate()->first()?->increment('stock', $item->quantity);
                    }
                }
                $order->stock_restored_at = now();
            } elseif ($previousStatus === 'cancelled' && $newStatus !== 'cancelled' && $order->stock_restored_at) {
                foreach ($order->items as $item) {
                    $product = $item->product()->lockForUpdate()->first();

                    if (! $product || $product->stock < $item->quantity) {
                        throw ValidationException::withMessages([
                            'status' => 'This order cannot be reopened because there is not enough stock available.',
                        ]);
                    }

                    $product->decrement('stock', $item->quantity);
                }
                $order->stock_restored_at = null;
            }

            $order->status = $newStatus;
            $order->save();
        });

        if ($validated['status'] !== $previousStatus) {
            try {
                Mail::to($order->email)->send(new OrderStatusUpdateMail($order));
            } catch (\Throwable $e) {
                Log::warning('Order status email failed: '.$e->getMessage());
            }
        }

        return back()->with('success', 'Order #'.$order->order_number.' marked as '.$validated['status'].'.');
    }

    public function updatePaymentStatus(Request $request, Order $order)
    {
        $validated = $request->validate([
            'payment_status' => ['required', 'in:'.implode(',', Order::PAYMENT_STATUSES)],
        ]);

        $order->update($validated);

        return back()->with('success', 'Payment status updated to '.$validated['payment_status'].'.');
    }
}
