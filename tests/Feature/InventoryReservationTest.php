<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\Event;
use App\Models\GiftDesign;
use App\Models\InventoryAdjustment;
use App\Models\InventoryReservation;
use App\Models\LaserWork;
use App\Models\Order;
use App\Models\Staff;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InventoryReservationTest extends TestCase
{
    use RefreshDatabase;

    private function customer(string $email = 'customer@example.com'): User
    {
        $user = User::factory()->create(['email' => $email]);
        Customer::create(['full_name' => 'Customer', 'email' => $email, 'registered_date' => now()]);

        return $user;
    }

    private function employee(string $role, string $email): User
    {
        Staff::create(['full_name' => ucfirst($role), 'role' => $role, 'email' => $email, 'phone' => '0771234567', 'hire_date' => now()->toDateString()]);

        return User::factory()->create(['email' => $email]);
    }

    private function order(User $user, string $type, int $id, int $quantity = 1, string $token = 'a')
    {
        return $this->actingAs($user)->post(route('orders.catalog.store'), [
            'item_type' => $type,
            'item_id' => $id,
            'quantity' => $quantity,
            'submission_token' => str_repeat($token, 64),
        ]);
    }

    private function gift(int $stock = 5, string $category = 'Gift'): GiftDesign
    {
        return GiftDesign::create(['item_name' => 'Stock Gift', 'category' => $category, 'price' => 1000, 'stock_quantity' => $stock]);
    }

    public function test_gift_order_reserves_stock_and_rejects_insufficient_or_invalid_quantities(): void
    {
        $customer = $this->customer();
        $gift = $this->gift(3);

        $this->order($customer, 'gift', $gift->gift_design_id, 2)
            ->assertRedirect(route('checkout.confirmation', ['order' => 1]));
        $this->assertDatabaseHas('gift_designs', ['gift_design_id' => $gift->gift_design_id, 'stock_quantity' => 3, 'reserved_quantity' => 2]);
        $this->assertDatabaseHas('inventory_reservations', ['item_type' => 'gift', 'item_id' => $gift->gift_design_id, 'quantity' => 2, 'status' => 'reserved']);

        $other = $this->customer('other@example.com');
        $this->order($other, 'gift', $gift->gift_design_id, 2, 'b')->assertSessionHasErrors('items');
        $this->order($other, 'gift', $gift->gift_design_id, 0, 'c')->assertSessionHasErrors('quantity');
        $this->assertDatabaseCount('orders', 1);
        $this->assertSame(2, $gift->fresh()->reserved_quantity);
    }

    public function test_out_of_stock_gift_is_rejected_without_creating_order_or_reservation(): void
    {
        $customer = $this->customer();
        $gift = $this->gift(0);

        $this->order($customer, 'gift', $gift->gift_design_id)->assertSessionHasErrors('items');

        $this->assertDatabaseCount('orders', 0);
        $this->assertDatabaseCount('inventory_reservations', 0);
        $this->assertSame(0, $gift->fresh()->stock_quantity);
    }

    public function test_plural_frame_category_can_be_ordered_and_adjusted_as_frame_inventory(): void
    {
        $customer = $this->customer();
        $frame = $this->gift(3, 'Frames');

        $this->order($customer, 'frame', $frame->gift_design_id)->assertRedirect();
        $this->assertDatabaseHas('inventory_reservations', [
            'item_type' => 'frame',
            'item_id' => $frame->gift_design_id,
            'status' => 'reserved',
        ]);

        $manager = $this->employee('manager', 'frame-manager@example.com');
        $this->actingAs($manager)->put(route('staff.inventory.adjust', ['type' => 'frame', 'id' => $frame->gift_design_id]), [
            'action' => 'add', 'quantity' => 1,
        ])->assertRedirect();

        $this->assertSame(4, $frame->fresh()->stock_quantity);
    }

    public function test_plural_frame_category_can_be_reserved_through_existing_cart_checkout(): void
    {
        $customer = $this->customer();
        $frame = $this->gift(3, 'Frames');

        $this->actingAs($customer)->post(route('checkout.store'), [
            'submission_token' => str_repeat('j', 64),
            'items' => [['item_id' => $frame->gift_design_id, 'item_type' => 'frame', 'quantity' => 2]],
        ])->assertRedirect(route('checkout.confirmation', ['order' => 1]));

        $this->assertDatabaseHas('gift_designs', [
            'gift_design_id' => $frame->gift_design_id,
            'stock_quantity' => 3,
            'reserved_quantity' => 2,
        ]);
        $this->assertDatabaseHas('inventory_reservations', [
            'item_type' => 'frame',
            'item_id' => $frame->gift_design_id,
            'quantity' => 2,
            'status' => 'reserved',
        ]);
    }

    public function test_duplicate_order_and_payment_confirmation_change_stock_only_once(): void
    {
        $customer = $this->customer();
        $gift = $this->gift(5);
        $payload = ['item_type' => 'gift', 'item_id' => $gift->gift_design_id, 'quantity' => 2, 'submission_token' => str_repeat('d', 64)];

        $this->actingAs($customer)->post(route('orders.catalog.store'), $payload)->assertRedirect(route('checkout.confirmation', ['order' => 1]));
        $this->post(route('orders.catalog.store'), $payload)->assertRedirect(route('checkout.confirmation', ['order' => 1]));
        $this->assertDatabaseCount('orders', 1);
        $this->assertDatabaseCount('inventory_reservations', 1);

        $staff = $this->employee('staff', 'staff@example.com');
        $payment = ['advanced_paid' => '500.00'];
        $this->actingAs($staff)->put(route('staff.orders.payment', 1), $payment)->assertRedirect(route('staff.orders.show', 1));
        $this->put(route('staff.orders.payment', 1), $payment)->assertRedirect(route('staff.orders.show', 1));
        $this->assertDatabaseHas('gift_designs', ['gift_design_id' => $gift->gift_design_id, 'stock_quantity' => 3, 'reserved_quantity' => 0]);
        $this->assertDatabaseHas('inventory_reservations', ['order_item_id' => 1, 'status' => 'confirmed']);
    }

    public function test_cancellation_releases_reservations_or_restores_confirmed_stock_once(): void
    {
        $customer = $this->customer();
        $gift = $this->gift(5);
        $this->order($customer, 'gift', $gift->gift_design_id, 2)->assertRedirect();
        $staff = $this->employee('staff', 'staff@example.com');

        $this->actingAs($staff)->put(route('staff.orders.status', 1), ['status' => 'cancelled'])->assertRedirect(route('staff.orders.show', 1));
        $this->put(route('staff.orders.status', 1), ['status' => 'cancelled'])->assertRedirect(route('staff.orders.show', 1));
        $this->assertDatabaseHas('gift_designs', ['gift_design_id' => $gift->gift_design_id, 'stock_quantity' => 5, 'reserved_quantity' => 0]);
        $this->assertDatabaseHas('inventory_reservations', ['order_item_id' => 1, 'status' => 'released']);

        $secondGift = GiftDesign::create(['item_name' => 'Second Gift', 'category' => 'Gift', 'price' => 1000, 'stock_quantity' => 5]);
        $this->actingAs($customer)->post(route('orders.catalog.store'), ['item_type' => 'gift', 'item_id' => $secondGift->gift_design_id, 'quantity' => 2, 'submission_token' => str_repeat('e', 64)])->assertRedirect();
        $this->actingAs($staff)->put(route('staff.orders.payment', 2), ['advanced_paid' => '1000.00'])->assertRedirect();
        $this->put(route('staff.orders.status', 2), ['status' => 'cancelled'])->assertRedirect();
        $this->put(route('staff.orders.status', 2), ['status' => 'cancelled'])->assertRedirect();
        $this->assertDatabaseHas('gift_designs', ['gift_design_id' => $secondGift->gift_design_id, 'stock_quantity' => 5, 'reserved_quantity' => 0]);
        $this->assertDatabaseHas('inventory_reservations', ['order_item_id' => 2, 'status' => 'restored']);
    }

    public function test_completed_order_cancellation_does_not_restore_fulfilled_stock(): void
    {
        $customer = $this->customer();
        $gift = $this->gift(3);
        $this->order($customer, 'gift', $gift->gift_design_id, 2)->assertRedirect();
        $staff = $this->employee('staff', 'staff@example.com');

        $this->actingAs($staff)->put(route('staff.orders.payment', 1), ['advanced_paid' => '1000.00'])->assertRedirect();
        $this->put(route('staff.orders.status', 1), ['status' => 'completed'])->assertRedirect();
        $this->put(route('staff.orders.status', 1), ['status' => 'cancelled'])->assertRedirect();

        $this->assertDatabaseHas('gift_designs', ['gift_design_id' => $gift->gift_design_id, 'stock_quantity' => 1, 'reserved_quantity' => 0]);
        $this->assertDatabaseHas('inventory_reservations', ['order_item_id' => 1, 'status' => 'fulfilled']);
    }

    public function test_ready_made_laser_uses_stock_while_custom_made_laser_does_not(): void
    {
        $customer = $this->customer();
        $custom = LaserWork::create(['product_name' => 'Custom Laser', 'laser_type' => 'Engraving', 'price' => 1500, 'production_type' => 'custom_made', 'stock_quantity' => 0]);
        $ready = LaserWork::create(['product_name' => 'Ready Laser', 'laser_type' => 'Cutting', 'price' => 2000, 'production_type' => 'ready_made', 'stock_quantity' => 3]);

        $this->order($customer, 'laser', $custom->laser_id, 2)->assertRedirect();
        $this->assertDatabaseMissing('inventory_reservations', ['item_type' => 'laser', 'item_id' => $custom->laser_id]);

        $this->order($customer, 'laser', $ready->laser_id, 2, 'f')->assertRedirect();
        $this->assertDatabaseHas('laser_works', ['laser_id' => $ready->laser_id, 'stock_quantity' => 3, 'reserved_quantity' => 2]);
        $this->order($customer, 'laser', $ready->laser_id, 2, 'g')->assertSessionHasErrors('items');
    }

    public function test_event_bookings_reserve_capacity_and_confirmation_cancellation_are_idempotent(): void
    {
        $customer = $this->customer();
        $event = Event::create(['event_name' => 'Wedding Setup', 'event_type' => 'Wedding', 'event_date' => now()->addMonth(), 'price' => 10000, 'booking_capacity' => 2]);

        $this->order($customer, 'event', $event->event_id, 2)->assertRedirect();
        $this->assertDatabaseHas('events', ['event_id' => $event->event_id, 'reserved_slots' => 2, 'booked_slots' => 0]);
        $other = $this->customer('second@example.com');
        $this->order($other, 'event', $event->event_id, 1, 'h')->assertSessionHasErrors('items');

        $manager = $this->employee('manager', 'manager@example.com');
        $this->actingAs($manager)->put(route('staff.orders.payment', 1), ['advanced_paid' => '5000.00'])->assertRedirect();
        $this->assertDatabaseHas('events', ['event_id' => $event->event_id, 'reserved_slots' => 0, 'booked_slots' => 2]);
        $this->put(route('staff.orders.status', 1), ['status' => 'cancelled'])->assertRedirect();
        $this->assertDatabaseHas('events', ['event_id' => $event->event_id, 'reserved_slots' => 0, 'booked_slots' => 0]);
    }

    public function test_manager_can_adjust_inventory_staff_is_read_only_and_customers_are_blocked(): void
    {
        $gift = $this->gift(0);
        $manager = $this->employee('manager', 'manager@example.com');
        $staff = $this->employee('staff', 'staff@example.com');
        $customer = $this->customer();

        $this->actingAs($manager)->get(route('staff.inventory.index'))->assertOk()->assertSee('Inventory');
        $this->put(route('staff.inventory.adjust', ['type' => 'gift', 'id' => $gift->gift_design_id]), [
            'action' => 'add', 'quantity' => 6, 'low_stock_threshold' => 2, 'note' => 'Opening count',
        ])->assertRedirect();
        $this->assertDatabaseHas('gift_designs', ['gift_design_id' => $gift->gift_design_id, 'stock_quantity' => 6, 'low_stock_threshold' => 2]);
        $this->assertDatabaseHas('inventory_adjustments', ['item_type' => 'gift', 'item_id' => $gift->gift_design_id, 'quantity_delta' => 6, 'note' => 'Opening count']);

        $this->actingAs($staff)->get(route('staff.inventory.index'))->assertOk();
        $this->put(route('staff.inventory.adjust', ['type' => 'gift', 'id' => $gift->gift_design_id]), ['action' => 'add', 'quantity' => 1])->assertForbidden();
        $this->actingAs($customer)->get(route('staff.inventory.index'))->assertForbidden();
        $this->get(route('admin.inventory.index'))->assertForbidden();
    }

    public function test_admin_can_adjust_stock_and_reserved_stock_cannot_be_removed(): void
    {
        $admin = $this->employee('admin', 'admin@example.com');
        $customer = $this->customer();
        $gift = $this->gift(4);
        $this->actingAs($admin)->put(route('admin.inventory.adjust', ['type' => 'gift', 'id' => $gift->gift_design_id]), ['action' => 'remove', 'quantity' => 5])->assertSessionHasErrors('quantity');
        $this->assertSame(4, $gift->fresh()->stock_quantity);

        $this->put(route('admin.inventory.adjust', ['type' => 'gift', 'id' => $gift->gift_design_id]), ['action' => 'set', 'quantity' => 4, 'note' => 'Count correction'])->assertRedirect();
        $this->assertDatabaseHas('inventory_adjustments', ['item_type' => 'gift', 'quantity_before' => 4, 'quantity_after' => 4]);

        $this->order($customer, 'gift', $gift->gift_design_id, 2)->assertRedirect();
        $this->actingAs($admin)->put(route('admin.inventory.adjust', ['type' => 'gift', 'id' => $gift->gift_design_id]), ['action' => 'set', 'quantity' => 1])->assertSessionHasErrors('quantity');
        $this->assertSame(4, $gift->fresh()->stock_quantity);
    }
}
