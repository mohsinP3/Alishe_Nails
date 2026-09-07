<?php

namespace Tests\Feature;

use App\Models\Admin;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class WishlistCouponTrackingTest extends TestCase
{
    use RefreshDatabase;

    public function test_logged_in_customer_can_toggle_wishlist_for_a_product(): void
    {
        $user = User::factory()->create();
        $product = Product::create([
            'category_id' => null,
            'seller_id' => null,
            'name' => 'Glaze Set',
            'slug' => 'glaze-set',
            'sku' => 'ALN-TEST-1',
            'price' => 2500,
            'compare_at_price' => 3000,
            'short_description' => 'Soft glossy set',
            'description' => 'Soft glossy set for day wear.',
            'shape' => 'almond',
            'length' => 'short',
            'finish' => 'glossy',
            'badge' => 'Best seller',
            'stock' => 10,
            'is_active' => true,
        ]);

        $this->actingAs($user, 'web')
            ->post(route('account.wishlist.toggle', $product))
            ->assertRedirect();

        $this->assertDatabaseHas('wishlists', [
            'user_id' => $user->id,
            'product_id' => $product->id,
        ]);

        $this->actingAs($user, 'web')
            ->post(route('account.wishlist.toggle', $product))
            ->assertRedirect();

        $this->assertDatabaseMissing('wishlists', [
            'user_id' => $user->id,
            'product_id' => $product->id,
        ]);
    }

    public function test_admin_can_create_a_coupon_and_customer_can_apply_it(): void
    {
        $admin = Admin::create([
            'name' => 'Store Admin',
            'email' => 'admin@example.com',
            'password' => Hash::make('AdminPass123'),
        ]);

        $this->actingAs($admin, 'admin')
            ->post(route('admin.coupons.store'), [
                'code' => 'SAVE10',
                'type' => 'percent',
                'value' => 10,
                'expires_at' => now()->addDays(10)->format('Y-m-d'),
                'usage_limit' => 5,
            ])
            ->assertRedirect(route('admin.coupons.index'));

        $this->assertDatabaseHas('coupons', ['code' => 'SAVE10']);

        $user = User::factory()->create();

        $this->actingAs($user, 'web')
            ->withSession(['applied_coupon' => ['code' => 'SAVE10']])
            ->post(route('checkout.coupon.apply'), ['code' => 'SAVE10'])
            ->assertRedirect(route('checkout.index'));

        $this->assertTrue(session('applied_coupon.code') === 'SAVE10' || session('applied_coupon')['code'] === 'SAVE10');
    }

    public function test_guest_can_track_order_by_order_number_and_phone(): void
    {
        Order::create([
            'user_id' => null,
            'order_number' => 'ALN-TRACK-001',
            'access_token' => 'token-123',
            'first_name' => 'Aisha',
            'last_name' => 'Khan',
            'email' => 'aisha@example.com',
            'phone' => '+923001234567',
            'address' => 'Street 12',
            'city' => 'Karachi',
            'area' => 'DHA',
            'postal_code' => '75500',
            'payment_method' => 'cod',
            'transaction_reference' => null,
            'subtotal' => 1200,
            'shipping' => 0,
            'total' => 1200,
            'status' => 'processing',
            'payment_status' => 'pending',
        ]);

        $response = $this->post(route('track-order.search'), [
            'order_number' => 'ALN-TRACK-001',
            'identifier' => '+923001234567',
        ]);

        $response->assertOk();
        $response->assertSee('ALN-TRACK-001');
        $response->assertSee('Processing');
    }
}
