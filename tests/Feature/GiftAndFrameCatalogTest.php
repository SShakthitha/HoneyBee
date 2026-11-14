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

    public function test_legacy_and_cased_category_names_are_grouped_correctly(): void
    {
        GiftDesign::create([
            'item_name' => 'Legacy Gift Design',
            'category' => 'Gift & Design',
            'price' => 1800,
        ]);

        GiftDesign::create([
            'item_name' => 'Legacy Frame Design',
            'category' => 'Frame Designs',
            'price' => 2600,
        ]);

        $response = $this->get(route('gift.design'));

        $response->assertOk()
            ->assertSeeInOrder([
                'Our Gift Collection',
                'Legacy Gift Design',
                'Frame Collection',
                'Legacy Frame Design',
            ]);
    }

    public function test_empty_catalog_does_not_display_demo_products(): void
    {
        $response = $this->get(route('gift.design'));

        $response->assertOk()
            ->assertSee('Gift products will be added here soon.')
            ->assertDontSee('Birthday Gifts')
            ->assertDontSee('https://images.unsplash.com');
    }

    public function test_empty_gallery_sections_do_not_display_demo_images(): void
    {
        $response = $this->get(route('gift.design'));

        $response->assertOk()
            ->assertSee('Our latest creations will be added here soon.')
            ->assertSee('Frame design pictures will be added here soon.')
            ->assertDontSee('https://images.unsplash.com');
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
