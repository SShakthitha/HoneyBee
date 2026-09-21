<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\Order;
use App\Models\Staff;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class StaffDashboardAuthorizationTest extends TestCase
{
    use RefreshDatabase;

    private function staffUser(): User
    {
        $email = 'operator@example.com';
        Staff::create(['full_name' => 'Operations User', 'role' => 'staff', 'email' => $email, 'phone' => '0771234567', 'hire_date' => now()->toDateString()]);

        return User::factory()->create(['email' => $email]);
    }

    public function test_staff_can_access_their_operational_workspace(): void
    {
        $user = $this->staffUser();
        $this->actingAs($user)->get(route('staff.dashboard'))->assertOk();
        $this->actingAs($user)->get(route('staff.products.index'))->assertOk();
        $this->actingAs($user)->get(route('staff.gallery.index'))->assertOk();
    }

    public function test_staff_cannot_access_admin_customer_or_staff_management(): void
    {
        $user = $this->staffUser();
        $this->actingAs($user)->get(route('admin.customers.index'))->assertForbidden();
        $this->actingAs($user)->get(route('admin.customers.edit', 123))->assertForbidden();
        $this->actingAs($user)->delete(route('admin.customers.delete', 123))->assertForbidden();
        $this->actingAs($user)->get(route('admin.staff'))->assertForbidden();
    }

    public function test_non_staff_cannot_access_the_staff_workspace(): void
    {
        $this->actingAs(User::factory()->create())->get(route('staff.dashboard'))->assertForbidden();
    }

    public function test_staff_can_handle_operational_resources_but_cannot_change_role(): void
    {
        Storage::fake('public');
        $user = $this->staffUser();
        $customer = Customer::create(['full_name' => 'Customer', 'email' => 'customer@example.com', 'phone' => '0710000000', 'registered_date' => now()]);
        $order = Order::create(['customer_id' => $customer->customer_id, 'order_date' => now(), 'status' => 'pending']);

        $this->actingAs($user)->put(route('staff.orders.status', $order->order_id), ['status' => 'processing'])->assertRedirect();
        $this->assertDatabaseHas('orders', ['order_id' => $order->order_id, 'status' => 'processing']);

        $this->actingAs($user)->post(route('staff.products.store', 'gift'), ['item_name' => 'Gift box', 'category' => 'Gift', 'price' => 1500])->assertRedirect(route('staff.products.index'));
        $this->assertDatabaseHas('gift_designs', ['item_name' => 'Gift box']);

        $this->actingAs($user)->post(route('staff.gallery.store'), ['title' => 'New gallery item', 'category' => 'Events', 'image' => UploadedFile::fake()->image('event.jpg')])->assertRedirect(route('staff.gallery.index'));
        $this->assertDatabaseCount('gallery_images', 1);

        $this->actingAs($user)->put(route('staff.profile.update'), ['full_name' => 'New Name', 'phone' => '0720000000', 'role' => 'admin'])->assertRedirect(route('staff.profile'));
        $this->assertDatabaseHas('staff', ['email' => $user->email, 'full_name' => 'New Name', 'role' => 'staff']);
    }
}
