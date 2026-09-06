<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Order;
use App\Models\Product;
use App\Models\ShippingRate;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class CheckoutTest extends TestCase
{
    use RefreshDatabase;

    private function makeProduct(array $overrides = []): Product
    {
        $category = Category::create(['name' => 'Test Category', 'slug' => 'test-category-'.Str::random(6)]);

        return Product::create(array_merge([
            'category_id' => $category->id,
            'name' => 'Test Nail Set',
            'slug' => 'test-nail-set-'.Str::random(6),
            'sku' => 'SKU-'.Str::random(6),
            'price' => 2000,
            'stock' => 5,
            'is_active' => true,
        ], $overrides));
    }

    private function checkoutPayload(): array
    {
        return [
            'first_name' => 'Sana',
            'last_name' => 'Malik',
            'email' => 'sana@example.com',
            'phone' => '+92 300 1234567',
            'address' => '123 Blossom Lane',
            'city' => 'Karachi',
            'payment_method' => 'cod',
        ];
    }

    public function test_a_successful_order_decreases_product_stock(): void
    {
        $product = $this->makeProduct(['stock' => 5]);
        $this->post(route('cart.add', $product), ['qty' => 2]);

        $response = $this->post(route('checkout.store'), $this->checkoutPayload());

        $response->assertRedirect();
        $this->assertDatabaseHas('orders', ['email' => 'sana@example.com', 'status' => 'pending']);
        $this->assertEquals(3, $product->fresh()->stock, 'Stock must decrease by the ordered quantity.');
    }

    public function test_checkout_is_blocked_when_stock_is_insufficient(): void
    {
        $product = $this->makeProduct(['stock' => 2]);
        $this->post(route('cart.add', $product), ['qty' => 2]);

        // Someone else buys the remaining stock between add-to-cart and checkout.
        $product->update(['stock' => 1]);

        $response = $this->post(route('checkout.store'), $this->checkoutPayload());

        $response->assertRedirect(route('cart.index'));
        $this->assertEquals(1, $product->fresh()->stock, 'Stock must not change when checkout is rejected.');
        $this->assertDatabaseMissing('orders', ['email' => 'sana@example.com']);
    }

    public function test_order_total_uses_the_current_database_price_not_a_client_supplied_one(): void
    {
        $product = $this->makeProduct(['price' => 2000, 'stock' => 5]);
        $this->post(route('cart.add', $product), ['qty' => 1]);

        // Simulate the price changing after it was added to the cart/session.
        $product->update(['price' => 9999]);

        $this->post(route('checkout.store'), $this->checkoutPayload());

        $order = Order::where('email', 'sana@example.com')->first();
        $this->assertEquals(9999, (float) $order->subtotal, 'The order must charge the live DB price, never a stale cart price.');
    }

    public function test_order_shipping_uses_the_selected_city_and_area_rate(): void
    {
        $product = $this->makeProduct(['price' => 2000, 'stock' => 5]);
        ShippingRate::create([
            'city' => 'Lahore',
            'area' => 'Gulberg',
            'delivery_fee' => 175,
            'free_shipping_threshold' => 5000,
            'is_active' => true,
        ]);
        $this->post(route('cart.add', $product), ['qty' => 1]);

        $payload = array_merge($this->checkoutPayload(), [
            'city' => 'Lahore',
            'area' => 'Gulberg',
            'postal_code' => '54660',
            'payment_method' => 'bank_transfer',
            'transaction_reference' => 'BANK-REF-123',
        ]);
        $this->post(route('checkout.store'), $payload);

        $order = Order::where('email', 'sana@example.com')->first();
        $expected = ShippingRate::calculateFee('Lahore', 'Gulberg', (float) $order->subtotal)['fee'];

        $this->assertSame($expected, (float) $order->shipping);
        $this->assertSame('54660', $order->postal_code);
        $this->assertSame('BANK-REF-123', $order->transaction_reference);
    }

    public function test_unknown_city_uses_the_other_cities_shipping_rate(): void
    {
        ShippingRate::create([
            'city' => 'Other Cities',
            'area' => null,
            'delivery_fee' => 275,
            'free_shipping_threshold' => 5000,
            'is_active' => true,
        ]);

        $shipping = ShippingRate::calculateFee('Multan', null, 2000);

        $this->assertSame(275.0, $shipping['fee']);
        $this->assertSame('Other Cities', $shipping['zone_label']);
    }

    public function test_non_cod_payment_requires_a_transaction_reference(): void
    {
        $product = $this->makeProduct();
        $this->post(route('cart.add', $product), ['qty' => 1]);

        $response = $this->post(route('checkout.store'), array_merge($this->checkoutPayload(), [
            'payment_method' => 'bank_transfer',
        ]));

        $response->assertSessionHasErrors('transaction_reference');
        $this->assertDatabaseMissing('orders', ['email' => 'sana@example.com']);
    }

    public function test_guest_order_confirmation_requires_the_correct_access_token(): void
    {
        $product = $this->makeProduct();
        $this->post(route('cart.add', $product), ['qty' => 1]);
        $this->post(route('checkout.store'), $this->checkoutPayload());

        $order = Order::where('email', 'sana@example.com')->first();

        // Guessing the sequential ID without the signature must fail.
        $this->get(route('checkout.success', $order))->assertForbidden();

        // The correct access token succeeds.
        $this->get(route('checkout.success', ['order' => $order, 'signature' => $order->access_token]))
            ->assertOk();
    }
}
