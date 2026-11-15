<?php

namespace Tests\Feature;

use App\Models\Event;
use App\Models\GiftDesign;
use App\Models\LaserWork;
use App\Models\Staff;
use App\Models\User;
use Illuminate\Filesystem\FilesystemAdapter;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Mockery;
use Tests\TestCase;

class ProductImageReplacementTest extends TestCase
{
    use RefreshDatabase;

    public function test_staff_gift_image_is_preserved_when_replacement_storage_fails(): void
    {
        $product = GiftDesign::create([
            'item_name' => 'Existing Gift',
            'category' => 'Gift',
            'price' => '1500.00',
            'image' => 'gifts/existing.jpg',
        ]);
        $user = $this->userWithStaffRole('staff');
        $disk = $this->publicDiskThatFailsToStore('gifts/existing.jpg');

        $this->actingAs($user)
            ->from(route('staff.products.edit', ['gift', $product->gift_design_id]))
            ->put(route('staff.products.update', ['gift', $product->gift_design_id]), [
                'item_name' => 'Existing Gift',
                'category' => 'Gift',
                'price' => '1500.00',
                'image' => UploadedFile::fake()->image('replacement.jpg'),
            ])
            ->assertRedirect(route('staff.products.edit', ['gift', $product->gift_design_id]))
            ->assertSessionHasErrors('image');

        $this->assertSame('gifts/existing.jpg', $product->fresh()->image);
        $this->assertTrue($disk->exists('gifts/existing.jpg'));
    }

    public function test_admin_laser_image_is_preserved_when_replacement_storage_fails(): void
    {
        $product = LaserWork::create([
            'product_name' => 'Existing Laser',
            'laser_type' => 'Engraving',
            'price' => '2500.00',
            'image' => 'laser/existing.jpg',
        ]);
        $user = $this->userWithStaffRole('admin');
        $disk = $this->publicDiskThatFailsToStore('laser/existing.jpg');

        $this->actingAs($user)
            ->from(route('admin.laser.edit', $product->laser_id))
            ->put(route('admin.laser.update', $product->laser_id), [
                'product_name' => 'Existing Laser',
                'laser_type' => 'Engraving',
                'price' => '2500.00',
                'image' => UploadedFile::fake()->image('replacement.jpg'),
            ])
            ->assertRedirect(route('admin.laser.edit', $product->laser_id))
            ->assertSessionHasErrors('image');

        $this->assertSame('laser/existing.jpg', $product->fresh()->image);
        $this->assertTrue($disk->exists('laser/existing.jpg'));
    }

    public function test_admin_event_image_is_preserved_when_replacement_storage_fails(): void
    {
        $product = Event::create([
            'event_name' => 'Existing Event',
            'event_type' => 'Wedding',
            'event_date' => now()->toDateString(),
            'price' => '3500.00',
            'image' => 'events/existing.jpg',
        ]);
        $user = $this->userWithStaffRole('admin');
        $disk = $this->publicDiskThatFailsToStore('events/existing.jpg');

        $this->actingAs($user)
            ->from(route('admin.event.edit', $product->event_id))
            ->put(route('admin.event.update', $product->event_id), [
                'event_name' => 'Existing Event',
                'event_type' => 'Wedding',
                'event_date' => now()->toDateString(),
                'price' => '3500.00',
                'image' => UploadedFile::fake()->image('replacement.jpg'),
            ])
            ->assertRedirect(route('admin.event.edit', $product->event_id))
            ->assertSessionHasErrors('image');

        $this->assertSame('events/existing.jpg', $product->fresh()->image);
        $this->assertTrue($disk->exists('events/existing.jpg'));
    }

    private function userWithStaffRole(string $role): User
    {
        $email = $role.'-image-test@example.com';

        Staff::create([
            'full_name' => ucfirst($role).' Image Test',
            'role' => $role,
            'email' => $email,
            'phone' => '0710000000',
            'hire_date' => now()->toDateString(),
        ]);

        return User::factory()->create(['email' => $email]);
    }

    private function publicDiskThatFailsToStore(string $existingPath): FilesystemAdapter
    {
        Storage::fake('public');
        $disk = Storage::disk('public');
        $disk->put($existingPath, 'existing image');

        $mock = Mockery::mock($disk)->makePartial();
        $mock->shouldReceive('putFileAs')->once()->andReturn(false);
        $mock->shouldReceive('delete')->never();

        Storage::set('public', $mock);

        return $disk;
    }
}
