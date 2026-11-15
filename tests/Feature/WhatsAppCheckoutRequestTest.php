<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\GiftDesign;
use App\Models\Staff;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WhatsAppCheckoutRequestTest extends TestCase
{
    use RefreshDatabase;

    public function test_checkout_button_opens_a_server_priced_whatsapp_request_without_saving_an_order(): void
    {
        [$user, $customer] = $this->customer();
        $gift = GiftDesign::create([
            'item_name' => 'Honey Gift', 'category' => 'Gift', 'price' => '1250.00', 'stock_quantity' => 5,
        ]);
        $frame = GiftDesign::create([
            'item_name' => 'Wedding Frame', 'category' => 'Frames', 'price' => '3300.00', 'stock_quantity' => 4,
        ]);

        $this->actingAs($user)->get(route('checkout'))
            ->assertOk()
            ->assertSee('Order via WhatsApp')
            ->assertSee(route('checkout.whatsapp.prepare'), false)
            ->assertSee('No order is saved and no stock is checked or reserved');

        $this->actingAs($user)->get(route('gift.design'))
            ->assertOk()
            ->assertSee('id="whatsapp-cart-form"', false)
            ->assertSee('target="honeybee-whatsapp-request"', false)
            ->assertSee('Order via WhatsApp')
            ->assertDontSee('Proceed to Checkout');

        $response = $this->actingAs($user)->post(route('checkout.whatsapp.prepare'), [
            'items' => [
                ['item_id' => $gift->gift_design_id, 'item_type' => 'gift', 'quantity' => 2, 'price' => '0.01'],
                ['item_id' => $frame->gift_design_id, 'item_type' => 'frame', 'quantity' => 1, 'price' => '0.01'],
            ],
            'notes' => 'Please use blue wrapping.',
        ])->assertOk()->assertSee('WhatsApp request prepared')->assertSee('Open WhatsApp if it did not open');

        $url = $response->viewData('whatsAppUrl');
        $this->assertStringStartsWith('https://wa.me/'.config('honeybee.contact.whatsapp_number').'?text=', $url);
        parse_str((string) parse_url($url, PHP_URL_QUERY), $query);
        $message = $query['text'] ?? '';

        $this->assertStringContainsString('Customer: '.$customer->full_name, $message);
        $this->assertStringContainsString('Phone: '.$customer->phone, $message);
        $this->assertStringContainsString('Email: '.$customer->email, $message);
        $this->assertStringContainsString('Gift: Honey Gift | Quantity: 2 | Unit price: Rs. 1250 | Subtotal: Rs. 2500', $message);
        $this->assertStringContainsString('Frame: Wedding Frame | Quantity: 1 | Unit price: Rs. 3300 | Subtotal: Rs. 3300', $message);
        $this->assertStringContainsString('Total: Rs. 5800', $message);
        $this->assertStringContainsString('Customer notes: Please use blue wrapping.', $message);
        $this->assertStringContainsString('Order reference: Not assigned', $message);
        $this->assertStringContainsString('Stock has not been checked or reserved', $message);
        $this->assertStringContainsString('Bank transfer only', $message);
        $this->assertDatabaseCount('orders', 0);
        $this->assertDatabaseCount('order_items', 0);
    }

    public function test_repeated_whatsapp_requests_do_not_create_duplicate_orders(): void
    {
        [$user] = $this->customer();
        $gift = GiftDesign::create([
            'item_name' => 'Repeat Gift', 'category' => 'Gift', 'price' => '250.00', 'stock_quantity' => 5,
        ]);
        $payload = ['items' => [[
            'item_id' => $gift->gift_design_id,
            'item_type' => 'gift',
            'quantity' => 1,
        ]]];

        $this->actingAs($user)->post(route('checkout.whatsapp.prepare'), $payload)->assertOk();
        $this->post(route('checkout.whatsapp.prepare'), $payload)->assertOk();

        $this->assertDatabaseCount('orders', 0);
        $this->assertDatabaseCount('order_items', 0);
    }

    public function test_empty_missing_and_wrong_category_items_are_rejected(): void
    {
        [$user] = $this->customer();
        $gift = GiftDesign::create([
            'item_name' => 'Gift', 'category' => 'Gift', 'price' => '100.00', 'stock_quantity' => 2,
        ]);

        $this->actingAs($user)->from(route('checkout'))->post(route('checkout.whatsapp.prepare'), [])
            ->assertRedirect(route('checkout'))->assertSessionHasErrors('items');

        $this->from(route('checkout'))->post(route('checkout.whatsapp.prepare'), [
            'items' => [['item_id' => 999999, 'item_type' => 'gift', 'quantity' => 1]],
        ])->assertRedirect(route('checkout'))->assertSessionHasErrors('items');

        $this->from(route('checkout'))->post(route('checkout.whatsapp.prepare'), [
            'items' => [['item_id' => $gift->gift_design_id, 'item_type' => 'frame', 'quantity' => 1]],
        ])->assertRedirect(route('checkout'))->assertSessionHasErrors('items');

        $this->assertDatabaseCount('orders', 0);
        $this->assertDatabaseCount('order_items', 0);
    }

    public function test_guest_and_staff_cannot_prepare_customer_checkout_requests(): void
    {
        $this->post(route('checkout.whatsapp.prepare'), [])->assertRedirect(route('login'));

        $email = 'request-staff@example.com';
        Staff::create([
            'full_name' => 'Request Staff', 'role' => 'staff', 'email' => $email,
            'phone' => '0771234567', 'hire_date' => now()->toDateString(),
        ]);
        $staff = User::factory()->create(['email' => $email]);

        $this->actingAs($staff)->post(route('checkout.whatsapp.prepare'), [])->assertForbidden();
        $this->assertDatabaseCount('orders', 0);
    }

    private function customer(): array
    {
        $email = 'whatsapp-request@example.com';
        $user = User::factory()->create(['email' => $email, 'email_verified_at' => now()]);
        $customer = Customer::create([
            'full_name' => 'HoneyBee Buyer',
            'email' => $email,
            'phone' => '0775550123',
            'registered_date' => now(),
        ]);

        return [$user, $customer];
    }
}
