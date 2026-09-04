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
            'shipping' => Cart::shipping(),
            'total' => Cart::total(),
        ]);
    }

    public function add(Request $request, Product $product)
    {
        $validated = $request->validate([
            'qty' => ['nullable', 'integer', 'min:1', 'max:20'],
            'shape' => ['nullable', 'string', 'max:50'],
            'size' => ['nullable', 'string', 'max:20'],
        ]);

        Cart::add(
            $product,
            $validated['qty'] ?? 1,
            $validated['shape'] ?? null,
            $validated['size'] ?? null
        );

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
