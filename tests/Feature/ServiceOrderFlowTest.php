<?php

namespace Tests\Feature;

use App\Models\Business;
use App\Models\Customer;
use App\Models\Order;
use App\Models\Service;
use App\Models\Staff;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ServiceOrderFlowTest extends TestCase
{
    use RefreshDatabase;

    private function customer(): array
    {
        $user = User::factory()->create(['email' => 'customer@example.com']);
        $customer = Customer::create([
            'full_name' => 'Customer',
            'email' => $user->email,
            'registered_date' => now(),
        ]);

        return [$user, $customer];
    }

    private function service(string $status = 'available'): Service
    {
        $business = Business::create([
            'business_name' => 'HoneyBee Shop',
            'contact_email' => 'shop@example.com',
            'phone' => '0771234567',
        ]);

        return Service::create([
            'business_id' => $business->business_id,
            'service_name' => 'Custom decoration',
            'service_type' => 'event',
            'description' => 'Custom styling for a celebration.',
            'availability_status' => $status,
            'price' => 5000,
        ]);
    }

    private function staffUser(string $role, string $email): User
    {
        Staff::create([
            'full_name' => ucfirst($role),
            'role' => $role,
            'email' => $email,
            'phone' => '0771234567',
            'hire_date' => now()->toDateString(),
        ]);

        return User::factory()->create(['email' => $email]);
    }

    public function test_service_listing_and_detail_pages_render_available_services(): void
    {
        $available = $this->service();
        $this->service('unavailable');

        $this->get(route('services'))
            ->assertOk()
            ->assertSee('Custom decoration')
            ->assertSee(route('services.show', $available->service_id), false)
            ->assertDontSee('Unavailable decoration');

        $this->get(route('services.show', $available->service_id))
            ->assertOk()
            ->assertSee('Custom decoration')
            ->assertSee('Rs. 5,000.00')
            ->assertSee('Request this service');

        $unavailableId = Service::where('availability_status', 'unavailable')->value('service_id');
        $this->get(route('services.show', $unavailableId))->assertNotFound();
        $this->get(route('services.show', 999999))->assertNotFound();
    }

    public function test_service_order_uses_server_price_and_ignores_forged_payment_totals(): void
    {
        [$user, $customer] = $this->customer();
        $service = $this->service();

        $this->actingAs($user)->post(route('orders.store'), [
            'service_id' => $service->service_id,
            'payment_method' => 'cash',
            'paid_amount' => '0.01',
            'advanced_paid' => '5000.00',
            'discount' => '4999.99',
            'delivery_date' => now()->addDays(5)->toDateString(),
            'attribute' => 'Please use honey-colored ribbons.',
        ])->assertRedirect(route('orders.index'));

        $order = Order::sole();
        $this->assertSame($customer->customer_id, $order->customer_id);
        $this->assertSame($service->service_id, $order->service_id);
        $this->assertSame('5000.00', $order->paid_amount);
        $this->assertSame('0.00', $order->advanced_paid);
        $this->assertSame('0.00', $order->discount);
        $this->assertSame('cash', $order->payment_method);
        $this->assertSame('pending', $order->status);
        $this->assertSame('Please use honey-colored ribbons.', $order->attribute);
    }

    public function test_invalid_payment_method_and_dates_are_rejected(): void
    {
        [$user] = $this->customer();
        $service = $this->service();

        $this->actingAs($user)->from(route('orders.create', $service->service_id))
            ->post(route('orders.store'), [
                'service_id' => $service->service_id,
                'payment_method' => 'unrecognized',
                'delivery_date' => now()->subDay()->toDateString(),
            ])
            ->assertRedirect(route('orders.create', $service->service_id))
            ->assertSessionHasErrors(['payment_method', 'delivery_date']);

        $this->assertDatabaseCount('orders', 0);
    }

    public function test_staff_and_admin_can_record_only_confirmed_advances_within_the_order_total(): void
    {
        [$customerUser] = $this->customer();
        $service = $this->service();
        $this->actingAs($customerUser)->post(route('orders.store'), [
            'service_id' => $service->service_id,
            'payment_method' => 'bank_transfer',
        ])->assertRedirect(route('orders.index'));
        $order = Order::sole();

        $staff = $this->staffUser('staff', 'staff@example.com');
        $this->actingAs($staff)->put(route('staff.orders.payment', $order->order_id), [
            'advanced_paid' => '1250.50',
        ])->assertRedirect(route('staff.orders.show', $order->order_id));
        $this->assertSame('1250.50', $order->fresh()->advanced_paid);
        $this->assertNotNull($order->fresh()->payment_date);

        $admin = $this->staffUser('admin', 'admin@example.com');
        $this->actingAs($admin)->put(route('admin.orders.payment', $order->order_id), [
            'advanced_paid' => '2000.00',
        ])->assertRedirect(route('admin.orders.show', $order->order_id));
        $this->assertSame('2000.00', $order->fresh()->advanced_paid);

        $this->actingAs($admin)->from(route('admin.orders.show', $order->order_id))
            ->put(route('admin.orders.payment', $order->order_id), ['advanced_paid' => '5000.01'])
            ->assertRedirect(route('admin.orders.show', $order->order_id))
            ->assertSessionHasErrors('advanced_paid');
        $this->assertSame('2000.00', $order->fresh()->advanced_paid);

        $this->actingAs($customerUser)->put(route('staff.orders.payment', $order->order_id), [
            'advanced_paid' => '1000.00',
        ])->assertForbidden();
    }
}
