<?php

namespace Tests\Feature\Admin;

use App\Models\GalleryImage;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class GalleryImageManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_an_admin_can_upload_a_gallery_image_to_the_public_disk(): void
    {
        Storage::fake('public');

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

    public function test_deleting_a_gallery_image_removes_its_public_file_and_database_record(): void
    {
        Storage::fake('public');
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
