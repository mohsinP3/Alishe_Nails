<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrderAuthorizationTest extends TestCase
{
    use RefreshDatabase;

    public function test_a_customer_cannot_view_another_customers_order(): void
    {
        $owner = User::factory()->create();
        $intruder = User::factory()->create();

        $order = Order::create([
            'user_id' => $owner->id,
            'first_name' => 'Owner',
            'last_name' => 'One',
            'email' => 'owner@example.com',
            'phone' => '0300',
            'address' => 'Somewhere',
            'city' => 'Karachi',
            'payment_method' => 'cod',
            'subtotal' => 1000,
            'shipping' => 0,
            'total' => 1000,
        ]);

        $response = $this->actingAs($intruder, 'web')->get(route('account.orders.show', $order));

        $response->assertForbidden();
    }

    public function test_a_customer_can_view_their_own_order(): void
    {
        $owner = User::factory()->create();

        $order = Order::create([
            'user_id' => $owner->id,
            'first_name' => 'Owner',
            'last_name' => 'One',
            'email' => 'owner@example.com',
            'phone' => '0300',
            'address' => 'Somewhere',
            'city' => 'Karachi',
            'payment_method' => 'cod',
            'subtotal' => 1000,
            'shipping' => 0,
            'total' => 1000,
        ]);

        $response = $this->actingAs($owner, 'web')->get(route('account.orders.show', $order));

        $response->assertOk();
    }

    public function test_a_guest_order_is_not_accessible_through_the_customer_orders_route(): void
    {
        $customer = User::factory()->create();

        $guestOrder = Order::create([
            'user_id' => null,
            'first_name' => 'Guest',
            'last_name' => 'Buyer',
            'email' => 'guest@example.com',
            'phone' => '0300',
            'address' => 'Somewhere',
            'city' => 'Karachi',
            'payment_method' => 'cod',
            'subtotal' => 1000,
            'shipping' => 0,
            'total' => 1000,
        ]);

        $response = $this->actingAs($customer, 'web')->get(route('account.orders.show', $guestOrder));

        $response->assertForbidden();
    }

    public function test_guest_visitors_cannot_view_my_orders_at_all(): void
    {
        $response = $this->get(route('account.orders'));

        $response->assertRedirect(route('login'));
    }
}
