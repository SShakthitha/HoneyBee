<?php

namespace Tests\Feature\Admin;

use App\Models\Business;
use App\Models\Customer;
use App\Models\Handles;
use App\Models\Order;
use App\Models\Service;
use App\Models\Staff;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BusinessDeletionSafetyTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_delete_a_business_without_related_records(): void
    {
        $admin = $this->admin();
        $business = $this->business();

        $this->actingAs($admin)
            ->from(route('admin.businesses'))
            ->delete(route('admin.businesses.delete', $business->business_id))
            ->assertRedirect(route('admin.businesses'))
            ->assertSessionHas('success', 'Business deleted!');

        $this->assertDatabaseMissing('businesses', ['business_id' => $business->business_id]);
    }

    public function test_business_with_staff_cannot_be_deleted_and_staff_is_preserved(): void
    {
        $admin = $this->admin();
        $business = $this->business();
        $staff = $this->staff($business);

        $this->assertDeletionBlocked($admin, $business, 'staff');

        $this->assertDatabaseHas('businesses', ['business_id' => $business->business_id]);
        $this->assertDatabaseHas('staff', ['staff_id' => $staff->staff_id, 'business_id' => $business->business_id]);
    }

    public function test_business_table_disables_delete_when_related_records_exist(): void
    {
        $admin = $this->admin();
        $business = $this->business();
        $this->service($business);

        $response = $this->actingAs($admin)->getJson(route('admin.businesses.data'));

        $response->assertOk();
        $this->assertStringContainsString('disabled aria-disabled="true"', $response->json('data.0.action'));
        $this->assertStringContainsString('linked staff or services', $response->json('data.0.action'));
    }

    public function test_business_with_services_cannot_be_deleted_and_service_is_preserved(): void
    {
        $admin = $this->admin();
        $business = $this->business();
        $service = $this->service($business);

        $this->assertDeletionBlocked($admin, $business, 'services');

        $this->assertDatabaseHas('businesses', ['business_id' => $business->business_id]);
        $this->assertDatabaseHas('services', ['service_id' => $service->service_id, 'business_id' => $business->business_id]);
    }

    public function test_business_with_staff_assignments_cannot_be_deleted_and_assignment_is_preserved(): void
    {
        $admin = $this->admin();
        $business = $this->business();
        $service = $this->service($business);
        $staff = $this->staff();
        $assignment = Handles::create([
            'staff_id' => $staff->staff_id,
            'service_id' => $service->service_id,
            'assigned_date' => now()->toDateString(),
        ]);

        $this->assertDeletionBlocked($admin, $business, 'staff assignments');

        $this->assertDatabaseHas('businesses', ['business_id' => $business->business_id]);
        $this->assertDatabaseHas('services', ['service_id' => $service->service_id]);
        $this->assertDatabaseHas('staff', ['staff_id' => $staff->staff_id]);
        $this->assertDatabaseHas('handles', ['handles_id' => $assignment->handles_id]);
    }

    public function test_business_with_historical_orders_cannot_be_deleted_and_order_relationship_is_preserved(): void
    {
        $admin = $this->admin();
        $business = $this->business();
        $service = $this->service($business);
        $customer = Customer::create([
            'full_name' => 'Order History Customer',
            'email' => 'order-history@example.com',
            'registered_date' => now(),
        ]);
        $order = Order::create([
            'customer_id' => $customer->customer_id,
            'service_id' => $service->service_id,
            'order_date' => now()->toDateString(),
            'status' => 'completed',
        ]);

        $this->assertDeletionBlocked($admin, $business, 'historical orders');

        $this->assertDatabaseHas('businesses', ['business_id' => $business->business_id]);
        $this->assertDatabaseHas('services', ['service_id' => $service->service_id, 'business_id' => $business->business_id]);
        $this->assertDatabaseHas('orders', ['order_id' => $order->order_id, 'service_id' => $service->service_id]);
    }

    public function test_customer_and_staff_cannot_delete_businesses(): void
    {
        $business = $this->business();
        $customer = User::factory()->create(['email' => 'business-customer@example.com']);
        $staffUser = User::factory()->create(['email' => 'business-staff@example.com']);
        $this->staff(null, $staffUser->email, 'staff');

        $this->actingAs($customer)
            ->delete(route('admin.businesses.delete', $business->business_id))
            ->assertForbidden();

        $this->actingAs($staffUser)
            ->delete(route('admin.businesses.delete', $business->business_id))
            ->assertForbidden();

        $this->assertDatabaseHas('businesses', ['business_id' => $business->business_id]);
    }

    private function assertDeletionBlocked(User $admin, Business $business, string $reason): void
    {
        $this->actingAs($admin)
            ->from(route('admin.businesses'))
            ->delete(route('admin.businesses.delete', $business->business_id))
            ->assertRedirect(route('admin.businesses'))
            ->assertSessionHasErrors('business');

        $message = session('errors')->getBag('default')->first('business');
        $this->assertStringContainsString($reason, $message);
        $this->assertStringContainsString('preserved', $message);
    }

    private function business(): Business
    {
        return Business::create([
            'business_name' => 'SQLite Business Deletion Test',
            'contact_email' => 'business-deletion@example.com',
            'phone' => '0700000000',
        ]);
    }

    private function service(Business $business): Service
    {
        return Service::create([
            'business_id' => $business->business_id,
            'service_name' => 'SQLite Service Deletion Test',
            'service_type' => 'verification',
            'availability_status' => 'available',
            'price' => '1000.00',
        ]);
    }

    private function staff(?Business $business = null, ?string $email = null, string $role = 'staff'): Staff
    {
        return Staff::create([
            'business_id' => $business?->business_id,
            'full_name' => 'SQLite Staff Deletion Test',
            'role' => $role,
            'email' => $email ?? 'staff-deletion-'.uniqid().'@example.com',
            'phone' => '0700000001',
            'hire_date' => now()->toDateString(),
        ]);
    }

    private function admin(): User
    {
        $email = 'business-admin-'.uniqid().'@example.com';
        $this->staff(null, $email, 'admin');

        return User::factory()->create(['email' => $email]);
    }
}
