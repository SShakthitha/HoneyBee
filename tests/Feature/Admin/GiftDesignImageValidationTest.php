<?php

namespace Tests\Feature\Admin;

use App\Models\GiftDesign;
use App\Models\Staff;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class GiftDesignImageValidationTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_upload_validated_image_to_generated_public_gift_path(): void
    {
        Storage::fake('public');
        $admin = $this->admin();

        $this->actingAs($admin)->post(route('admin.gift.store'), $this->productData([
            'image' => UploadedFile::fake()->image('approved.webp', 120, 120)->size(256),
        ]))->assertRedirect(route('admin.services.gift'));

        $gift = GiftDesign::sole();
        $this->assertStringStartsWith('gifts/', $gift->image);
        $this->assertStringNotContainsString('..', $gift->image);
        Storage::disk('public')->assertExists($gift->image);
    }

    public function test_invalid_or_corrupt_image_content_is_rejected(): void
    {
        Storage::fake('public');
        $admin = $this->admin();

        $this->actingAs($admin)
            ->from(route('admin.gift.create'))
            ->post(route('admin.gift.store'), $this->productData([
                'image' => UploadedFile::fake()->createWithContent('corrupt.jpg', 'this is not an image'),
            ]))
            ->assertRedirect(route('admin.gift.create'))
            ->assertSessionHasErrors('image');

        $this->assertDatabaseCount('gift_designs', 0);
        $this->assertSame([], Storage::disk('public')->allFiles());
    }

    public function test_uploads_over_ten_megabytes_are_rejected(): void
    {
        Storage::fake('public');
        $admin = $this->admin();

        $this->actingAs($admin)
            ->from(route('admin.gift.create'))
            ->post(route('admin.gift.store'), $this->productData([
                'image' => UploadedFile::fake()->image('too-large.jpg', 1, 1)->size(10241),
            ]))
            ->assertRedirect(route('admin.gift.create'))
            ->assertSessionHasErrors('image');

        $this->assertDatabaseCount('gift_designs', 0);
    }

    public function test_invalid_replacement_preserves_existing_image_path_and_file(): void
    {
        Storage::fake('public');
        Storage::disk('public')->put('gifts/existing-approved.jpg', 'existing image bytes');
        $gift = GiftDesign::create([
            'item_name' => 'Existing Gift',
            'category' => 'Gift',
            'price' => '1500.00',
            'image' => 'gifts/existing-approved.jpg',
        ]);

        $this->actingAs($this->admin())
            ->from(route('admin.gift.edit', $gift->gift_design_id))
            ->put(route('admin.gift.update', $gift->gift_design_id), $this->productData([
                'image' => UploadedFile::fake()->createWithContent('invalid.png', 'invalid image payload'),
            ]))
            ->assertRedirect(route('admin.gift.edit', $gift->gift_design_id))
            ->assertSessionHasErrors('image');

        $this->assertSame('gifts/existing-approved.jpg', $gift->fresh()->image);
        Storage::disk('public')->assertExists('gifts/existing-approved.jpg');
    }

    private function productData(array $overrides = []): array
    {
        return array_merge([
            'item_name' => 'SQLite Image Validation Test',
            'category' => 'Gift',
            'price' => '1500.00',
        ], $overrides);
    }

    private function admin(): User
    {
        $email = 'image-admin-'.uniqid().'@example.com';
        Staff::create([
            'full_name' => 'SQLite Image Admin',
            'role' => 'admin',
            'email' => $email,
            'phone' => '0700000000',
            'hire_date' => now()->toDateString(),
        ]);

        return User::factory()->create(['email' => $email]);
    }
}
