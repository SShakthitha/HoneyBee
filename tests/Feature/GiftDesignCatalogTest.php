<?php

namespace Tests\Feature;

use App\Models\GalleryImage;
use App\Models\GiftDesign;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class GiftDesignCatalogTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        putenv('DB_CONNECTION=sqlite');
        putenv('DB_DATABASE=:memory:');

        parent::setUp();
    }

    public function test_gifts_and_frames_render_in_separate_sections_with_prices(): void
    {
        GiftDesign::create([
            'item_name' => 'Personalised Gift',
            'category' => 'Gift',
            'price' => 2500,
        ]);
        GiftDesign::create([
            'item_name' => 'Couple Photo Frame',
            'category' => 'Frame',
            'price' => 3200,
        ]);

        $this->get(route('gift.design'))
            ->assertOk()
            ->assertSeeInOrder([
                'Our Gift Collection',
                'Personalised Gift',
                'Frame Collection',
                'Couple Photo Frame',
            ])
            ->assertSee('Rs. 2,500.00')
            ->assertSee('Rs. 3,200.00');
    }

    public function test_gallery_frame_is_copied_into_a_frame_product(): void
    {
        Storage::fake('public');
        Storage::disk('public')->put('gallery/frame-design.jpg', 'test image content');

        $galleryImage = GalleryImage::create([
            'title' => 'Spotify Code Frame',
            'category' => 'Frame Designs',
            'image' => 'gallery/frame-design.jpg',
        ]);

        $this->get(route('admin.gift.create', ['gallery_image_id' => $galleryImage->id]))
            ->assertOk()
            ->assertSee('Selected gallery image will be used.')
            ->assertSee('Spotify Code Frame');

        $this->from(route('admin.gift.create', ['gallery_image_id' => $galleryImage->id]))
            ->post(route('admin.gift.store'), [
                'gallery_image_id' => $galleryImage->id,
                'item_name' => 'Spotify Code Frame',
                'category' => 'Gift',
                'price' => 4500,
            ])
            ->assertRedirect('/admin/services');

        $product = GiftDesign::where('item_name', 'Spotify Code Frame')->firstOrFail();

        $this->assertSame('Frame', $product->category);
        $this->assertEquals(4500, $product->price);
        Storage::disk('public')->assertExists($product->image);
    }
}
