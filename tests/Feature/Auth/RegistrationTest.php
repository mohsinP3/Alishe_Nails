<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_a_customer_can_register(): void
    {
        $response = $this->post('/register', [
            'name' => 'Sana Malik',
            'email' => 'sana@example.com',
            'phone' => '+92 300 1234567',
            'password' => 'Password123',
            'password_confirmation' => 'Password123',
        ]);

        $response->assertRedirect(route('home'));
        $this->assertDatabaseHas('users', ['email' => 'sana@example.com']);
        $this->assertAuthenticated('web');

        $user = User::where('email', 'sana@example.com')->first();
        $this->assertNotEquals('Password123', $user->password, 'Password must be hashed, never stored in plain text.');
    }

    public function test_registration_requires_a_unique_email(): void
    {
        User::factory()->create(['email' => 'taken@example.com']);

        $response = $this->post('/register', [
            'name' => 'Someone',
            'email' => 'taken@example.com',
            'password' => 'Password123',
            'password_confirmation' => 'Password123',
        ]);

        $response->assertSessionHasErrors('email');
        $this->assertGuest('web');
    }

    public function test_registration_requires_matching_password_confirmation(): void
    {
        $response = $this->post('/register', [
            'name' => 'Someone',
            'email' => 'new@example.com',
            'password' => 'Password123',
            'password_confirmation' => 'Different123',
        ]);

        $response->assertSessionHasErrors('password');
    }
}
