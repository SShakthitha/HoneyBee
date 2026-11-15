<?php

namespace Tests\Feature\Auth;

use App\Models\Customer;
use App\Models\Staff;
use App\Models\User;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Auth\Events\Verified;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\URL;
use Tests\TestCase;

class EmailVerificationTest extends TestCase
{
    use RefreshDatabase;

    public function test_email_verification_screen_can_be_rendered(): void
    {
        $user = User::factory()->unverified()->create();
        $this->createCustomerRecord($user);

        $response = $this->actingAs($user)->get('/verify-email');

        $response->assertStatus(200);
    }

    public function test_email_can_be_verified(): void
    {
        $user = User::factory()->unverified()->create();
        $this->createCustomerRecord($user);

        Event::fake();

        $verificationUrl = URL::temporarySignedRoute(
            'verification.verify',
            now()->addMinutes(60),
            ['id' => $user->id, 'hash' => sha1($user->email)]
        );

        $response = $this->actingAs($user)->get($verificationUrl);

        Event::assertDispatched(Verified::class);
        $this->assertTrue($user->fresh()->hasVerifiedEmail());
        $response->assertRedirect(route('customer.dashboard', absolute: false).'?verified=1');
    }

    public function test_email_is_not_verified_with_invalid_hash(): void
    {
        $user = User::factory()->unverified()->create();
        $this->createCustomerRecord($user);

        $verificationUrl = URL::temporarySignedRoute(
            'verification.verify',
            now()->addMinutes(60),
            ['id' => $user->id, 'hash' => sha1('wrong-email')]
        );

        $this->actingAs($user)->get($verificationUrl);

        $this->assertFalse($user->fresh()->hasVerifiedEmail());
    }

    public function test_expired_and_another_users_verification_links_are_rejected(): void
    {
        $user = User::factory()->unverified()->create();
        $otherUser = User::factory()->unverified()->create();
        $this->createCustomerRecord($user);
        $this->createCustomerRecord($otherUser);

        $expiredUrl = URL::temporarySignedRoute(
            'verification.verify',
            now()->subMinute(),
            ['id' => $user->id, 'hash' => sha1($user->email)]
        );

        $this->actingAs($user)->get($expiredUrl)->assertForbidden();
        $this->assertFalse($user->fresh()->hasVerifiedEmail());

        $otherUsersUrl = URL::temporarySignedRoute(
            'verification.verify',
            now()->addMinutes(60),
            ['id' => $otherUser->id, 'hash' => sha1($otherUser->email)]
        );

        $this->actingAs($user)->get($otherUsersUrl)->assertForbidden();
        $this->assertFalse($otherUser->fresh()->hasVerifiedEmail());
    }

    public function test_verified_staff_account_can_access_its_role_dashboard(): void
    {
        $user = User::factory()->unverified()->create();
        Staff::create([
            'full_name' => $user->name,
            'role' => 'admin',
            'email' => $user->email,
            'phone' => '0771234567',
            'hire_date' => now()->toDateString(),
        ]);

        $this->actingAs($user)
            ->get(route('admin.dashboard'))
            ->assertRedirect(route('verification.notice'));

        $verificationUrl = URL::temporarySignedRoute(
            'verification.verify',
            now()->addMinutes(60),
            ['id' => $user->id, 'hash' => sha1($user->email)]
        );

        $this->get($verificationUrl)->assertRedirect(route('admin.dashboard', absolute: false).'?verified=1');
        $this->assertTrue($user->fresh()->hasVerifiedEmail());
        $this->get(route('admin.dashboard'))->assertOk();
    }

    public function test_verification_notifications_and_signed_dashboard_redirects_work_for_each_role(): void
    {
        Notification::fake();

        $accounts = [
            ['admin', 'verification-admin@example.com', 'admin.dashboard'],
            ['manager', 'verification-manager@example.com', 'manager.dashboard'],
            ['staff', 'verification-staff@example.com', 'staff.dashboard'],
            ['customer', 'verification-customer@example.com', 'customer.dashboard'],
        ];

        foreach ($accounts as [$role, $email, $dashboardRoute]) {
            $user = User::factory()->unverified()->create([
                'name' => 'Verification '.$role,
                'email' => $email,
            ]);

            if ($role === 'customer') {
                $this->createCustomerRecord($user);
            } else {
                Staff::create([
                    'full_name' => $user->name,
                    'role' => $role,
                    'email' => $user->email,
                    'phone' => '0771234567',
                    'hire_date' => now()->toDateString(),
                ]);
            }

            $user->sendEmailVerificationNotification();
            Notification::assertSentTo($user, VerifyEmail::class);
            $this->assertSame($email, $user->routeNotificationFor('mail'));

            $notification = Notification::sent($user, VerifyEmail::class)->first();
            $verificationUrl = $notification->toMail($user)->actionUrl;
            $segments = explode('/', trim(parse_url($verificationUrl, PHP_URL_PATH), '/'));
            $query = [];
            parse_str((string) parse_url($verificationUrl, PHP_URL_QUERY), $query);

            $this->assertSame('verify-email', $segments[count($segments) - 3]);
            $this->assertSame((string) $user->getKey(), $segments[count($segments) - 2]);
            $this->assertSame(sha1($email), $segments[count($segments) - 1]);
            $this->assertArrayHasKey('expires', $query);
            $this->assertGreaterThan(now()->timestamp, (int) $query['expires']);
            $this->assertLessThanOrEqual(now()->addMinutes(60)->timestamp, (int) $query['expires']);
            $this->assertTrue(URL::hasValidSignature(Request::create($verificationUrl)));

            $this->actingAs($user)
                ->get(route($dashboardRoute))
                ->assertRedirect(route('verification.notice'));

            $this->get($verificationUrl)
                ->assertRedirect(route($dashboardRoute, absolute: false).'?verified=1');
            $this->assertTrue($user->fresh()->hasVerifiedEmail());
            $this->get(route($dashboardRoute))->assertOk();
        }
    }

    private function createCustomerRecord(User $user): void
    {
        Customer::create([
            'full_name' => $user->name,
            'email' => $user->email,
            'registered_date' => now(),
        ]);
    }
}
