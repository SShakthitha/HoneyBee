<?php

namespace Tests\Feature;

use App\Models\GiftDesign;
use App\Models\Staff;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GiftAndFrameCatalogTest extends TestCase
{
    use RefreshDatabase;

    public function test_gift_and_frame_products_are_displayed_in_their_own_sections(): void
    {
        $gift = GiftDesign::create([
            'item_name' => 'Personalised Gift Box',
            'category' => 'Gift',
            'price' => 1500,
            'image' => 'gifts/gift-box.jpg',
        ]);

        $frame = GiftDesign::create([
            'item_name' => 'Custom Wedding Frame',
            'category' => 'Frame',
            'price' => 2500,
            'image' => 'gifts/wedding-frame.jpg',
        ]);

        $response = $this->get(route('gift.design'));

        $response->assertOk()
            ->assertSee($gift->item_name)
            ->assertSee($frame->item_name)
            ->assertSee('storage/'.$gift->image)
            ->assertSee('storage/'.$frame->image);
    }

    public function test_the_admin_gift_form_offers_gift_and_frame_categories(): void
    {
        $email = 'admin@example.com';
        Staff::create([
            'full_name' => 'Test Admin',
            'role' => 'admin',
            'email' => $email,
            'phone' => '0771234567',
            'hire_date' => now()->toDateString(),
        ]);
        $this->actingAs(User::factory()->create(['email' => $email]));

        $this->get(route('admin.gift.create'))
            ->assertOk()
            ->assertSee('value="Gift"', false)
            ->assertSee('value="Frame"', false);
    }
}
