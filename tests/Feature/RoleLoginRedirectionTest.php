<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\Staff;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class RoleLoginRedirectionTest extends TestCase
{
    use RefreshDatabase;

    private const FALLBACK_PASSWORD = 'role-test-password';

    private function passwordFor(?string $role): string
    {
        $key = match ($role) {
            'admin' => 'HONEYBEE_ADMIN_PASSWORD',
            'staff' => 'HONEYBEE_STAFF_PASSWORD',
            'manager' => 'HONEYBEE_MANAGER_PASSWORD',
            default => 'HONEYBEE_CUSTOMER_PASSWORD',
        };

        return (string) env($key, self::FALLBACK_PASSWORD);
    }

    private function makeAccount(string $email, string $name, ?string $role = null): User
    {
        $user = User::factory()->create([
            'name' => $name,
            'email' => $email,
            'password' => Hash::make($this->passwordFor($role)),
            'email_verified_at' => now(),
        ]);

        if ($role !== null) {
            Staff::create([
                'full_name' => $name,
                'role' => $role,
                'email' => $email,
                'phone' => '0771234567',
                'hire_date' => now()->toDateString(),
            ]);
        } else {
            Customer::create([
                'full_name' => $name,
                'email' => $email,
                'registered_date' => now(),
            ]);
        }

        return $user;
    }

    public function test_admin_login_redirects_to_admin_dashboard_and_rejects_other_role_dashboards(): void
    {
        $this->makeAccount('sivarajahshakthitha25@gmail.com', 'HoneyBee Admin', 'admin');

        $this->post(route('login'), ['email' => 'sivarajahshakthitha25@gmail.com', 'password' => $this->passwordFor('admin')])
            ->assertRedirect(route('admin.dashboard'));

        $this->assertAuthenticated();
        $this->get(route('admin.dashboard'))
            ->assertOk()
            ->assertHeader('Cache-Control', 'max-age=0, must-revalidate, no-store, private')
            ->assertSee('method="POST" action="'.route('logout').'"', false)
            ->assertSee('fa-right-from-bracket', false);
        $this->get(route('staff.dashboard'))->assertForbidden();
        $this->get(route('customer.dashboard'))->assertForbidden();
        $this->get(route('orders.index'))->assertForbidden();
    }

    public function test_customer_login_redirects_to_customer_dashboard_and_rejects_admin_and_staff_routes(): void
    {
        $this->makeAccount('shakthitha25@gmail.com', 'HoneyBee Customer');

        $this->post(route('login'), ['email' => 'shakthitha25@gmail.com', 'password' => $this->passwordFor(null)])
            ->assertRedirect(route('customer.dashboard'));

        $this->assertAuthenticated();
        $this->get(route('customer.dashboard'))->assertOk();
        $this->get(route('orders.index'))->assertOk();
        $this->get(route('admin.dashboard'))->assertForbidden();
        $this->get(route('staff.dashboard'))->assertForbidden();
    }

    public function test_staff_login_redirects_to_staff_dashboard_and_rejects_admin_and_customer_routes(): void
    {
        $this->makeAccount('shakthithasivarajah2002@gmail.com', 'HoneyBee Staff', 'staff');

        $this->post(route('login'), ['email' => 'shakthithasivarajah2002@gmail.com', 'password' => $this->passwordFor('staff')])
            ->assertRedirect(route('staff.dashboard'));

        $this->assertAuthenticated();
        $this->get(route('staff.dashboard'))->assertOk();
        $this->get(route('admin.dashboard'))->assertForbidden();
        $this->get(route('customer.dashboard'))->assertForbidden();
        $this->get(route('orders.index'))->assertForbidden();
        $this->get(route('customer.profile.edit'))->assertForbidden();
    }

    public function test_manager_login_redirects_to_manager_dashboard_and_cannot_access_admin_routes(): void
    {
        $this->makeAccount('manager@example.com', 'HoneyBee Manager', 'manager');

        $this->post(route('login'), ['email' => 'manager@example.com', 'password' => $this->passwordFor('manager')])
            ->assertRedirect(route('manager.dashboard'));

        $this->assertAuthenticated();
        $this->get(route('manager.dashboard'))->assertOk();
        $this->get(route('staff.staff.index'))->assertOk();
        $this->get(route('admin.dashboard'))->assertForbidden();
        $this->get(route('admin.settings.whatsapp.edit'))->assertForbidden();
    }

    public function test_invalid_password_and_invalid_email_are_rejected(): void
    {
        $this->makeAccount('shakthitha25@gmail.com', 'HoneyBee Customer');

        $this->from(route('login'))
            ->post(route('login'), ['email' => 'shakthitha25@gmail.com', 'password' => 'incorrect-test-password'])
            ->assertRedirect(route('login'))
            ->assertSessionHasErrors('email');
        $this->assertGuest();

        $this->from(route('login'))
            ->post(route('login'), ['email' => 'not-an-email', 'password' => self::FALLBACK_PASSWORD])
            ->assertRedirect(route('login'))
            ->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    public function test_login_logout_and_login_again_work_with_a_persistent_authenticated_session(): void
    {
        $this->makeAccount('shakthitha25@gmail.com', 'HoneyBee Customer');

        $this->post(route('login'), ['email' => 'shakthitha25@gmail.com', 'password' => $this->passwordFor(null)])
            ->assertRedirect(route('customer.dashboard'));
        $this->get(route('customer.dashboard'))->assertOk();

        $this->post(route('logout'))->assertRedirect(route('login'));
        $this->assertGuest();
        $this->get(route('customer.dashboard'))->assertRedirect(route('login'));

        $this->post(route('login'), ['email' => 'shakthitha25@gmail.com', 'password' => $this->passwordFor(null)])
            ->assertRedirect(route('customer.dashboard'));
        $this->assertAuthenticated();
    }

    public function test_admin_logout_prevents_further_access_to_protected_pages(): void
    {
        $this->makeAccount('sivarajahshakthitha25@gmail.com', 'HoneyBee Admin', 'admin');

        $this->post(route('login'), [
            'email' => 'sivarajahshakthitha25@gmail.com',
            'password' => $this->passwordFor('admin'),
        ])->assertRedirect(route('admin.dashboard'));

        $this->get(route('admin.dashboard'))
            ->assertOk()
            ->assertHeader('Cache-Control', 'max-age=0, must-revalidate, no-store, private');

        $this->post(route('logout'))->assertRedirect(route('login'));
        $this->assertGuest();
        $this->get(route('admin.dashboard'))->assertRedirect(route('login'));
    }

    public function test_unassigned_user_cannot_keep_an_authenticated_session(): void
    {
        $user = User::factory()->create(['password' => Hash::make(self::FALLBACK_PASSWORD)]);

        $this->from(route('login'))
            ->post(route('login'), ['email' => $user->email, 'password' => self::FALLBACK_PASSWORD])
            ->assertRedirect(route('login'))
            ->assertSessionHasErrors('email');

        $this->assertGuest();
    }
}
