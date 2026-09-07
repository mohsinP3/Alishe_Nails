<?php

namespace Tests\Feature;

use App\Models\Admin;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Seller;
use App\Models\SellerSubscription;
use App\Models\SubscriptionPlan;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Tests\TestCase;

class MarketplaceTest extends TestCase
{
    use RefreshDatabase;

    private function makeSeller(array $overrides = []): Seller
    {
        return Seller::create(array_merge([
            'name' => 'Nail Creator',
            'email' => 'creator@example.com',
            'instagram_handle' => 'nailcreator',
            'product_details' => 'Chrome and almond sets',
            'password' => Hash::make('SellerPass123'),
            'status' => 'approved',
            'approved_at' => now(),
        ], $overrides));
    }

    private function makeProduct(Seller $seller): Product
    {
        return Product::create([
            'name' => 'Creator Chrome Set',
            'slug' => 'creator-chrome-'.Str::random(6),
            'sku' => 'SEL-'.Str::random(6),
            'price' => 2000,
            'stock' => 5,
            'seller_id' => $seller->id,
            'is_active' => true,
        ]);
    }

    private function activateSubscription(Seller $seller): SellerSubscription
    {
        $plan = SubscriptionPlan::create(['name' => 'Monthly', 'duration_days' => 30, 'price' => 1000, 'is_active' => true]);

        return SellerSubscription::create([
            'seller_id' => $seller->id,
            'subscription_plan_id' => $plan->id,
            'price' => 1000,
            'duration_days' => 30,
            'status' => 'active',
            'payment_method' => 'bank_transfer',
            'transaction_reference' => 'TEST-REF',
            'starts_at' => now(),
            'expires_at' => now()->addDays(30),
        ]);
    }

    private function checkoutPayload(): array
    {
        return [
            'first_name' => 'Sana', 'last_name' => 'Malik', 'email' => 'sana@example.com',
            'phone' => '+92 300 1234567', 'address' => '123 Blossom Lane', 'city' => 'Karachi',
            'payment_method' => 'cod',
        ];
    }

    public function test_influencer_can_apply_and_is_not_logged_in_before_approval(): void
    {
        $response = $this->post(route('seller.apply.store'), [
            'name' => 'New Creator', 'email' => 'new@example.com', 'instagram_handle' => 'newcreator',
            'product_details' => 'Hand-painted sets', 'password' => 'SellerPass123', 'password_confirmation' => 'SellerPass123',
        ]);

        $response->assertRedirect(route('seller.login'));
        $this->assertDatabaseHas('sellers', ['email' => 'new@example.com', 'status' => 'pending']);
        $this->assertGuest('seller');
    }

    public function test_admin_can_approve_seller_and_seller_can_open_dashboard(): void
    {
        $seller = $this->makeSeller(['status' => 'pending', 'approved_at' => null]);
        $admin = Admin::create(['name' => 'Admin', 'email' => 'admin@example.com', 'password' => Hash::make('AdminPass123')]);

        $this->actingAs($admin, 'admin')->post(route('admin.marketplace.sellers.approve', $seller))->assertRedirect();
        $this->assertDatabaseHas('sellers', ['id' => $seller->id, 'status' => 'approved']);

        $this->post(route('seller.login.attempt'), ['email' => $seller->email, 'password' => 'SellerPass123'])
            ->assertRedirect(route('seller.dashboard'));
        $this->assertAuthenticatedAs($seller, 'seller');
        $this->get(route('seller.dashboard'))->assertOk()->assertSee('Your Products');
    }

    public function test_seller_product_is_attributed_and_seller_receives_gross_sale_at_checkout(): void
    {
        $seller = $this->makeSeller();
        $this->activateSubscription($seller);
        $product = $this->makeProduct($seller);
        $this->post(route('cart.add', $product), ['qty' => 2]);

        $this->post(route('checkout.store'), $this->checkoutPayload())->assertRedirect();

        $item = OrderItem::first();
        $this->assertSame($seller->id, $item->seller_id);
        $this->assertSame(0.0, (float) $item->commission_rate);
        $this->assertSame(0.0, (float) $item->commission_amount);
        $this->assertSame(4000.0, (float) $item->seller_earning);
    }

    public function test_seller_cannot_edit_another_sellers_product(): void
    {
        $owner = $this->makeSeller();
        $other = $this->makeSeller(['email' => 'other@example.com']);
        $this->activateSubscription($owner);
        $this->activateSubscription($other);
        $product = $this->makeProduct($owner);

        $this->actingAs($other, 'seller')->get(route('seller.products.edit', $product))->assertForbidden();
    }

    public function test_expired_subscription_hides_seller_product_from_shop(): void
    {
        $seller = $this->makeSeller();
        $subscription = $this->activateSubscription($seller);
        $subscription->update(['status' => 'expired', 'expires_at' => now()->subDay()]);
        $product = $this->makeProduct($seller);

        $this->get(route('shop.index'))->assertOk()->assertDontSee($product->name);
        $this->get(route('products.show', $product))->assertNotFound();
    }

    public function test_seller_can_submit_a_renewal_payment_for_an_active_plan(): void
    {
        $seller = $this->makeSeller();
        $plan = SubscriptionPlan::create(['name' => 'Yearly', 'duration_days' => 365, 'price' => 9000, 'is_active' => true]);

        $this->actingAs($seller, 'seller')->post(route('seller.subscription.store'), [
            'subscription_plan_id' => $plan->id,
            'payment_method' => 'jazzcash_easypaisa',
            'transaction_reference' => 'JC-12345',
        ])->assertRedirect(route('seller.dashboard'));

        $this->assertDatabaseHas('seller_subscriptions', [
            'seller_id' => $seller->id,
            'subscription_plan_id' => $plan->id,
            'status' => 'pending',
            'transaction_reference' => 'JC-12345',
        ]);
    }

    public function test_admin_activation_starts_subscription_and_reactivates_products(): void
    {
        $seller = $this->makeSeller();
        $plan = SubscriptionPlan::create(['name' => 'Monthly', 'duration_days' => 30, 'price' => 1000, 'is_active' => true]);
        $subscription = SellerSubscription::create([
            'seller_id' => $seller->id, 'subscription_plan_id' => $plan->id, 'price' => 1000,
            'duration_days' => 30, 'status' => 'pending', 'payment_method' => 'bank_transfer',
            'transaction_reference' => 'BANK-123',
        ]);
        $product = $this->makeProduct($seller);
        $product->update(['is_active' => false]);
        $admin = Admin::create(['name' => 'Admin', 'email' => 'sub-admin@example.com', 'password' => Hash::make('AdminPass123')]);

        $this->actingAs($admin, 'admin')->post(route('admin.subscriptions.activate', $subscription))->assertRedirect();

        $this->assertDatabaseHas('seller_subscriptions', ['id' => $subscription->id, 'status' => 'active']);
        $this->assertTrue((bool) $product->fresh()->is_active);
        $this->assertNotNull($subscription->fresh()->expires_at);
    }
}
