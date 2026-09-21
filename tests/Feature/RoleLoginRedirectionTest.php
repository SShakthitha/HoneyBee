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

    private const PASSWORD = 'Shakthitha#25';

    private function makeAccount(string $email, string $name, ?string $role = null): User
    {
        $user = User::factory()->create(['name' => $name, 'email' => $email, 'password' => Hash::make(self::PASSWORD)]);

        if ($role) {
            Staff::create(['full_name' => $name, 'role' => $role, 'email' => $email, 'phone' => '0771234567', 'hire_date' => now()->toDateString()]);
        } else {
            Customer::create(['full_name' => $name, 'email' => $email, 'registered_date' => now()]);
        }

        return $user;
    }

    public function test_admin_credentials_redirect_to_admin_dashboard_and_keep_admin_access(): void
    {
        $this->makeAccount('sivarajahshakthitha25@gmail.com', 'HoneyBee Admin', 'admin');

        $this->post(route('login'), ['email' => 'sivarajahshakthitha25@gmail.com', 'password' => self::PASSWORD])
            ->assertRedirect(route('admin.dashboard'));

        $this->assertAuthenticated();
        $this->get(route('admin.dashboard'))->assertOk();
    }

    public function test_customer_credentials_redirect_to_customer_dashboard_and_cannot_access_admin_or_staff(): void
    {
        $this->makeAccount('shakthitha25@gmail.com', 'HoneyBee Customer');

        $this->post(route('login'), ['email' => 'shakthitha25@gmail.com', 'password' => self::PASSWORD])
            ->assertRedirect(route('customer.dashboard'));

        $this->get(route('customer.dashboard'))->assertOk();
        $this->get(route('admin.dashboard'))->assertForbidden();
        $this->get(route('staff.dashboard'))->assertForbidden();
    }

    public function test_manager_credentials_redirect_to_staff_dashboard_without_admin_management_access(): void
    {
        $this->makeAccount('shakthithasivarajah2002@gmail.com', 'HoneyBee Manager', 'manager');

        $this->post(route('login'), ['email' => 'shakthithasivarajah2002@gmail.com', 'password' => self::PASSWORD])
            ->assertRedirect(route('staff.dashboard'));

        $this->get(route('staff.dashboard'))->assertOk();
        $this->get(route('admin.customers.index'))->assertForbidden();
        $this->get(route('admin.staff'))->assertForbidden();
    }

    public function test_logout_still_invalidates_the_session(): void
    {
        $user = $this->makeAccount('shakthitha25@gmail.com', 'HoneyBee Customer');

        $this->actingAs($user)->post(route('logout'))->assertRedirect('/');

        $this->assertGuest();
    }
}
