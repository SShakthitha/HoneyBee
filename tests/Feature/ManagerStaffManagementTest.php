<?php

namespace Tests\Feature;

use App\Models\Business;
use App\Models\Customer;
use App\Models\Handles;
use App\Models\Service;
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
        $this->actingAs($manager)->get(route('manager.dashboard'))->assertOk();
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
        $this->actingAs($staff)->get(route('manager.dashboard'))->assertForbidden();
        $this->actingAs($staff)->post(route('staff.staff.store'), ['full_name' => 'Blocked', 'role' => 'staff', 'email' => 'blocked@example.com', 'phone' => '0711111111', 'hire_date' => now()->toDateString()])->assertForbidden();
        $this->actingAs($staff)->put(route('staff.staff.update', 999), [])->assertForbidden();
        $this->actingAs($staff)->delete(route('staff.staff.destroy', 999))->assertForbidden();

        $customer = User::factory()->create(['email' => 'customer@example.com']);
        Customer::create(['full_name' => 'Customer', 'email' => $customer->email, 'registered_date' => now()]);
        $this->actingAs($customer)->get(route('staff.dashboard'))->assertForbidden();
        $this->actingAs($customer)->get(route('manager.dashboard'))->assertForbidden();
        $this->actingAs($customer)->get(route('staff.staff.index'))->assertForbidden();
    }

    public function test_admin_retains_existing_staff_management_access(): void
    {
        $admin = $this->userWithRole('admin', 'admin@example.com');
        $this->actingAs($admin)->get(route('admin.staff'))->assertOk();
        $this->actingAs($admin)->get(route('admin.dashboard'))->assertOk();
        $this->actingAs($admin)->get(route('admin.settings.whatsapp.edit'))->assertOk();
    }

    public function test_manager_cannot_view_or_manage_other_managers_or_administrators(): void
    {
        $manager = $this->userWithRole('manager', 'manager@example.com');
        $otherManager = $this->userWithRole('manager', 'another.manager@example.com');
        $admin = $this->userWithRole('admin', 'admin.target@example.com');

        $this->actingAs($manager)->get(route('staff.staff.index'))
            ->assertOk()
            ->assertDontSee($otherManager->email)
            ->assertDontSee($admin->email);
        $this->actingAs($manager)->get(route('staff.staff.edit', Staff::where('email', $otherManager->email)->value('staff_id')))->assertForbidden();
        $this->actingAs($manager)->get(route('staff.staff.edit', Staff::where('email', $admin->email)->value('staff_id')))->assertForbidden();
        $this->actingAs($manager)->put(route('staff.staff.update', Staff::where('email', $otherManager->email)->value('staff_id')), [])->assertForbidden();
        $this->actingAs($manager)->delete(route('staff.staff.destroy', Staff::where('email', $otherManager->email)->value('staff_id')))->assertForbidden();
        $this->actingAs($manager)->put(route('staff.staff.update', Staff::where('email', $admin->email)->value('staff_id')), [])->assertForbidden();
        $this->actingAs($manager)->delete(route('staff.staff.destroy', Staff::where('email', $admin->email)->value('staff_id')))->assertForbidden();
    }

    public function test_manager_cannot_create_or_promote_another_manager(): void
    {
        $manager = $this->userWithRole('manager', 'manager@example.com');
        $payload = ['full_name' => 'Attempted Manager', 'role' => 'manager', 'email' => 'attempted.manager@example.com', 'phone' => '0712345678', 'hire_date' => now()->toDateString()];

        $this->actingAs($manager)->post(route('staff.staff.store'), $payload)->assertSessionHasErrors('role');
        $this->assertDatabaseMissing('staff', ['email' => $payload['email']]);

        $member = $this->userWithRole('staff', 'existing.staff@example.com');
        $staffId = Staff::where('email', $member->email)->value('staff_id');
        $this->actingAs($manager)->put(route('staff.staff.update', $staffId), array_merge($payload, ['email' => $member->email]))
            ->assertSessionHasErrors('role');
        $this->assertDatabaseHas('staff', ['staff_id' => $staffId, 'role' => 'staff']);
    }

    public function test_manager_cannot_reassign_customer_or_login_emails_to_staff(): void
    {
        $manager = $this->userWithRole('manager', 'manager@example.com');
        $member = $this->userWithRole('staff', 'existing.staff@example.com');
        $customer = User::factory()->create(['email' => 'existing.customer@example.com']);
        Customer::create(['full_name' => 'Customer', 'email' => $customer->email, 'registered_date' => now()]);
        $staffId = Staff::where('email', $member->email)->value('staff_id');

        $newStaff = ['full_name' => 'Attempted Staff', 'role' => 'staff', 'email' => $customer->email, 'phone' => '0712345678', 'hire_date' => now()->toDateString()];
        $this->actingAs($manager)->post(route('staff.staff.store'), $newStaff)->assertSessionHasErrors('email');
        $this->assertDatabaseMissing('staff', ['email' => $customer->email]);

        $this->actingAs($manager)->put(route('staff.staff.update', $staffId), array_merge($newStaff, ['email' => $customer->email]))
            ->assertSessionHasErrors('email');
        $this->actingAs($manager)->put(route('staff.staff.update', $staffId), array_merge($newStaff, ['email' => 'replacement@example.com']))
            ->assertSessionHasErrors('email');

        $this->assertDatabaseHas('staff', ['staff_id' => $staffId, 'email' => $member->email, 'role' => 'staff']);
        $this->assertDatabaseHas('customers', ['email' => $customer->email]);
    }

    public function test_manager_cannot_delete_staff_with_assignment_history(): void
    {
        $manager = $this->userWithRole('manager', 'manager@example.com');
        $member = $this->userWithRole('staff', 'assigned.staff@example.com');
        $staffId = Staff::where('email', $member->email)->value('staff_id');
        $business = Business::create(['business_name' => 'Assignment Test', 'contact_email' => 'assignment@example.com', 'phone' => '0712345678']);
        $service = Service::create(['business_id' => $business->business_id, 'service_name' => 'Assignment Service', 'service_type' => 'test', 'price' => 1000]);
        $assignment = Handles::create(['staff_id' => $staffId, 'service_id' => $service->service_id, 'assigned_date' => now()->toDateString()]);

        $this->actingAs($manager)->from(route('staff.staff.index'))->delete(route('staff.staff.destroy', $staffId))
            ->assertRedirect(route('staff.staff.index'))
            ->assertSessionHasErrors('staff');

        $this->assertDatabaseHas('staff', ['staff_id' => $staffId]);
        $this->assertDatabaseHas('handles', ['handles_id' => $assignment->handles_id]);
    }

    public function test_manager_cannot_delete_staff_with_a_login_account(): void
    {
        $manager = $this->userWithRole('manager', 'manager@example.com');
        $member = $this->userWithRole('staff', 'login.staff@example.com');
        $staffId = Staff::where('email', $member->email)->value('staff_id');

        $this->actingAs($manager)->get(route('staff.staff.index'))
            ->assertOk()
            ->assertSee('Staff login accounts cannot be deleted');

        $this->actingAs($manager)->from(route('staff.staff.index'))->delete(route('staff.staff.destroy', $staffId))
            ->assertRedirect(route('staff.staff.index'))
            ->assertSessionHasErrors('staff');

        $this->assertDatabaseHas('staff', ['staff_id' => $staffId, 'email' => $member->email, 'role' => 'staff']);
        $this->assertDatabaseHas('users', ['email' => $member->email]);
    }

    public function test_manager_dashboard_and_staff_list_show_relevant_assignments(): void
    {
        $manager = $this->userWithRole('manager', 'manager@example.com');
        $member = $this->userWithRole('staff', 'assignment.view@example.com');
        $staffId = Staff::where('email', $member->email)->value('staff_id');
        $business = Business::create(['business_name' => 'Assignment View Test', 'contact_email' => 'assignment.view@example.com', 'phone' => '0712345678']);
        $service = Service::create(['business_id' => $business->business_id, 'service_name' => 'Laser Gift Service', 'service_type' => 'test', 'price' => 1000]);
        Handles::create(['staff_id' => $staffId, 'service_id' => $service->service_id, 'assigned_date' => now()->toDateString(), 'status' => 'processing']);

        $this->actingAs($manager)->get(route('manager.dashboard'))->assertOk()->assertSee('Laser Gift Service')->assertSee('processing');
        $this->actingAs($manager)->get(route('staff.staff.index'))->assertOk()->assertSee('Laser Gift Service')->assertSee('processing');
    }
}
