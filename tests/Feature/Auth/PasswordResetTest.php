<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Auth\Passwords\PasswordBrokerManager;
use Illuminate\Concurrency\ConcurrencyManager;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class PasswordResetTest extends TestCase
{
    use RefreshDatabase;

    public function test_the_password_broker_is_bound_in_the_container(): void
    {
        // Regression guard: Illuminate\Auth\Passwords\PasswordResetServiceProvider
        // was accidentally removed from config/app.php. Without it, "Forgot
        // Password" crashed with "Target class [auth.password] does not exist."
        // while the rest of the suite stayed green. This fails the moment the
        // provider is dropped again.
        $this->assertInstanceOf(
            PasswordBrokerManager::class,
            $this->app->make('auth.password'),
        );
    }

    public function test_the_concurrency_manager_is_bound_in_the_container(): void
    {
        // Regression guard for the companion Laravel 11 default provider that
        // was dropped alongside the password broker (deferrable, so it only
        // surfaces when Concurrency is first resolved).
        $this->assertInstanceOf(
            ConcurrencyManager::class,
            $this->app->make(ConcurrencyManager::class),
        );
    }

    public function test_the_forgot_password_page_can_be_rendered(): void
    {
        $response = $this->get('/forgot-password');

        $response->assertOk();
    }

    public function test_a_customer_can_request_a_password_reset_link(): void
    {
        Notification::fake();

        $user = User::factory()->create();

        $response = $this->post('/forgot-password', ['email' => $user->email]);

        $response->assertRedirect();
        $response->assertSessionHas('success', 'A password reset link has been sent to your email.');

        Notification::assertSentTo($user, ResetPassword::class);
        $this->assertDatabaseHas('password_reset_tokens', ['email' => $user->email]);
    }

    public function test_no_reset_link_is_sent_for_an_unknown_email(): void
    {
        Notification::fake();

        $response = $this->post('/forgot-password', ['email' => 'nobody@example.com']);

        // Laravel's broker refuses unknown addresses (INVALID_USER), so the
        // controller redirects back with a validation error and — importantly —
        // no token row is created and no notification is dispatched.
        $response->assertRedirect();
        $response->assertSessionHasErrors('email');

        Notification::assertNothingSent();
        $this->assertDatabaseCount('password_reset_tokens', 0);
    }

    public function test_the_reset_password_page_can_be_rendered_with_a_token(): void
    {
        $response = $this->get('/reset-password/some-token?email=someone@example.com');

        $response->assertOk();
        $response->assertViewHas('token', 'some-token');
        $response->assertViewHas('email', 'someone@example.com');
    }

    public function test_a_customer_can_reset_their_password_with_a_valid_token(): void
    {
        $user = User::factory()->create(['password' => Hash::make('OldPassword123')]);
        $token = $this->requestResetTokenFor($user);

        $response = $this->post('/reset-password', [
            'token' => $token,
            'email' => $user->email,
            'password' => 'NewPassword123',
            'password_confirmation' => 'NewPassword123',
        ]);

        $response->assertRedirect(route('login'));
        $response->assertSessionHas('success', 'Your password has been reset. Please log in.');

        // The single-use token must be consumed after a successful reset.
        $this->assertDatabaseMissing('password_reset_tokens', ['email' => $user->email]);

        $fresh = $user->fresh();
        $this->assertTrue(Hash::check('NewPassword123', $fresh->password));
        $this->assertFalse(Hash::check('OldPassword123', $fresh->password));
    }

    public function test_the_new_password_actually_logs_the_customer_in(): void
    {
        $user = User::factory()->create(['password' => Hash::make('OldPassword123')]);
        $token = $this->requestResetTokenFor($user);

        $this->post('/reset-password', [
            'token' => $token,
            'email' => $user->email,
            'password' => 'NewPassword123',
            'password_confirmation' => 'NewPassword123',
        ]);

        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'NewPassword123',
        ]);

        $response->assertRedirect(route('home'));
        $this->assertAuthenticatedAs($user, 'web');
    }

    public function test_the_password_cannot_be_reset_with_an_invalid_token(): void
    {
        $user = User::factory()->create(['password' => Hash::make('OldPassword123')]);

        $response = $this->from('/reset-password/bad-token')->post('/reset-password', [
            'token' => 'bad-token',
            'email' => $user->email,
            'password' => 'NewPassword123',
            'password_confirmation' => 'NewPassword123',
        ]);

        $response->assertRedirect('/reset-password/bad-token');
        $response->assertSessionHasErrors('email');
        $this->assertTrue(Hash::check('OldPassword123', $user->fresh()->password));
    }

    public function test_reset_password_requires_a_strong_confirmed_password(): void
    {
        $user = User::factory()->create();
        $token = $this->requestResetTokenFor($user);

        $response = $this->post('/reset-password', [
            'token' => $token,
            'email' => $user->email,
            // Fails Password::min(8)->letters()->numbers().
            'password' => 'short',
            'password_confirmation' => 'short',
        ]);

        $response->assertSessionHasErrors('password');

        // A failed attempt must not consume the token — the user can retry.
        $this->assertDatabaseHas('password_reset_tokens', ['email' => $user->email]);
    }

    public function test_forgot_password_requires_a_valid_email(): void
    {
        $response = $this->post('/forgot-password', ['email' => 'not-an-email']);

        $response->assertSessionHasErrors('email');
    }

    /**
     * Request a reset link for the given user and capture the token that
     * would be embedded in the emailed link.
     */
    private function requestResetTokenFor(User $user): string
    {
        Notification::fake();

        $this->post('/forgot-password', ['email' => $user->email]);

        $token = null;

        Notification::assertSentTo(
            $user,
            ResetPassword::class,
            function (ResetPassword $notification) use (&$token): bool {
                $token = $notification->token;

                return true;
            },
        );

        return $token;
    }
}
