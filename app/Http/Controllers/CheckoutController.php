<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Support\Cart;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CheckoutController extends Controller
{
    public function index()
    {
        if (empty(Cart::content())) {
            return redirect()->route('cart.index')->with('error', 'Your cart is empty.');
        }

        return view('checkout.index', [
            'items' => Cart::content(),
            'subtotal' => Cart::subtotal(),
            'shipping' => Cart::shipping(),
            'total' => Cart::total(),
        ]);
    }

    public function store(Request $request)
    {
        if (empty(Cart::content())) {
            return redirect()->route('cart.index')->with('error', 'Your cart is empty.');
        }

        // Never trust client-side validation alone — everything is re-validated here.
        $validated = $request->validate([
            'first_name' => ['required', 'string', 'max:100'],
            'last_name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'max:150'],
            'phone' => ['required', 'string', 'max:30'],
            'address' => ['required', 'string', 'max:255'],
            'city' => ['required', 'string', 'max:100'],
            'payment_method' => ['required', 'in:cod,bank_transfer,jazzcash_easypaisa'],
        ]);

        $order = DB::transaction(function () use ($validated) {
            $subtotal = Cart::subtotal();
            $shipping = Cart::shipping();

            $order = Order::create([
                ...$validated,
                'subtotal' => $subtotal,
                'shipping' => $shipping,
                'total' => $subtotal + $shipping,
            ]);

            foreach (Cart::content() as $row) {
                $product = Product::find($row['product_id']);

                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $product?->id,
                    'product_name' => $row['name'],
                    'shape' => $row['shape'],
                    'size' => $row['size'],
                    'quantity' => $row['qty'],
                    'price' => $row['price'],
                    'line_total' => $row['price'] * $row['qty'],
                ]);
            }

            return $order;
        });

        Cart::clear();

        return redirect()->route('checkout.success', $order)->with('success', 'Order placed successfully!');
    }

    public function success(Order $order)
    {
        return view('checkout.success', compact('order'));
    }
}
