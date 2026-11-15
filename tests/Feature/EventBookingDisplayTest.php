<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\Event;
use App\Models\InventoryReservation;
use App\Models\Order;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EventBookingDisplayTest extends TestCase
{
    use RefreshDatabase;

    private function event(int $capacity = 3): Event
    {
        return Event::create([
            'event_name' => 'Garden Celebration',
            'event_type' => 'Wedding',
            'event_date' => now()->addMonth(),
            'event_location' => 'Jaffna',
            'price' => 12500,
            'description' => 'A floral outdoor celebration package.',
            'booking_capacity' => $capacity,
        ]);
    }

    private function customer(): User
    {
        $user = User::factory()->create([
            'email' => 'event-customer@example.com',
            'email_verified_at' => now(),
        ]);
        Customer::create([
            'full_name' => 'Event Customer',
            'email' => $user->email,
            'registered_date' => now(),
        ]);

        return $user;
    }

    public function test_event_cards_hide_slot_counts_and_customer_quantity_selection(): void
    {
        $this->event();

        $this->get(route('events'))
            ->assertOk()
            ->assertSee('Garden Celebration')
            ->assertSee('A floral outdoor celebration package.')
            ->assertSee('Rs 12,500.00')
            ->assertDontSee('booking slots available')
            ->assertDontSee('Reserve slots')
            ->assertDontSee('type="number"', false);
    }

    public function test_event_booking_uses_one_hidden_quantity_and_still_reserves_capacity(): void
    {
        $event = $this->event();
        $customer = $this->customer();

        $page = $this->actingAs($customer)->get(route('events'))->assertOk();
        $page->assertSee('Book Event')
            ->assertSee('name="quantity" value="1"', false)
            ->assertDontSee('type="number"', false)
            ->assertDontSee('booking slots available');

        $this->post(route('orders.catalog.store'), [
            'item_type' => 'event',
            'item_id' => $event->event_id,
            'quantity' => 1,
            'submission_token' => str_repeat('e', 64),
        ])->assertRedirect(route('checkout.confirmation', ['order' => 1]));

        $this->assertDatabaseHas('events', [
            'event_id' => $event->event_id,
            'booking_capacity' => 3,
            'reserved_slots' => 1,
            'booked_slots' => 0,
        ]);
        $this->assertSame('event', InventoryReservation::sole()->item_type);
        $this->assertSame(1, InventoryReservation::sole()->quantity);
        $this->assertSame($customer->id, Order::sole()->customer_id);
    }

    public function test_customer_event_listing_does_not_expose_capacity_availability(): void
    {
        $this->event(0);
        $customer = $this->customer();

        $this->actingAs($customer)->get(route('events'))
            ->assertOk()
            ->assertSee('Garden Celebration')
            ->assertSee('Book Event')
            ->assertDontSee('Currently unavailable')
            ->assertDontSee('booking slots available');
    }
}
