<?php

namespace Tests\Feature\Admin;

use App\Models\Admin;
use App\Models\Category;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Tests\TestCase;

class AdminOrderTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): Admin
    {
        return Admin::create([
            'name' => 'Store Admin',
            'email' => 'admin-'.Str::random(8).'@example.com',
            'password' => Hash::make('AdminPass123'),
        ]);
    }

    private function makeOrder(): Order
    {
        $category = Category::create(['name' => 'Test', 'slug' => 'test-'.Str::random(6)]);
        Product::create([
            'category_id' => $category->id,
            'name' => 'Test Set',
            'slug' => 'test-set-'.Str::random(6),
            'sku' => 'SKU-'.Str::random(6),
            'price' => 2000,
            'stock' => 5,
            'is_active' => true,
        ]);

        return Order::create([
            'first_name' => 'Sana',
            'last_name' => 'Malik',
            'email' => 'sana-'.Str::random(6).'@example.com',
            'phone' => '+92 300 1234567',
            'address' => '123 Blossom Lane',
            'city' => 'Karachi',
            'payment_method' => 'cod',
            'subtotal' => 2000,
            'shipping' => 200,
            'total' => 2200,
            'status' => 'pending',
            'payment_status' => 'pending',
        ]);
    }

    public function test_admin_can_update_order_status_to_any_documented_status(): void
    {
        $order = $this->makeOrder();

        // The old UI used a 'completed' value that is NOT in Order::STATUSES —
        // it was rejected by validation. Use one of the documented statuses.
        $response = $this->actingAs($this->admin(), 'admin')
            ->patch(route('admin.orders.updateStatus', $order), ['status' => 'out_for_delivery']);

        $response->assertRedirect();
        $this->assertSame('out_for_delivery', $order->fresh()->status);
    }

    public function test_admin_cannot_set_an_undocumented_status(): void
    {
        $order = $this->makeOrder();

        $response = $this->actingAs($this->admin(), 'admin')
            ->patch(route('admin.orders.updateStatus', $order), ['status' => 'completed']);

        $response->assertSessionHasErrors('status');
        $this->assertSame('pending', $order->fresh()->status);
    }

    public function test_cancelling_an_order_restores_the_reserved_stock(): void
    {
        $order = $this->makeOrder();
        $product = Product::first();
        $order->items()->create([
            'product_id' => $product->id,
            'product_name' => $product->name,
            'quantity' => 2,
            'price' => 2000,
            'line_total' => 4000,
        ]);
        $stockBefore = $product->fresh()->stock;

        $response = $this->actingAs($this->admin(), 'admin')
            ->patch(route('admin.orders.updateStatus', $order), ['status' => 'cancelled']);

        $response->assertRedirect();
        $this->assertSame($stockBefore + 2, $product->fresh()->stock);
    }

    public function test_admin_can_update_payment_status(): void
    {
        $order = $this->makeOrder();

        $response = $this->actingAs($this->admin(), 'admin')
            ->patch(route('admin.orders.updatePaymentStatus', $order), ['payment_status' => 'paid']);

        $response->assertRedirect();
        $this->assertSame('paid', $order->fresh()->payment_status);
    }

    public function test_admin_login_page_renders_login_fields(): void
    {
        $response = $this->get(route('admin.login'));

        $response->assertOk();
        $response->assertSee('name="email"', false);
        $response->assertSee('name="password"', false);
    }
}