<?php

namespace App\Support;

use App\Models\Product;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;

/**
 * Lightweight session-based cart. No external package required.
 * Cart contents are stored in the session under CART_KEY as:
 *   [rowId => ['product_id', 'name', 'slug', 'image', 'price', 'shape', 'size', 'qty']]
 */
class Cart
{
    private const CART_KEY = 'alishe_cart';

    public static function content(): array
    {
        return Session::get(self::CART_KEY, []);
    }

    /**
     * Returns the row ID on success, or null if the product is inactive /
     * out of stock / the requested quantity (existing + new) would exceed
     * available stock. The controller checks for null and shows an error —
     * the cart never silently holds more than what's actually in stock.
     */
    public static function add(Product $product, int $qty = 1, ?string $shape = null, ?string $size = null): ?string
    {
        if (! $product->is_active || $product->stock <= 0) {
            return null;
        }

        $cart = self::content();
        $qty = max(1, $qty);

        // Merge quantity if the exact same product+shape+size already exists.
        foreach ($cart as $rowId => $row) {
            if ($row['product_id'] === $product->id && $row['shape'] === $shape && $row['size'] === $size) {
                $newQty = $row['qty'] + $qty;

                if ($newQty > $product->stock) {
                    return null;
                }

                $cart[$rowId]['qty'] = $newQty;
                Session::put(self::CART_KEY, $cart);

                return $rowId;
            }
        }

        if ($qty > $product->stock) {
            return null;
        }

        $rowId = Str::random(12);
        $cart[$rowId] = [
            'product_id' => $product->id,
            'name' => $product->name,
            'slug' => $product->slug,
            'image' => $product->image,
            'price' => (float) $product->price,
            'shape' => $shape,
            'size' => $size,
            'qty' => $qty,
        ];

        Session::put(self::CART_KEY, $cart);

        return $rowId;
    }

    public static function update(string $rowId, int $qty): void
    {
        $cart = self::content();

        if (! isset($cart[$rowId])) {
            return;
        }

        if ($qty <= 0) {
            unset($cart[$rowId]);
            Session::put(self::CART_KEY, $cart);

            return;
        }

        $product = Product::find($cart[$rowId]['product_id']);
        if (! $product || ! $product->is_active) {
            unset($cart[$rowId]);
            Session::put(self::CART_KEY, $cart);

            return;
        }

        $qty = min($qty, max(0, $product->stock));
        if ($qty <= 0) {
            unset($cart[$rowId]);
            Session::put(self::CART_KEY, $cart);

            return;
        }

        $cart[$rowId]['qty'] = $qty;
        Session::put(self::CART_KEY, $cart);
    }

    public static function remove(string $rowId): void
    {
        $cart = self::content();
        unset($cart[$rowId]);
        Session::put(self::CART_KEY, $cart);
    }

    public static function clear(): void
    {
        Session::forget(self::CART_KEY);
    }

    public static function count(): int
    {
        return collect(self::content())->sum('qty');
    }

    public static function subtotal(): float
    {
        return collect(self::content())->sum(fn ($row) => $row['price'] * $row['qty']);
    }

    public static function shipping(): float
    {
        $subtotal = self::subtotal();

        if ($subtotal <= 0) {
            return 0;
        }

        // Free delivery above the announcement-bar threshold, flat rate below it.
        return $subtotal >= 5000 ? 0 : 200;
    }

    public static function total(): float
    {
        return self::subtotal() + self::shipping();
    }
}
