<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\GiftDesign;
use App\Models\Order;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CartOfferPricingTest extends TestCase
{
    use RefreshDatabase;

    public function test_checkout_uses_the_valid_offer_price_and_normal_price(): void
    {
        $user = User::factory()->create(['email' => 'customer@example.com']);
        Customer::create(['full_name' => 'Customer', 'email' => $user->email, 'registered_date' => now()]);
        $offered = GiftDesign::create(['item_name' => 'Offer Gift', 'category' => 'Gift', 'price' => 3500, 'offer_price' => 2800, 'stock_quantity' => 2]);
        $normal = GiftDesign::create(['item_name' => 'Normal Gift', 'category' => 'Gift', 'price' => 1200, 'stock_quantity' => 2]);

        $this->actingAs($user)->post(route('checkout.store'), [
            'submission_token' => str_repeat('b', 64),
            'items' => [
                ['item_id' => $offered->gift_design_id, 'item_type' => 'gift', 'quantity' => 1],
                ['item_id' => $normal->gift_design_id, 'item_type' => 'gift', 'quantity' => 2],
            ],
            'notes' => 'Gift wrap please',
        ])->assertRedirect(route('checkout.confirmation', ['order' => 1]));

        $order = Order::with('items')->sole();
        $this->assertSame('5200.00', $order->paid_amount);
        $this->assertSame('Gift wrap please', $order->attribute);
        $this->assertSame('2800.00', $order->items->firstWhere('item_id', $offered->gift_design_id)->price);
        $this->assertSame('1200.00', $order->items->firstWhere('item_id', $normal->gift_design_id)->price);
    }

    public function test_checkout_ignores_an_invalid_offer_price(): void
    {
        $user = User::factory()->create(['email' => 'customer@example.com']);
        Customer::create(['full_name' => 'Customer', 'email' => $user->email, 'registered_date' => now()]);
        $product = GiftDesign::create(['item_name' => 'Normal Price Gift', 'category' => 'Gift', 'price' => 3500, 'offer_price' => 0, 'stock_quantity' => 1]);

        $this->actingAs($user)->post(route('checkout.store'), [
            'submission_token' => str_repeat('c', 64),
            'items' => [['item_id' => $product->gift_design_id, 'item_type' => 'gift', 'quantity' => 1]],
        ])->assertRedirect(route('checkout.confirmation', ['order' => 1]));

        $this->assertSame('3500.00', Order::sole()->paid_amount);
    }
}
