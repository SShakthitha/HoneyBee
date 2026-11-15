<?php

namespace Tests\Feature\Auth;

use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;
use App\Models\Staff;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_registration_screen_can_be_rendered(): void
    {
        $response = $this->get('/register');

        $response->assertStatus(200);
    }

    public function test_new_users_can_register(): void
    {
        $response = $this->post('/register', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'phone' => '0771234567',
            'address' => '123 Main Street',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $this->assertAuthenticated();
        $this->assertDatabaseHas('customers', [
            'email' => 'test@example.com',
            'phone' => '0771234567',
            'address' => '123 Main Street',
        ]);
        $response->assertRedirect(route('customer.dashboard', absolute: false));
    }

    public function test_registration_sends_an_email_verification_notification(): void
    {
        Notification::fake();

        $this->post('/register', [
            'name' => 'Verification Test User',
            'email' => 'verification-test@example.com',
            'phone' => '0771234567',
            'address' => '123 Main Street',
            'password' => 'password',
            'password_confirmation' => 'password',
        ])->assertRedirect(route('customer.dashboard', absolute: false));

        $user = User::where('email', 'verification-test@example.com')->firstOrFail();

        Notification::assertSentTo($user, VerifyEmail::class);
    }

    public function test_registration_cannot_use_an_unverified_staff_identity_to_enter_staff_routes(): void
    {
        Staff::create([
            'full_name' => 'Pending Administrator',
            'role' => 'admin',
            'email' => 'pending-admin@example.com',
            'phone' => '0771234567',
            'hire_date' => now()->toDateString(),
        ]);

        $this->post('/register', [
            'name' => 'Unverified Claimant',
            'email' => 'pending-admin@example.com',
            'phone' => '0771234567',
            'address' => '123 Main Street',
            'password' => 'password',
            'password_confirmation' => 'password',
        ])->assertRedirect(route('customer.dashboard', absolute: false));

        $this->assertAuthenticated();
        $this->get(route('customer.dashboard'))
            ->assertRedirect(route('verification.notice'));
        $this->get(route('admin.dashboard'))
            ->assertRedirect(route('verification.notice'));
    }

    public function test_registration_regenerates_the_session_id(): void
    {
        $this->get('/register');
        $sessionIdBeforeRegistration = app('session')->driver()->getId();

        $this->post('/register', [
            'name' => 'Session Test User',
            'email' => 'session-test@example.com',
            'phone' => '0771234567',
            'address' => '123 Main Street',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $this->assertNotSame($sessionIdBeforeRegistration, app('session')->driver()->getId());
    }

    public function test_new_registration_requires_phone_and_address(): void
    {
        $response = $this->from('/register')->post('/register', [
            'name' => 'Test User',
            'email' => 'missing-contact@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $response->assertRedirect('/register')
            ->assertSessionHasErrors(['phone', 'address']);
        $this->assertDatabaseMissing('customers', ['email' => 'missing-contact@example.com']);
    }
}
