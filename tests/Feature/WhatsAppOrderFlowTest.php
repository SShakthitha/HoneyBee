<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\GiftDesign;
use App\Models\Order;
use App\Models\Staff;
use App\Models\User;
use App\Models\WhatsAppSetting;
use App\Services\WhatsAppOrderMessageBuilder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WhatsAppOrderFlowTest extends TestCase
{
    use RefreshDatabase;

    private function customer(string $email = 'customer@example.com'): User
    {
        $user = User::factory()->create(['email' => $email, 'name' => 'Shakthitha']);
        Customer::create(['full_name' => 'Shakthitha', 'email' => $email, 'registered_date' => now()]);

        return $user;
    }

    private function admin(): User
    {
        $user = User::factory()->create(['email' => 'admin@example.com']);
        Staff::create(['full_name' => 'Admin', 'role' => 'admin', 'email' => $user->email, 'phone' => '0771234567', 'hire_date' => now()]);

        return $user;
    }

    public function test_admin_can_view_and_update_whatsapp_settings(): void
    {
        $admin = $this->admin();
        $this->actingAs($admin)->get(route('admin.settings.whatsapp.edit'))
            ->assertOk()->assertSee('WhatsApp Order Settings');

        $this->actingAs($admin)->put(route('admin.settings.whatsapp.update'), [
            'order_number' => '+94 76-715 8873',
            'message_template' => 'Order #{order_id}: {customer_name} {items} Rs. {total}',
        ])->assertRedirect(route('admin.settings.whatsapp.edit'));

        $this->assertDatabaseHas('whats_app_settings', [
            'order_number' => '94767158873',
            'message_template' => 'Order #{order_id}: {customer_name} {items} Rs. {total}',
        ]);
    }

    public function test_non_admin_cannot_access_or_update_whatsapp_settings(): void
    {
        $customer = $this->customer();
        $this->actingAs($customer)->get(route('admin.settings.whatsapp.edit'))->assertForbidden();
        $this->actingAs($customer)->put(route('admin.settings.whatsapp.update'), [
            'order_number' => '94767158873', 'message_template' => 'Hello',
        ])->assertForbidden();
    }

    public function test_settings_reject_invalid_number_empty_message_and_unknown_placeholder(): void
    {
        $admin = $this->admin();
        $response = $this->actingAs($admin)->from(route('admin.settings.whatsapp.edit'))->put(route('admin.settings.whatsapp.update'), [
            'order_number' => 'not-a-number', 'message_template' => '',
        ]);
        $response->assertRedirect(route('admin.settings.whatsapp.edit'))->assertSessionHasErrors(['order_number', 'message_template']);

        $this->actingAs($admin)->from(route('admin.settings.whatsapp.edit'))->put(route('admin.settings.whatsapp.update'), [
            'order_number' => '94767158873', 'message_template' => 'Hello {unknown}',
        ])->assertSessionHasErrors('message_template');
    }

    public function test_order_is_committed_before_its_owner_can_view_confirmation(): void
    {
        $customer = $this->customer();
        $product = GiftDesign::create(['item_name' => 'Gift & Frame', 'category' => 'Gift', 'price' => 3500]);

        $this->actingAs($customer)->post(route('checkout.store'), [
            'items' => [['item_id' => $product->gift_design_id, 'item_type' => 'gift', 'quantity' => 2]],
        ])->assertRedirect(route('checkout.confirmation', ['order' => 1]));

        $order = Order::with('items')->sole();
        $this->assertSame('7000.00', $order->paid_amount);
        $this->assertCount(1, $order->items);
        $this->assertSame('7000.00', $order->items->first()->subtotal);
        $this->actingAs($customer)->get(route('checkout.confirmation', $order))->assertOk()->assertSee('Your order ID is <strong>#1</strong>', false);
    }

    public function test_confirmation_cannot_be_viewed_by_another_customer(): void
    {
        $owner = $this->customer();
        $other = $this->customer('other@example.com');
        $order = Order::create(['customer_id' => Customer::where('email', $owner->email)->value('customer_id'), 'order_date' => now()]);

        $this->actingAs($other)->get(route('checkout.confirmation', $order))->assertNotFound();
    }

    public function test_failed_checkout_does_not_create_an_order_or_a_whatsapp_action(): void
    {
        $customer = $this->customer();

        $this->actingAs($customer)->post(route('checkout.store'), [
            'items' => [['item_id' => 999999, 'item_type' => 'gift', 'quantity' => 1]],
        ])->assertNotFound();

        $this->assertDatabaseCount('orders', 0);
        $this->assertDatabaseCount('order_items', 0);
    }

    public function test_message_builder_uses_configured_number_replaces_placeholders_and_encodes_url(): void
    {
        $user = $this->customer();
        $order = Order::create(['customer_id' => Customer::where('email', $user->email)->value('customer_id'), 'order_date' => now(), 'paid_amount' => 7500]);
        $order->items()->create(['item_type' => 'gift', 'item_name' => 'Gift & Box', 'price' => 3750, 'quantity' => 2, 'subtotal' => 7500]);
        $settings = WhatsAppSetting::current();
        $settings->update(['order_number' => '94770000000', 'message_template' => "New order #{order_id}\nCustomer: {customer_name}\n{items}\nTotal: {total}"]);

        $builder = app(WhatsAppOrderMessageBuilder::class);
        $message = $builder->messageFor($order->fresh(['customer', 'items']));
        $url = $builder->urlFor($order->fresh(['customer', 'items']));

        $this->assertStringContainsString('New order #'.$order->order_id, $message);
        $this->assertStringContainsString('Shakthitha', $message);
        $this->assertStringContainsString('Gift & Box × 2 — Rs. 7500', $message);
        $this->assertStringContainsString('Total: 7500', $message);
        $this->assertStringNotContainsString('{items}', $message);
        $this->assertStringStartsWith('https://wa.me/94770000000?text=', $url);
        $this->assertStringContainsString('Gift%20%26%20Box', $url);
    }

    public function test_missing_whatsapp_setting_does_not_prevent_order_confirmation(): void
    {
        $customer = $this->customer();
        $order = Order::create(['customer_id' => Customer::where('email', $customer->email)->value('customer_id'), 'order_date' => now()]);
        WhatsAppSetting::query()->delete();

        $this->actingAs($customer)->get(route('checkout.confirmation', $order))
            ->assertOk()->assertSee('WhatsApp ordering is currently unavailable');
    }
}
