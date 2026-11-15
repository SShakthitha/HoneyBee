<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\GiftDesign;
use App\Models\Order;
use App\Models\Staff;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CustomerCartCheckoutFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_verified_customer_can_order_a_product_and_complete_cart_checkout(): void
    {
        [$user, $customer] = $this->customer('buyer@example.com');
        $gift = GiftDesign::create([
            'item_name' => 'Journey Gift',
            'category' => 'Gift',
            'price' => '1250.00',
            'stock_quantity' => 5,
        ]);

        $this->actingAs($user)->get(route('gift.design.show', $gift->gift_design_id))
            ->assertOk()
            ->assertSee('Journey Gift')
            ->assertSee(route('orders.catalog.store'), false);

        $this->actingAs($user)->post(route('orders.catalog.store'), [
            'item_type' => 'gift',
            'item_id' => $gift->gift_design_id,
            'quantity' => 1,
            'submission_token' => str_repeat('a', 64),
        ])->assertRedirect(route('checkout.confirmation', ['order' => 1]));

        $this->actingAs($user)->get(route('checkout'))
            ->assertOk()
            ->assertSee('Order via WhatsApp')
            ->assertSee(route('checkout.whatsapp.prepare'), false);

        $this->actingAs($user)->post(route('checkout.store'), [
            'submission_token' => str_repeat('b', 64),
            'items' => [[
                'item_id' => $gift->gift_design_id,
                'item_type' => 'gift',
                'quantity' => 2,
                'price' => '0.01',
            ]],
            'notes' => 'Please wrap as a gift.',
        ])->assertRedirect(route('checkout.confirmation', ['order' => 2]));

        $order = Order::where('submission_token', str_repeat('b', 64))->firstOrFail();
        $this->assertSame($customer->customer_id, $order->customer_id);
        $this->assertSame('bank_transfer', $order->payment_method);
        $this->assertSame('2500.00', $order->paid_amount);
        $this->assertSame('Please wrap as a gift.', $order->attribute);
        $this->assertDatabaseHas('gift_designs', [
            'gift_design_id' => $gift->gift_design_id,
            'stock_quantity' => 5,
            'reserved_quantity' => 3,
        ]);

        $this->actingAs($user)->get(route('orders.show', $order->order_id))->assertOk();
        [$otherUser] = $this->customer('other-buyer@example.com');
        $this->actingAs($otherUser)->get(route('orders.show', $order->order_id))->assertNotFound();
        $this->actingAs($otherUser)->get(route('checkout.confirmation', ['order' => $order->order_id]))->assertNotFound();
    }

    public function test_cart_routes_require_a_verified_customer_identity_and_reject_staff(): void
    {
        $this->get(route('checkout'))->assertRedirect(route('login'));

        $unassignedUser = User::factory()->create();
        $this->actingAs($unassignedUser)->get(route('gift.design'))->assertOk();
        $this->actingAs($unassignedUser)->get(route('checkout'))->assertForbidden();

        $email = 'operator@example.com';
        Staff::create([
            'full_name' => 'Operator',
            'role' => 'staff',
            'email' => $email,
            'phone' => '0771234567',
            'hire_date' => now()->toDateString(),
        ]);
        $staffUser = User::factory()->create(['email' => $email]);

        $this->actingAs($staffUser)->get(route('checkout'))->assertForbidden();
        $this->actingAs($staffUser)->post(route('checkout.store'), [])->assertForbidden();
    }

    private function customer(string $email): array
    {
        $user = User::factory()->create(['email' => $email, 'email_verified_at' => now()]);
        $customer = Customer::create([
            'full_name' => 'Customer',
            'email' => $email,
            'registered_date' => now(),
        ]);

        return [$user, $customer];
    }
}
