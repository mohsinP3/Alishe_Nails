<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Support\Cart;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function index()
    {
        $items = Cart::content();

        return view('cart.index', [
            'items' => $items,
            'subtotal' => Cart::subtotal(),
        ]);
    }

    public function add(Request $request, Product $product)
    {
        if ($product->seller && ! $product->seller->hasActiveSubscription()) {
            return back()->with('error', 'This seller subscription has expired and the product is currently unavailable.');
        }

        $validated = $request->validate([
            'qty' => ['nullable', 'integer', 'min:1', 'max:20'],
            'shape' => ['nullable', 'string', 'max:50'],
            'size' => ['nullable', 'string', 'max:20'],
        ]);

        $rowId = Cart::add(
            $product,
            $validated['qty'] ?? 1,
            $validated['shape'] ?? null,
            $validated['size'] ?? null
        );

        if ($rowId === null) {
            $message = $product->stock <= 0
                ? $product->name.' is currently out of stock.'
                : 'Only '.$product->stock.' of '.$product->name.' are available.';

            if ($request->expectsJson()) {
                return response()->json(['message' => $message], 422);
            }

            return back()->with('error', $message);
        }

        if ($request->expectsJson() && ! $request->has('buy_now') && $request->input('redirect') !== 'checkout') {
            return response()->json([
                'cart_count' => Cart::count(),
                'message' => $product->name.' added to your cart.',
            ]);
        }

        if ($request->has('buy_now') || $request->input('redirect') === 'checkout') {
            return redirect()->route('checkout.index')->with('success', $product->name.' added to your checkout order.');
        }

        return back()->with('success', $product->name.' added to your cart.');
    }

    public function update(Request $request, string $rowId)
    {
        $validated = $request->validate([
            'qty' => ['required', 'integer', 'min:0', 'max:20'],
        ]);

        Cart::update($rowId, $validated['qty']);

        return back()->with('success', 'Cart updated.');
    }

    public function remove(string $rowId)
    {
        Cart::remove($rowId);

        return back()->with('success', 'Item removed from cart.');
    }
}
