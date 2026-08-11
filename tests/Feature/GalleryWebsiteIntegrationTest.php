<?php

namespace Tests\Feature;

use App\Models\GalleryImage;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GalleryWebsiteIntegrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_the_home_gallery_displays_images_from_every_category(): void
    {
        $gift = $this->galleryImage('Gift & Design', 'gallery/gift.jpg');
        $laser = $this->galleryImage('Laser Work', 'gallery/laser.jpg');
        $event = $this->galleryImage('Events', 'gallery/event.jpg');

        $response = $this->get(route('home'));

        $response->assertOk()
            ->assertSee('storage/' . $gift->image)
            ->assertSee('storage/' . $laser->image)
            ->assertSee('storage/' . $event->image);
    }

    public function test_each_category_page_displays_only_its_gallery_images(): void
    {
        $gift = $this->galleryImage('Gift & Design', 'gallery/gift.jpg');
        $laser = $this->galleryImage('Laser Work', 'gallery/laser.jpg');
        $event = $this->galleryImage('Events', 'gallery/event.jpg');

        $this->get(route('gift.design'))
            ->assertOk()
            ->assertSee('storage/' . $gift->image)
            ->assertDontSee('storage/' . $laser->image)
            ->assertDontSee('storage/' . $event->image);

        $this->get(route('laser.work'))
            ->assertOk()
            ->assertSee('storage/' . $laser->image)
            ->assertDontSee('storage/' . $gift->image)
            ->assertDontSee('storage/' . $event->image);

        $this->get(route('events'))
            ->assertOk()
            ->assertSee('storage/' . $event->image)
            ->assertDontSee('storage/' . $gift->image)
            ->assertDontSee('storage/' . $laser->image);
    }

    private function galleryImage(string $category, string $path): GalleryImage
    {
        return GalleryImage::create([
            'title' => $category . ' sample',
            'category' => $category,
            'image' => $path,
        ]);
    }
}
