<?php

namespace Tests\Feature\Admin;

use App\Models\Admin;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AdminAuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_guests_are_redirected_to_login_when_visiting_the_dashboard(): void
    {
        $response = $this->get(route('admin.dashboard'));

        $response->assertRedirect(route('admin.login'));
    }

    public function test_an_admin_can_log_in_with_correct_credentials(): void
    {
        $admin = Admin::create([
            'name' => 'Store Admin',
            'email' => 'admin@example.com',
            'password' => Hash::make('AdminPass123'),
        ]);

        $response = $this->post(route('admin.login.attempt'), [
            'email' => 'admin@example.com',
            'password' => 'AdminPass123',
        ]);

        $response->assertRedirect(route('admin.dashboard'));
        $this->assertAuthenticatedAs($admin, 'admin');
    }

    public function test_a_logged_in_customer_cannot_access_the_admin_dashboard(): void
    {
        // A customer ('web' guard) session must never grant admin access —
        // the two guards are completely independent.
        $customer = User::factory()->create();

        $response = $this->actingAs($customer, 'web')->get(route('admin.dashboard'));

        $response->assertRedirect(route('admin.login'));
    }

    public function test_admin_login_fails_with_wrong_password(): void
    {
        Admin::create([
            'name' => 'Store Admin',
            'email' => 'admin@example.com',
            'password' => Hash::make('AdminPass123'),
        ]);

        $response = $this->post(route('admin.login.attempt'), [
            'email' => 'admin@example.com',
            'password' => 'WrongPassword',
        ]);

        $response->assertSessionHasErrors('email');
        $this->assertGuest('admin');
    }

    public function test_admin_dashboard_renders_sidebar_and_navigation(): void
    {
        $admin = Admin::create([
            'name' => 'Store Admin',
            'email' => 'admin@example.com',
            'password' => Hash::make('AdminPass123'),
        ]);

        $response = $this->actingAs($admin, 'admin')->get(route('admin.dashboard'));

        $response->assertStatus(200);
        $response->assertSee('admin-sidebar');
        $response->assertSee(route('admin.categories.index'));
        $response->assertSee(route('admin.reviews.index'));
    }
}
