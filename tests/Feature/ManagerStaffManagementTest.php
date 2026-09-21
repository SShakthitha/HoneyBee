<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\Staff;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ManagerStaffManagementTest extends TestCase
{
    use RefreshDatabase;

    private function userWithRole(string $role, string $email): User
    {
        Staff::create(['full_name' => ucfirst($role).' User', 'role' => $role, 'email' => $email, 'phone' => '0771234567', 'hire_date' => now()->toDateString()]);

        return User::factory()->create(['email' => $email]);
    }

    public function test_manager_can_access_staff_workspace_and_manage_staff(): void
    {
        $manager = $this->userWithRole('manager', 'manager@example.com');
        $this->actingAs($manager)->get(route('staff.dashboard'))->assertOk();
        $this->actingAs($manager)->get(route('staff.orders.index'))->assertOk();
        $this->actingAs($manager)->get(route('staff.products.index'))->assertOk();
        $this->actingAs($manager)->get(route('staff.gallery.index'))->assertOk();
        $this->actingAs($manager)->get(route('staff.profile'))->assertOk();
        $this->actingAs($manager)->get(route('staff.staff.index'))->assertOk();

        $payload = ['full_name' => 'New Staff Member', 'role' => 'staff', 'email' => 'new.staff@example.com', 'phone' => '0712345678', 'hire_date' => now()->toDateString()];
        $this->actingAs($manager)->post(route('staff.staff.store'), $payload)->assertRedirect(route('staff.staff.index'));
        $member = Staff::where('email', $payload['email'])->firstOrFail();

        $this->actingAs($manager)->put(route('staff.staff.update', $member->staff_id), array_merge($payload, ['phone' => '0799999999']))->assertRedirect(route('staff.staff.index'));
        $this->assertDatabaseHas('staff', ['staff_id' => $member->staff_id, 'phone' => '0799999999']);
    }

    public function test_manager_cannot_promote_or_delete_themselves(): void
    {
        $manager = $this->userWithRole('manager', 'manager@example.com');
        $record = Staff::where('email', $manager->email)->firstOrFail();
        $payload = ['full_name' => $record->full_name, 'role' => 'admin', 'email' => $record->email, 'phone' => $record->phone, 'hire_date' => $record->hire_date];

        $this->actingAs($manager)->put(route('staff.staff.update', $record->staff_id), $payload)->assertForbidden();
        $this->actingAs($manager)->delete(route('staff.staff.destroy', $record->staff_id))->assertForbidden();
        $this->assertDatabaseHas('staff', ['staff_id' => $record->staff_id, 'role' => 'manager']);
    }

    public function test_regular_staff_and_customers_cannot_access_staff_management(): void
    {
        $staff = $this->userWithRole('staff', 'staff@example.com');
        $this->actingAs($staff)->get(route('staff.staff.index'))->assertForbidden();
        $this->actingAs($staff)->post(route('staff.staff.store'), ['full_name' => 'Blocked', 'role' => 'staff', 'email' => 'blocked@example.com', 'phone' => '0711111111', 'hire_date' => now()->toDateString()])->assertForbidden();
        $this->actingAs($staff)->put(route('staff.staff.update', 999), [])->assertForbidden();
        $this->actingAs($staff)->delete(route('staff.staff.destroy', 999))->assertForbidden();

        $customer = User::factory()->create(['email' => 'customer@example.com']);
        Customer::create(['full_name' => 'Customer', 'email' => $customer->email, 'registered_date' => now()]);
        $this->actingAs($customer)->get(route('staff.dashboard'))->assertForbidden();
        $this->actingAs($customer)->get(route('staff.staff.index'))->assertForbidden();
    }

    public function test_admin_retains_existing_staff_management_access(): void
    {
        $admin = $this->userWithRole('admin', 'admin@example.com');
        $this->actingAs($admin)->get(route('admin.staff'))->assertOk();
    }
}
