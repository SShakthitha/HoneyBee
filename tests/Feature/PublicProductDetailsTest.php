<?php

namespace Tests\Feature;

use App\Models\Event;
use App\Models\GiftDesign;
use App\Models\LaserWork;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class PublicProductDetailsTest extends TestCase
{
    use RefreshDatabase;

    public function test_gift_detail_page_shows_actual_product_fields_and_image(): void
    {
        Storage::fake('public');
        Storage::disk('public')->put('gifts/engraved-box.jpg', 'gift image');
        $gift = GiftDesign::create([
            'item_name' => 'Engraved Gift Box',
            'category' => 'Gift',
            'material' => 'Wood',
            'size' => 'Medium',
            'price' => 2500,
            'description' => 'A personalized box.',
            'image' => 'gifts/engraved-box.jpg',
            'available_stock' => 37,
        ]);

        $this->get(route('gift.design.show', $gift->gift_design_id))
            ->assertOk()
            ->assertSee('Engraved Gift Box')
            ->assertSee('A personalized box.')
            ->assertSee('storage/gifts/engraved-box.jpg')
            ->assertSee('Order')
            ->assertDontSee('37 available')
            ->assertDontSee('Out of stock');
    }

    public function test_laser_detail_page_shows_actual_product_fields_and_image(): void
    {
        Storage::fake('public');
        Storage::disk('public')->put('laser/plaque.jpg', 'laser image');
        $laser = LaserWork::create([
            'product_name' => 'Laser Cut Plaque',
            'laser_type' => 'Engraving',
            'material_type' => 'Acrylic',
            'price' => 3500,
            'description' => 'Custom cut plaque.',
            'image' => 'laser/plaque.jpg',
            'production_type' => 'ready_made',
            'available_stock' => 43,
        ]);

        $this->get(route('laser.work.show', $laser->laser_id))
            ->assertOk()
            ->assertSee('Laser Cut Plaque')
            ->assertSee('Custom cut plaque.')
            ->assertSee('storage/laser/plaque.jpg')
            ->assertSee('Order')
            ->assertDontSee('43 ready-made units available')
            ->assertDontSee('Out of stock');
    }

    public function test_empty_laser_and_event_catalogues_do_not_show_sample_products(): void
    {
        $this->get(route('laser.work'))
            ->assertOk()
            ->assertSee('No laser products are available yet.')
            ->assertDontSee('Laser Photo Frames');

        $this->get(route('events'))
            ->assertOk()
            ->assertSee('No events are listed yet.')
            ->assertDontSee('Birthday Party Events');
    }

    public function test_laser_and_event_listing_templates_use_uploaded_product_images(): void
    {
        Storage::fake('public');
        Storage::disk('public')->put('laser/image-laser.jpg', 'laser image');
        Storage::disk('public')->put('events/image-event.jpg', 'event image');
        $laser = LaserWork::create(['product_name' => 'Image Laser', 'laser_type' => 'Cutting', 'price' => 1000, 'image' => 'laser/image-laser.jpg']);
        $event = Event::create(['event_name' => 'Image Event', 'event_type' => 'Wedding', 'event_date' => now()->toDateString(), 'price' => 10000, 'image' => 'events/image-event.jpg']);

        $this->get(route('laser.work'))
            ->assertOk()
            ->assertSee('storage/laser/image-laser.jpg');

        $this->get(route('events'))
            ->assertOk()
            ->assertSee('storage/events/image-event.jpg');
    }

    public function test_customer_product_listings_hide_stock_counts_and_availability_labels(): void
    {
        GiftDesign::create(['item_name' => 'Visible Gift', 'category' => 'Gift', 'price' => 1200, 'available_stock' => 37]);
        GiftDesign::create(['item_name' => 'Visible Frame', 'category' => 'Frame', 'price' => 2200, 'available_stock' => 41]);
        LaserWork::create(['product_name' => 'Ready Laser', 'laser_type' => 'Engraving', 'price' => 3200, 'production_type' => 'ready_made', 'available_stock' => 43]);

        $this->get(route('gift.design'))
            ->assertOk()
            ->assertSee('Visible Gift')
            ->assertSee('Visible Frame')
            ->assertSee('Rs 1,200.00')
            ->assertDontSee('37 available')
            ->assertDontSee('41 available')
            ->assertDontSee('Out of stock');

        $this->get(route('laser.work'))
            ->assertOk()
            ->assertSee('Ready Laser')
            ->assertSee('Rs 3,200.00')
            ->assertDontSee('43 ready-made units available')
            ->assertDontSee('Out of stock');
    }

    public function test_stored_image_prefixes_are_normalized_and_missing_files_use_the_fallback(): void
    {
        Storage::fake('public');
        Storage::disk('public')->put('gifts/path-prefixed.jpg', 'present image');
        $present = GiftDesign::create([
            'item_name' => 'Prefixed Image Gift',
            'category' => 'Gift',
            'price' => 900,
            'image' => 'storage/gifts/path-prefixed.jpg',
        ]);
        $missing = GiftDesign::create([
            'item_name' => 'Missing Image Gift',
            'category' => 'Gift',
            'price' => 1000,
            'image' => 'gifts/missing-file.jpg',
        ]);

        $this->get(route('gift.design.show', $present->gift_design_id))
            ->assertOk()
            ->assertSee('storage/gifts/path-prefixed.jpg');
        $this->get(route('gift.design.show', $missing->gift_design_id))
            ->assertOk()
            ->assertSee('Product image not available')
            ->assertDontSee('storage/gifts/missing-file.jpg');
    }
}
