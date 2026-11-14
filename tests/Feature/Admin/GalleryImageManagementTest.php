<?php

namespace Tests\Feature\Admin;

use App\Models\GalleryImage;
use App\Models\GiftDesign;
use App\Models\Staff;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class GalleryImageManagementTest extends TestCase
{
    use RefreshDatabase;

    private function actingAsAdmin(): void
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
    }

    public function test_an_admin_can_upload_a_gallery_image_to_the_public_disk(): void
    {
        Storage::fake('public');
        $this->actingAsAdmin();

        $response = $this->post(route('admin.gallery.store'), [
            'title' => 'Custom Wedding Board',
            'category' => 'Gift & Design',
            'image' => UploadedFile::fake()->image('wedding-board.jpg'),
        ]);

        $response->assertRedirect(route('admin.gallery.index'));

        $image = GalleryImage::firstOrFail();

        $this->assertSame('Custom Wedding Board', $image->title);
        $this->assertSame('Gift & Design', $image->category);
        $this->assertStringStartsWith('gallery/', $image->image);
        Storage::disk('public')->assertExists($image->image);
    }

    public function test_a_gallery_image_requires_a_valid_category_and_image_file(): void
    {
        $this->actingAsAdmin();

        $response = $this->from(route('admin.gallery.create'))
            ->post(route('admin.gallery.store'), [
                'title' => 'Invalid upload',
                'category' => 'Other',
                'image' => UploadedFile::fake()->create('notes.pdf', 20, 'application/pdf'),
            ]);

        $response->assertRedirect(route('admin.gallery.create'));
        $response->assertSessionHasErrors(['category', 'image']);
        $this->assertDatabaseCount('gallery_images', 0);
    }

    public function test_an_admin_can_add_a_gift_gallery_image_to_the_gift_collection_with_a_price(): void
    {
        Storage::fake('public');
        $this->actingAsAdmin();
        Storage::disk('public')->put('gallery/gift-box.jpg', 'image contents');

        $galleryImage = GalleryImage::create([
            'title' => 'Personalised Gift Box',
            'category' => 'Gift & Design',
            'image' => 'gallery/gift-box.jpg',
        ]);

        $this->get(route('admin.gallery.index'))
            ->assertOk()
            ->assertSee(route('admin.gift.create', ['gallery_image_id' => $galleryImage->id]), false)
            ->assertSee('Add to Gifts');

        $this->get(route('admin.gift.create', ['gallery_image_id' => $galleryImage->id]))
            ->assertOk()
            ->assertSee('Personalised Gift Box')
            ->assertSee('name="gallery_image_id" value="'.$galleryImage->id.'"', false);

        $this->post(route('admin.gift.store'), [
            'gallery_image_id' => $galleryImage->id,
            'item_name' => 'Personalised Gift Box',
            'category' => 'Gift',
            'price' => 2750,
        ])->assertRedirect(route('admin.services.gift'));

        $gift = GiftDesign::sole();

        $this->assertSame('Personalised Gift Box', $gift->item_name);
        $this->assertSame(2750.0, (float) $gift->price);
        $this->assertStringStartsWith('gifts/', $gift->image);
        Storage::disk('public')->assertExists($gift->image);

        $this->get(route('gift.design'))
            ->assertOk()
            ->assertSee('Personalised Gift Box')
            ->assertSee('2,750.00');
    }

    public function test_an_admin_can_add_a_frame_gallery_image_to_the_frame_collection_with_a_price(): void
    {
        Storage::fake('public');
        $this->actingAsAdmin();
        Storage::disk('public')->put('gallery/photo-frame.jpg', 'image contents');

        $galleryImage = GalleryImage::create([
            'title' => 'Personalised Photo Frame',
            'category' => 'Frame Designs',
            'image' => 'gallery/photo-frame.jpg',
        ]);

        $this->get(route('admin.gallery.index'))
            ->assertOk()
            ->assertSee('Add to Frames');

        $this->get(route('admin.gift.create', ['gallery_image_id' => $galleryImage->id]))
            ->assertOk()
            ->assertSee('value="Frame" selected', false);

        $this->post(route('admin.gift.store'), [
            'gallery_image_id' => $galleryImage->id,
            'item_name' => 'Personalised Photo Frame',
            'category' => 'Frame',
            'price' => 3200,
        ])->assertRedirect(route('admin.services.gift'));

        $frame = GiftDesign::sole();

        $this->assertSame('Frame', $frame->category);
        $this->assertSame(3200.0, (float) $frame->price);
        $this->assertStringStartsWith('gifts/', $frame->image);
        Storage::disk('public')->assertExists($frame->image);

        $this->get(route('gift.design'))
            ->assertOk()
            ->assertSee('Personalised Photo Frame')
            ->assertSee('3,200.00');
    }

    public function test_deleting_a_gallery_image_removes_its_public_file_and_database_record(): void
    {
        Storage::fake('public');
        $this->actingAsAdmin();
        Storage::disk('public')->put('gallery/remove-me.jpg', 'image contents');

        $image = GalleryImage::create([
            'title' => 'Remove me',
            'category' => 'Events',
            'image' => 'gallery/remove-me.jpg',
        ]);

        $response = $this->delete(route('admin.gallery.delete', $image));

        $response->assertRedirect(route('admin.gallery.index'));
        $this->assertModelMissing($image);
        Storage::disk('public')->assertMissing('gallery/remove-me.jpg');
    }
}
