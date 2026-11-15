<?php

namespace Tests\Feature;

use App\Models\Business;
use App\Models\Customer;
use App\Models\GiftDesign;
use App\Models\Order;
use App\Models\Service;
use App\Models\Staff;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BusinessFunctionalityCompletionTest extends TestCase
{
    use RefreshDatabase;

    public function test_customer_checkout_saves_correct_order_and_displays_confirmation_and_history(): void
    {
        $user = User::factory()->create(['email' => 'checkout-test@example.com']);
        $customer = Customer::create([
            'full_name' => 'Checkout Test Customer',
            'email' => $user->email,
            'phone' => '0700000000',
            'registered_date' => now(),
        ]);
        $product = GiftDesign::create([
            'item_name' => 'Isolated Test Gift',
            'category' => 'Gift',
            'price' => 1500,
            'offer_price' => 1200,
            'stock_quantity' => 10,
        ]);

        $this->get(route('gift.design'))->assertOk()->assertSee('Isolated Test Gift');

        $this->actingAs($user)->post(route('checkout.store'), [
            'submission_token' => str_repeat('a', 64),
            'items' => [[
                'item_id' => $product->gift_design_id,
                'item_type' => 'gift',
                'quantity' => 2,
                'price' => 0.01,
            ]],
            'notes' => 'SQLite checkout verification',
        ])->assertRedirect(route('checkout.confirmation', ['order' => 1]));

        $order = Order::with('items')->sole();
        $this->assertSame($customer->customer_id, $order->customer_id);
        $this->assertSame('2400.00', $order->paid_amount);
        $this->assertSame('0.00', $order->advanced_paid);
        $this->assertSame('0.00', $order->discount);
        $this->assertSame('pending', $order->status);
        $this->assertSame('bank_transfer', $order->payment_method);
        $this->assertSame('Isolated Test Gift', $order->items->sole()->item_name);
        $this->assertSame('1200.00', $order->items->sole()->price);
        $this->assertSame(2, $order->items->sole()->quantity);
        $this->assertSame('2400.00', $order->items->sole()->subtotal);
        $this->assertSame('SQLite checkout verification', $order->attribute);

        $this->actingAs($user)
            ->get(route('checkout.confirmation', $order))
            ->assertOk()
            ->assertSee('Your order ID is <strong>#1</strong>', false);

        $this->actingAs($user)
            ->get(route('orders.index'))
            ->assertOk()
            ->assertSee('Isolated Test Gift')
            ->assertSee('Qty: 2')
            ->assertSee('Rs. 2,400.00')
            ->assertSee('pending');
    }

    public function test_admin_can_create_edit_deactivate_reactivate_and_remove_bookable_service(): void
    {
        $admin = $this->admin();
        $business = Business::create([
            'business_name' => 'SQLite Service Test Business',
            'contact_email' => 'service-test@example.com',
            'phone' => '0700000001',
        ]);

        $this->actingAs($admin)->get(route('admin.service-records.create'))
            ->assertOk()->assertSee('SQLite Service Test Business');

        $this->actingAs($admin)->post(route('admin.service-records.store'), [
            'business_id' => $business->business_id,
            'service_name' => 'SQLite Test Service',
            'service_type' => 'verification',
            'description' => 'Temporary isolated test record.',
            'availability_status' => 'available',
            'price' => '5000.00',
            'category' => 'test',
        ])->assertRedirect(route('admin.service-records.index'));

        $service = Service::sole();
        $this->assertDatabaseHas('services', [
            'service_id' => $service->service_id,
            'service_name' => 'SQLite Test Service',
            'price' => '5000.00',
            'availability_status' => 'available',
        ]);
        $this->actingAs($admin)->get(route('admin.service-records.index'))
            ->assertOk()->assertSee('SQLite Test Service')->assertSee('Available');
        $this->actingAs($admin)->get(route('admin.service-records.edit', $service->service_id))
            ->assertOk()->assertSee('SQLite Test Service')->assertSee('id="service-price"', false);
        $this->get(route('services'))->assertOk()->assertSee('SQLite Test Service');
        $this->get(route('services.show', $service->service_id))->assertOk()->assertSee('Rs. 5,000.00');

        $this->actingAs($admin)->put(route('admin.service-records.update', $service->service_id), [
            'business_id' => $business->business_id,
            'service_name' => 'Updated SQLite Test Service',
            'service_type' => 'verification',
            'availability_status' => 'unavailable',
            'price' => '5000.00',
        ])->assertRedirect(route('admin.service-records.index'));

        $this->assertDatabaseHas('services', ['service_id' => $service->service_id, 'availability_status' => 'unavailable']);
        $this->get(route('services'))->assertOk()->assertDontSee('Updated SQLite Test Service');
        $this->get(route('services.show', $service->service_id))->assertNotFound();

        $this->actingAs($admin)->put(route('admin.service-records.update', $service->service_id), [
            'business_id' => $business->business_id,
            'service_name' => 'Updated SQLite Test Service',
            'service_type' => 'verification',
            'availability_status' => 'available',
            'price' => '5000.00',
        ])->assertRedirect(route('admin.service-records.index'));

        $this->get(route('services.show', $service->service_id))->assertOk()->assertSee('Updated SQLite Test Service');
        $this->actingAs($admin)->delete(route('admin.service-records.destroy', $service->service_id))
            ->assertRedirect(route('admin.service-records.index'));
        $this->assertDatabaseMissing('services', ['service_id' => $service->service_id]);
    }

    public function test_admin_can_create_update_and_delete_product_customer_and_staff_test_records(): void
    {
        $admin = $this->admin();

        $this->actingAs($admin)->post(route('admin.gift.store'), [
            'item_name' => 'SQLite Test Product',
            'category' => 'Gift',
            'price' => '1350.00',
            'description' => 'Temporary isolated product test.',
        ])->assertRedirect(route('admin.services.gift'));
        $product = GiftDesign::where('item_name', 'SQLite Test Product')->sole();
        $this->actingAs($admin)->put(route('admin.gift.update', $product->gift_design_id), [
            'item_name' => 'Updated SQLite Test Product',
            'category' => 'Frame',
            'price' => '1550.00',
            'description' => 'Updated isolated product test.',
        ])->assertRedirect(route('admin.services.gift'));
        $this->assertDatabaseHas('gift_designs', ['gift_design_id' => $product->gift_design_id, 'item_name' => 'Updated SQLite Test Product', 'price' => '1550.00']);
        $this->actingAs($admin)->delete(route('admin.gift.destroy', $product->gift_design_id))->assertRedirect(route('admin.services.gift'));
        $this->assertDatabaseMissing('gift_designs', ['gift_design_id' => $product->gift_design_id]);

        $customerData = ['full_name' => 'SQLite Test Customer', 'email' => 'sqlite-customer@example.com', 'phone' => '0700000002', 'address' => 'Test only'];
        $this->actingAs($admin)->post(route('admin.customers.store'), $customerData)->assertRedirect(route('admin.customers.index'));
        $customer = Customer::where('email', $customerData['email'])->sole();
        $this->actingAs($admin)->put(route('admin.customers.update', $customer->customer_id), array_merge($customerData, ['full_name' => 'Updated SQLite Test Customer']))
            ->assertRedirect(route('admin.customers.index'));
        $this->assertDatabaseHas('customers', ['customer_id' => $customer->customer_id, 'full_name' => 'Updated SQLite Test Customer']);
        $this->actingAs($admin)->delete(route('admin.customers.delete', $customer->customer_id))->assertRedirect(route('admin.customers.index'));
        $this->assertDatabaseMissing('customers', ['customer_id' => $customer->customer_id]);

        $staffData = ['full_name' => 'SQLite Test Staff', 'role' => 'staff', 'email' => 'sqlite-staff@example.com', 'phone' => '0700000003', 'hire_date' => now()->toDateString()];
        $this->actingAs($admin)->post(route('admin.staff.store'), $staffData)->assertRedirect();
        $staff = Staff::where('email', $staffData['email'])->sole();
        $this->actingAs($admin)->put(route('admin.staff.update', $staff->staff_id), array_merge($staffData, ['full_name' => 'Updated SQLite Test Staff']))
            ->assertRedirect();
        $this->assertDatabaseHas('staff', ['staff_id' => $staff->staff_id, 'full_name' => 'Updated SQLite Test Staff']);
        $this->actingAs($admin)->delete(route('admin.staff.delete', $staff->staff_id))->assertRedirect();
        $this->assertDatabaseMissing('staff', ['staff_id' => $staff->staff_id]);
    }

    public function test_customer_can_update_their_profile_without_changing_another_account(): void
    {
        $user = User::factory()->create(['email' => 'profile-test@example.com']);
        $customer = Customer::create([
            'full_name' => 'Profile Test Customer',
            'email' => $user->email,
            'registered_date' => now(),
        ]);

        $this->actingAs($user)->put(route('customer.profile.update'), [
            'full_name' => 'Updated Profile Customer',
            'phone' => '0700000004',
            'address' => 'SQLite test address',
        ])->assertRedirect(route('dashboard'));

        $this->assertDatabaseHas('customers', [
            'customer_id' => $customer->customer_id,
            'full_name' => 'Updated Profile Customer',
            'phone' => '0700000004',
            'address' => 'SQLite test address',
        ]);
    }

    public function test_admin_can_update_order_status_and_record_only_valid_advance_payments(): void
    {
        $admin = $this->admin();
        $customer = Customer::create([
            'full_name' => 'Order Management Test Customer',
            'email' => 'order-management-test@example.com',
            'registered_date' => now(),
        ]);
        $order = Order::create([
            'customer_id' => $customer->customer_id,
            'order_date' => now(),
            'paid_amount' => '2400.00',
            'advanced_paid' => '0.00',
            'discount' => '0.00',
            'status' => 'pending',
        ]);

        $this->actingAs($admin)->put(route('admin.orders.status', $order->order_id), [
            'status' => 'processing',
        ])->assertRedirect(route('admin.orders'));
        $this->assertDatabaseHas('orders', ['order_id' => $order->order_id, 'status' => 'processing']);

        $this->actingAs($admin)->put(route('admin.orders.payment', $order->order_id), [
            'advanced_paid' => '800.25',
        ])->assertRedirect(route('admin.orders.show', $order->order_id));
        $this->assertSame('800.25', $order->fresh()->advanced_paid);
        $this->assertNotNull($order->fresh()->payment_date);

        $this->actingAs($admin)->from(route('admin.orders.show', $order->order_id))
            ->put(route('admin.orders.payment', $order->order_id), ['advanced_paid' => '2400.01'])
            ->assertRedirect(route('admin.orders.show', $order->order_id))
            ->assertSessionHasErrors('advanced_paid');
        $this->assertSame('800.25', $order->fresh()->advanced_paid);
    }

    private function admin(): User
    {
        $email = 'sqlite-admin@example.com';
        Staff::create([
            'full_name' => 'SQLite Test Admin',
            'role' => 'admin',
            'email' => $email,
            'phone' => '0700000005',
            'hire_date' => now()->toDateString(),
        ]);

        return User::factory()->create(['email' => $email]);
    }
}
