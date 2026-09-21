<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\Event;
use App\Models\GiftDesign;
use App\Models\LaserWork;
use App\Models\Promotion;
use App\Models\Staff;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class PromotionManagementTest extends TestCase
{
    use RefreshDatabase;

    private function adminUser(): User
    {
        Staff::create([
            'full_name' => 'Admin User',
            'role' => 'admin',
            'email' => 'admin@example.com',
            'phone' => '0771234567',
            'hire_date' => now()->toDateString(),
        ]);

        return User::factory()->create(['email' => 'admin@example.com']);
    }

    public function test_admin_can_create_edit_and_delete_promotion(): void
    {
        Storage::fake('public');
        $admin = $this->adminUser();

        $this->actingAs($admin)
            ->post(route('admin.promotions.store'), [
                'title' => 'Spring Special',
                'short_description' => 'Selected gifts',
                'button_text' => 'Shop Now',
                'destination' => 'gift.design',
                'start_date' => now()->subDay()->toDateString(),
                'end_date' => now()->addDay()->toDateString(),
                'is_active' => '1',
                'image' => UploadedFile::fake()->image('promo.jpg'),
            ])
            ->assertRedirect(route('admin.promotions.index'));

        $promotion = Promotion::firstOrFail();
        $this->assertDatabaseHas('promotions', ['title' => 'Spring Special', 'is_active' => true]);

        $this->actingAs($admin)
            ->put(route('admin.promotions.update', $promotion->id), [
                'title' => 'Spring Special Update',
                'short_description' => 'Now with more savings',
                'button_text' => 'See Deals',
                'destination' => 'events',
                'start_date' => now()->subDay()->toDateString(),
                'end_date' => now()->addDays(3)->toDateString(),
                'is_active' => '0',
            ])
            ->assertRedirect(route('admin.promotions.index'));

        $promotion->refresh();
        $this->assertSame('Spring Special Update', $promotion->title);
        $this->assertFalse((bool) $promotion->is_active);

        $this->actingAs($admin)
            ->delete(route('admin.promotions.destroy', $promotion->id))
            ->assertRedirect(route('admin.promotions.index'));

        $this->assertDatabaseMissing('promotions', ['id' => $promotion->id]);
    }

    public function test_only_active_promotions_show_on_homepage(): void
    {
        Promotion::create([
            'title' => 'Active promo',
            'short_description' => 'Current deal',
            'button_text' => 'Shop now',
            'destination' => 'gift.design',
            'start_date' => now()->subDay()->toDateString(),
            'end_date' => now()->addDay()->toDateString(),
            'is_active' => true,
        ]);

        Promotion::create([
            'title' => 'Inactive promo',
            'short_description' => 'Hidden deal',
            'button_text' => 'Shop now',
            'destination' => 'events',
            'start_date' => now()->subDay()->toDateString(),
            'end_date' => now()->addDay()->toDateString(),
            'is_active' => false,
        ]);

        Promotion::create([
            'title' => 'Future promo',
            'short_description' => 'Not yet',
            'button_text' => 'Shop now',
            'destination' => 'laser.work',
            'start_date' => now()->addDay()->toDateString(),
            'end_date' => now()->addDays(2)->toDateString(),
            'is_active' => true,
        ]);

        Promotion::create([
            'title' => 'Expired promo',
            'short_description' => 'No longer active',
            'button_text' => 'Shop now',
            'destination' => 'home',
            'start_date' => now()->subDays(3)->toDateString(),
            'end_date' => now()->subDay()->toDateString(),
            'is_active' => true,
        ]);

        $response = $this->get(route('home'));
        $response->assertOk();
        $response->assertSee('Active promo');
        $response->assertDontSee('Inactive promo');
        $response->assertDontSee('Future promo');
        $response->assertDontSee('Expired promo');
    }

    public function test_customer_cannot_access_promotion_management_routes(): void
    {
        $customer = User::factory()->create(['email' => 'customer@example.com']);
        Customer::create(['full_name' => 'Customer', 'email' => $customer->email, 'registered_date' => now()]);

        $this->actingAs($customer)->get(route('staff.promotions.index'))->assertForbidden();
        $this->actingAs($customer)->get(route('admin.promotions.index'))->assertForbidden();
    }

    public function test_product_offer_price_displays_discount_for_valid_offer(): void
    {
        GiftDesign::create([
            'item_name' => 'Honey Gift',
            'category' => 'Gift',
            'price' => 3500,
            'offer_price' => 2800,
            'description' => 'Test gift',
        ]);

        $response = $this->get(route('gift.design'));
        $response->assertOk();
        $response->assertSee('Offer');
        $response->assertSee('20% OFF');
        $response->assertSee('Rs 2,800.00');
    }

    public function test_invalid_offer_prices_are_not_treated_as_discounts(): void
    {
        $gift = GiftDesign::create(['item_name' => 'Gift', 'category' => 'Gift', 'price' => 3500, 'offer_price' => 3500]);
        $event = Event::create(['event_name' => 'Event', 'event_type' => 'Party', 'event_date' => now(), 'price' => 3500, 'offer_price' => 4000]);
        $laser = LaserWork::create(['product_name' => 'Laser', 'laser_type' => 'Engraving', 'price' => 3500, 'offer_price' => 0]);

        $this->assertFalse($gift->hasValidOfferPrice());
        $this->assertFalse($event->hasValidOfferPrice());
        $this->assertFalse($laser->hasValidOfferPrice());

        $this->get(route('gift.design'))->assertOk()->assertDontSee('Rs 3,500.00</del>', false);
        $this->get(route('events'))->assertOk()->assertDontSee('Rs 3,500.00</del>', false);
        $this->get(route('laser.work'))->assertOk()->assertDontSee('Rs 3,500.00</del>', false);
    }

    public function test_promotion_destination_must_be_a_known_customer_route(): void
    {
        $admin = $this->adminUser();

        $this->actingAs($admin)
            ->post(route('admin.promotions.store'), [
                'title' => 'Unsafe CTA',
                'button_text' => 'Go',
                'destination' => 'admin.promotions.index',
            ])
            ->assertSessionHasErrors('destination');
    }
}
