<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class PasswordResetTest extends TestCase
{
    use RefreshDatabase;

    public function test_reset_password_link_screen_can_be_rendered(): void
    {
        $response = $this->get('/forgot-password');

        $response->assertStatus(200);
    }

    public function test_reset_password_link_can_be_requested(): void
    {
        Notification::fake();

        $user = User::factory()->create();

        $this->post('/forgot-password', ['email' => $user->email]);

        Notification::assertSentTo($user, ResetPassword::class);
    }

    public function test_reset_password_screen_can_be_rendered(): void
    {
        Notification::fake();

        $user = User::factory()->create();

        $this->post('/forgot-password', ['email' => $user->email]);

        Notification::assertSentTo($user, ResetPassword::class, function ($notification) {
            $response = $this->get('/reset-password/'.$notification->token);

            $response->assertStatus(200);

            return true;
        });
    }

    public function test_password_can_be_reset_with_valid_token(): void
    {
        Notification::fake();

        $user = User::factory()->create();
        $oldPasswordHash = $user->password;

        $this->post('/forgot-password', ['email' => $user->email]);

        Notification::assertSentTo($user, ResetPassword::class, function ($notification) use ($user) {
            $response = $this->post('/reset-password', [
                'token' => $notification->token,
                'email' => $user->email,
                'password' => 'new-password',
                'password_confirmation' => 'new-password',
            ]);

            $response
                ->assertSessionHasNoErrors()
                ->assertRedirect(route('login'));

            return true;
        });

        $user->refresh();

        $this->assertTrue(Hash::check('password', $oldPasswordHash));
        $this->assertFalse(Hash::check('password', $user->password));
        $this->assertTrue(Hash::check('new-password', $user->password));
        $this->assertDatabaseMissing('password_reset_tokens', ['email' => $user->email]);
    }

    public function test_old_password_no_longer_works_and_new_password_can_log_in(): void
    {
        Notification::fake();

        $user = User::factory()->create();
        $token = $this->requestResetToken($user);

        $this->post('/reset-password', [
            'token' => $token,
            'email' => $user->email,
            'password' => 'new-password',
            'password_confirmation' => 'new-password',
        ])->assertRedirect(route('login'));

        $this->post('/login', [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $this->assertGuest();

        $this->post('/login', [
            'email' => $user->email,
            'password' => 'new-password',
        ])->assertRedirect(route('customer.dashboard', absolute: false));

        $this->assertAuthenticatedAs($user);
    }

    public function test_reset_token_cannot_be_reused(): void
    {
        Notification::fake();

        $user = User::factory()->create();
        $token = $this->requestResetToken($user);
        $payload = [
            'token' => $token,
            'email' => $user->email,
            'password' => 'new-password',
            'password_confirmation' => 'new-password',
        ];

        $this->post('/reset-password', $payload)->assertRedirect(route('login'));
        $this->post('/reset-password', $payload)->assertSessionHasErrors('email');
    }

    public function test_invalid_or_expired_reset_token_is_rejected(): void
    {
        $user = User::factory()->create();
        $payload = [
            'email' => $user->email,
            'password' => 'new-password',
            'password_confirmation' => 'new-password',
        ];

        $this->post('/reset-password', $payload + ['token' => 'invalid-token'])
            ->assertSessionHasErrors('email');

        DB::table('password_reset_tokens')->insert([
            'email' => $user->email,
            'token' => Hash::make('expired-token'),
            'created_at' => now()->subMinutes(config('auth.passwords.users.expire') + 1),
        ]);

        $this->post('/reset-password', $payload + ['token' => 'expired-token'])
            ->assertSessionHasErrors('email');
    }

    public function test_password_confirmation_must_match(): void
    {
        Notification::fake();

        $user = User::factory()->create();
        $token = $this->requestResetToken($user);

        $this->post('/reset-password', [
            'token' => $token,
            'email' => $user->email,
            'password' => 'new-password',
            'password_confirmation' => 'different-password',
        ])->assertSessionHasErrors('password');

        $this->assertTrue(Hash::check('password', $user->fresh()->password));
    }

    private function requestResetToken(User $user): string
    {
        $this->post('/forgot-password', ['email' => $user->email]);

        $token = null;

        Notification::assertSentTo($user, ResetPassword::class, function (ResetPassword $notification) use (&$token) {
            $token = $notification->token;

            return true;
        });

        return $token;
    }
}
