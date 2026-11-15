<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class PasswordOtpTest extends TestCase
{
    use RefreshDatabase;

    public function test_forgot_password_screen_can_be_rendered(): void
    {
        $response = $this->get('/forgot-password');

        $response->assertStatus(200);
    }

    public function test_otp_is_sent_for_registered_email(): void
    {
        Mail::fake();

        $user = User::factory()->create();

        $this->post('/forgot-password', ['email' => $user->email]);

        Mail::assertSentTo($user, \App\Mail\PasswordOtp::class);
        $this->assertDatabaseHas('password_otp_tokens', [
            'user_id' => $user->id,
            'email' => $user->email,
            'purpose' => 'password-reset',
            'consumed_at' => null,
        ]);
    }

    public function test_unknown_email_receives_generic_identical_response(): void
    {
        Mail::fake();

        $registered = User::factory()->create();
        $unknown = 'not-registered@example.com';

        $registeredResponse = $this->post('/forgot-password', ['email' => $registered->email]);
        $unknownResponse = $this->post('/forgot-password', ['email' => $unknown]);

        $registeredResponse->assertSessionHas('status');
        $unknownResponse->assertSessionHas('status');
        $this->assertSame(
            $registeredResponse->getSession()->get('status'),
            $unknownResponse->getSession()->get('status')
        );

        $this->assertDatabaseMissing('password_otp_tokens', ['email' => $unknown]);
    }

    public function test_valid_otp_resets_password_and_invalidates_code(): void
    {
        Mail::fake();

        $user = User::factory()->create(['password' => Hash::make('old-password')]);

        $this->post('/forgot-password', ['email' => $user->email]);

        $record = DB::table('password_otp_tokens')->where('email', $user->email)->first();

        $this->post('/reset-password', [
            'email' => $user->email,
            'otp' => '123456',
            'password' => 'new-password',
            'password_confirmation' => 'new-password',
        ])->assertSessionHasNoErrors()->assertRedirect(route('login'));

        $user->refresh();
        $this->assertTrue(Hash::check('new-password', $user->password));
        $this->assertFalse(Hash::check('old-password', $user->password));

        $this->assertDatabaseMissing('password_otp_tokens', [
            'user_id' => $user->id,
            'purpose' => 'password-reset',
            'consumed_at' => null,
        ]);
    }

    public function test_invalid_otp_is_rejected_and_increments_attempts(): void
    {
        Mail::fake();

        $user = User::factory()->create();

        $this->post('/forgot-password', ['email' => $user->email]);

        $record = DB::table('password_otp_tokens')->where('email', $user->email)->first();

        $this->post('/reset-password', [
            'email' => $user->email,
            'otp' => '000000',
            'password' => 'new-password',
            'password_confirmation' => 'new-password',
        ])->assertSessionHasErrors('otp');

        $this->assertDatabaseHas('password_otp_tokens', [
            'id' => $record->id,
            'attempts' => 1,
            'consumed_at' => null,
        ]);
    }
    public function test_expired_otp_is_rejected(): void
    {
        Mail::fake();

        $user = User::factory()->create();

        $this->post('/forgot-password', ['email' => $user->email]);

        DB::table('password_otp_tokens')
            ->where('email', $user->email)
            ->update(['expires_at' => now()->subMinute()]);

        $this->post('/reset-password', [
            'email' => $user->email,
            'otp' => '123456',
            'password' => 'new-password',
            'password_confirmation' => 'new-password',
        ])->assertSessionHasErrors('otp');
    }

    public function test_too_many_attempts_locks_out_otp(): void
    {
        Mail::fake();

        $user = User::factory()->create();

        $this->post('/forgot-password', ['email' => $user->email]);

        $record = DB::table('password_otp_tokens')->where('email', $user->email)->first();

        for ($i = 0; $i < config('password_otp.max_attempts'); $i++) {
            $this->post('/reset-password', [
                'email' => $user->email,
                'otp' => '000000',
                'password' => 'new-password',
                'password_confirmation' => 'new-password',
            ])->assertSessionHasErrors('otp');
        }

        $this->post('/reset-password', [
            'email' => $user->email,
            'otp' => '000000',
            'password' => 'new-password',
            'password_confirmation' => 'new-password',
        ])->assertSessionHasErrors('otp');

        $this->assertDatabaseHas('password_otp_tokens', [
            'id' => $record->id,
            'consumed_at' => not_null(),
        ]);
    }

    public function test_otp_cannot_be_reused_after_successful_reset(): void
    {
        Mail::fake();

        $user = User::factory()->create();

        $this->post('/forgot-password', ['email' => $user->email]);

        $this->post('/reset-password', [
            'email' => $user->email,
            'otp' => '123456',
            'password' => 'new-password',
            'password_confirmation' => 'new-password',
        ])->assertRedirect(route('login'));

        $this->post('/reset-password', [
            'email' => $user->email,
            'otp' => '123456',
            'password' => 'another-password',
            'password_confirmation' => 'another-password',
        ])->assertSessionHasErrors('otp');
    }

    public function test_resend_is_rate_limited_by_email_and_ip(): void
    {
        Mail::fake();

        $user = User::factory()->create();

        $this->post('/forgot-password', ['email' => $user->email]);
        $this->post('/forgot-password', ['email' => $user->email]);
        $this->post('/forgot-password', ['email' => $user->email]);

        $this->post('/forgot-password', ['email' => $user->email]);

        Mail::assertSentCount(\App\Mail\PasswordOtp::class, 3);
    }

    public function test_password_validation_rules_are_enforced(): void
    {
        Mail::fake();

        $user = User::factory()->create();

        $this->post('/forgot-password', ['email' => $user->email]);

        $this->post('/reset-password', [
            'email' => $user->email,
            'otp' => '123456',
            'password' => 'short',
            'password_confirmation' => 'short',
        ])->assertSessionHasErrors('password');
    }

    public function test_password_must_be_confirmed(): void
    {
        Mail::fake();

        $user = User::factory()->create();

        $this->post('/forgot-password', ['email' => $user->email]);

        $this->post('/reset-password', [
            'email' => $user->email,
            'otp' => '123456',
            'password' => 'new-password',
            'password_confirmation' => 'different-password',
        ])->assertSessionHasErrors('password');
    }
}
}