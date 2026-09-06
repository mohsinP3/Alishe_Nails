<?php

namespace Tests\Feature\Admin;

use App\Models\Admin;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

/**
 * Smoke-test that every admin page actually renders (200) for an
 * authenticated admin. Catches view typos, undefined variables and
 * missing routes before they reach production.
 */
class AdminPagesRenderTest extends TestCase
{
    use RefreshDatabase;

    public function test_every_admin_page_renders_for_an_authenticated_admin(): void
    {
        $admin = Admin::create([
            'name' => 'Store Admin',
            'email' => 'admin@example.com',
            'password' => Hash::make('AdminPass123'),
        ]);

        $pages = [
            route('admin.dashboard'),
            route('admin.products.index'),
            route('admin.products.create'),
            route('admin.categories.index'),
            route('admin.categories.create'),
            route('admin.shipping.index'),
            route('admin.shipping.create'),
            route('admin.orders.index'),
            route('admin.customers.index'),
            route('admin.reviews.index'),
            route('admin.analytics.index'),
            route('admin.settings.index'),
        ];

        foreach ($pages as $url) {
            $this->actingAs($admin, 'admin')->get($url)->assertOk();
        }
    }

    public function test_shipping_form_does_not_leak_error_directives(): void
    {
        $admin = Admin::create([
            'name' => 'Store Admin',
            'email' => 'admin@example.com',
            'password' => Hash::make('AdminPass123'),
        ]);

        $this->actingAs($admin, 'admin')
            ->get(route('admin.shipping.create'))
            ->assertOk()
            ->assertDontSee('@errorEnd');
    }
}