<?php

namespace Tests\Feature;

use App\Models\Event;
use App\Models\GiftDesign;
use App\Models\LaserWork;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PublicProductDetailsTest extends TestCase
{
    use RefreshDatabase;

    public function test_gift_detail_page_shows_actual_product_fields_and_image(): void
    {
        $gift = GiftDesign::create([
            'item_name' => 'Engraved Gift Box',
            'category' => 'Gift',
            'material' => 'Wood',
            'size' => 'Medium',
            'price' => 2500,
            'description' => 'A personalized box.',
            'image' => 'gifts/engraved-box.jpg',
        ]);

        $this->get(route('gift.design.show', $gift->gift_design_id))
            ->assertOk()
            ->assertSee('Engraved Gift Box')
            ->assertSee('A personalized box.')
            ->assertSee('storage/gifts/engraved-box.jpg');
    }

    public function test_laser_detail_page_shows_actual_product_fields_and_image(): void
    {
        $laser = LaserWork::create([
            'product_name' => 'Laser Cut Plaque',
            'laser_type' => 'Engraving',
            'material_type' => 'Acrylic',
            'price' => 3500,
            'description' => 'Custom cut plaque.',
            'image' => 'laser/plaque.jpg',
        ]);

        $this->get(route('laser.work.show', $laser->laser_id))
            ->assertOk()
            ->assertSee('Laser Cut Plaque')
            ->assertSee('Custom cut plaque.')
            ->assertSee('storage/laser/plaque.jpg');
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
        $laser = LaserWork::create(['product_name' => 'Image Laser', 'laser_type' => 'Cutting', 'price' => 1000, 'image' => 'laser/image-laser.jpg']);
        $event = Event::create(['event_name' => 'Image Event', 'event_type' => 'Wedding', 'event_date' => now()->toDateString(), 'price' => 10000, 'image' => 'events/image-event.jpg']);

        $this->get(route('laser.work'))
            ->assertOk()
            ->assertSee('storage/laser/image-laser.jpg');

        $this->get(route('events'))
            ->assertOk()
            ->assertSee('storage/events/image-event.jpg');
    }
}
