<?php

namespace Tests\Feature\Auth;

use App\Models\Customer;
use App\Models\Staff;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PasswordConfirmationTest extends TestCase
{
    use RefreshDatabase;

    public function test_confirm_password_screen_can_be_rendered(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/confirm-password');

        $response->assertStatus(200);
    }

    public function test_password_can_be_confirmed(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post('/confirm-password', [
            'password' => 'password',
        ]);

        $response->assertRedirect();
        $response->assertSessionHasNoErrors();
    }

    public function test_password_confirmation_falls_back_to_each_role_dashboard(): void
    {
        $accounts = [
            ['admin', 'admin-confirm@example.com', 'admin.dashboard'],
            ['manager', 'manager-confirm@example.com', 'manager.dashboard'],
            ['staff', 'staff-confirm@example.com', 'staff.dashboard'],
            ['customer', 'customer-confirm@example.com', 'customer.dashboard'],
        ];

        foreach ($accounts as [$role, $email, $dashboardRoute]) {
            $user = User::factory()->create(['email' => $email]);

            if ($role === 'customer') {
                Customer::create([
                    'full_name' => $user->name,
                    'email' => $user->email,
                    'registered_date' => now(),
                ]);
            } else {
                Staff::create([
                    'full_name' => $user->name,
                    'role' => $role,
                    'email' => $user->email,
                    'phone' => '0771234567',
                    'hire_date' => now()->toDateString(),
                ]);
            }

            $this->actingAs($user)
                ->post('/confirm-password', ['password' => 'password'])
                ->assertRedirect(route($dashboardRoute, absolute: false));
        }
    }

    public function test_password_is_not_confirmed_with_invalid_password(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post('/confirm-password', [
            'password' => 'wrong-password',
        ]);

        $response->assertSessionHasErrors();
    }
}
