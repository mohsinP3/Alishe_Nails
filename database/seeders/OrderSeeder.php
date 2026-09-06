<?php

namespace Database\Seeders;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\ShippingRate;
use Illuminate\Database\Seeder;

class OrderSeeder extends Seeder
{
    /**
     * Creates 5 sample orders, each with 1-3 items pulled from the seeded
     * products, so the orders/order_items tables have realistic fake data
     * to test the Cart -> Checkout -> Order flow against.
     */
    public function run(): void
    {
        $customers = [
            ['first_name' => 'Ayesha', 'last_name' => 'Khan', 'email' => 'ayesha.khan@example.com', 'phone' => '+92 300 1112233', 'city' => 'Karachi', 'address' => 'House 12, Street 4, DHA Phase 5'],
            ['first_name' => 'Sana', 'last_name' => 'Malik', 'email' => 'sana.malik@example.com', 'phone' => '+92 301 2223344', 'city' => 'Lahore', 'address' => 'Flat 3B, Gulberg III'],
            ['first_name' => 'Fatima', 'last_name' => 'Raza', 'email' => 'fatima.raza@example.com', 'phone' => '+92 302 3334455', 'city' => 'Islamabad', 'address' => 'House 7, F-10/2'],
            ['first_name' => 'Zainab', 'last_name' => 'Sheikh', 'email' => 'zainab.sheikh@example.com', 'phone' => '+92 303 4445566', 'city' => 'Karachi', 'address' => 'Apartment 9, Clifton Block 2'],
            ['first_name' => 'Mahnoor', 'last_name' => 'Ali', 'email' => 'mahnoor.ali@example.com', 'phone' => '+92 304 5556677', 'city' => 'Rawalpindi', 'address' => 'House 21, Satellite Town'],
        ];

        $paymentMethods = ['cod', 'bank_transfer', 'jazzcash_easypaisa'];
        $statuses = ['pending', 'processing', 'shipped', 'delivered', 'confirmed'];
        $products = Product::all();

        if ($products->isEmpty()) {
            $this->command?->warn('Skipping OrderSeeder: no products found — run ProductSeeder first.');

            return;
        }

        foreach ($customers as $i => $customer) {
            $itemsForOrder = $products->random(min(rand(1, 3), $products->count()));
            $subtotal = 0;

            $order = Order::create([
                ...$customer,
                'payment_method' => $paymentMethods[$i % count($paymentMethods)],
                'status' => $statuses[$i % count($statuses)],
                'subtotal' => 0,
                'shipping' => 0,
                'total' => 0,
            ]);

            foreach ($itemsForOrder as $product) {
                $qty = rand(1, 2);
                $lineTotal = $product->price * $qty;
                $subtotal += $lineTotal;

                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $product->id,
                    'product_name' => $product->name,
                    'shape' => $product->shape,
                    'size' => 'S',
                    'quantity' => $qty,
                    'price' => $product->price,
                    'line_total' => $lineTotal,
                ]);
            }

            $shipping = ShippingRate::calculateFee($customer['city'], null, $subtotal)['fee'];

            $order->update([
                'subtotal' => $subtotal,
                'shipping' => $shipping,
                'total' => $subtotal + $shipping,
            ]);
        }
    }
}
