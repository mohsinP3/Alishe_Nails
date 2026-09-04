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

    public static function add(Product $product, int $qty = 1, ?string $shape = null, ?string $size = null): string
    {
        $cart = self::content();

        // Merge quantity if the exact same product+shape+size already exists.
        foreach ($cart as $rowId => $row) {
            if ($row['product_id'] === $product->id && $row['shape'] === $shape && $row['size'] === $size) {
                $cart[$rowId]['qty'] += $qty;
                Session::put(self::CART_KEY, $cart);

                return $rowId;
            }
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
            'qty' => max(1, $qty),
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
        } else {
            $cart[$rowId]['qty'] = $qty;
        }

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
