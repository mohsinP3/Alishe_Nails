<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class LoginTest extends TestCase
{
    use RefreshDatabase;

    public function test_a_customer_can_log_in_with_correct_credentials(): void
    {
        $user = User::factory()->create(['password' => Hash::make('CorrectPass123')]);

        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'CorrectPass123',
        ]);

        $response->assertRedirect(route('home'));
        $this->assertAuthenticatedAs($user, 'web');
    }

    public function test_login_fails_with_wrong_password(): void
    {
        $user = User::factory()->create(['password' => Hash::make('CorrectPass123')]);

        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'WrongPassword',
        ]);

        $response->assertSessionHasErrors('email');
        $this->assertGuest('web');
    }

    public function test_a_logged_in_customer_can_log_out(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user, 'web');

        $response = $this->post('/logout');

        $response->assertRedirect(route('home'));
        $this->assertGuest('web');
    }
}
