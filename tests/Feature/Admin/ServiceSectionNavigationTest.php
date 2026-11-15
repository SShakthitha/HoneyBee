<?php

namespace Tests\Feature\Admin;

use App\Models\Event;
use App\Models\GiftDesign;
use App\Models\LaserWork;
use App\Models\Staff;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ServiceSectionNavigationTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): User
    {
        $email = 'admin@example.com';
        Staff::create(['full_name' => 'Admin', 'role' => 'admin', 'email' => $email, 'phone' => '0771234567', 'hire_date' => now()->toDateString()]);

        return User::factory()->create(['email' => $email]);
    }

    public function test_each_service_route_shows_only_its_existing_product_type(): void
    {
        Storage::fake('public');
        $admin = $this->admin();
        Storage::disk('public')->put('gifts/gift.jpg', 'gift image');
        Storage::disk('public')->put('laser/laser.jpg', 'laser image');
        Storage::disk('public')->put('events/event.jpg', 'event image');
        GiftDesign::create(['item_name' => 'Gift Only', 'category' => 'Gift', 'price' => 100, 'image' => 'gifts/gift.jpg']);
        LaserWork::create(['product_name' => 'Laser Only', 'laser_type' => 'Engraving', 'price' => 200, 'image' => 'laser/laser.jpg']);
        Event::create(['event_name' => 'Event Only', 'event_type' => 'Wedding', 'event_date' => now(), 'price' => 300, 'image' => 'events/event.jpg']);

        $this->actingAs($admin)->get(route('admin.services.gift'))->assertOk()->assertSee('Gift Only')->assertSee('storage/gifts/gift.jpg')->assertDontSee('Laser Only')->assertDontSee('Event Only')->assertSee(route('admin.gift.create'), false);
        $this->actingAs($admin)->get(route('admin.services.events'))->assertOk()->assertSee('Event Only')->assertSee('storage/events/event.jpg')->assertDontSee('Gift Only')->assertDontSee('Laser Only')->assertSee(route('admin.event.create'), false);
        $this->actingAs($admin)->get(route('admin.services.laser'))->assertOk()->assertSee('Laser Only')->assertSee('storage/laser/laser.jpg')->assertDontSee('Gift Only')->assertDontSee('Event Only')->assertSee(route('admin.laser.create'), false);
    }

    public function test_admin_can_upload_laser_and_event_images_and_preview_them_for_editing(): void
    {
        Storage::fake('public');
        $admin = $this->admin();

        $this->actingAs($admin)->get(route('admin.laser.create'))
            ->assertOk()->assertSee('enctype="multipart/form-data"', false)
            ->assertSee('name="image"', false)->assertSee('laser-image-preview', false);
        $this->actingAs($admin)->post(route('admin.laser.store'), [
            'product_name' => 'Laser Upload', 'laser_type' => 'Engraving', 'price' => 900,
            'image' => UploadedFile::fake()->image('laser.jpg'),
        ])->assertRedirect(route('admin.services.laser'));
        $laser = LaserWork::where('product_name', 'Laser Upload')->firstOrFail();
        $this->assertStringStartsWith('laser/', $laser->image);
        Storage::disk('public')->assertExists($laser->image);
        $this->actingAs($admin)->get(route('admin.laser.edit', $laser->laser_id))
            ->assertOk()->assertSee('Current image for Laser Upload')->assertSee('name="image"', false);

        $this->actingAs($admin)->get(route('admin.event.create'))
            ->assertOk()->assertSee('enctype="multipart/form-data"', false)
            ->assertSee('name="image"', false)->assertSee('event-image-preview', false);
        $this->actingAs($admin)->post(route('admin.event.store'), [
            'event_name' => 'Event Upload', 'event_type' => 'Wedding', 'event_date' => now()->toDateString(), 'price' => 1100,
            'image' => UploadedFile::fake()->image('event.jpg'),
        ])->assertRedirect(route('admin.services.events'));
        $event = Event::where('event_name', 'Event Upload')->firstOrFail();
        $this->assertStringStartsWith('events/', $event->image);
        Storage::disk('public')->assertExists($event->image);
        $this->actingAs($admin)->get(route('admin.event.edit', $event->event_id))
            ->assertOk()->assertSee('Current image for Event Upload')->assertSee('name="image"', false);
    }

    public function test_legacy_combined_services_route_redirects_to_gift_and_design(): void
    {
        $this->actingAs($this->admin())->get(route('admin.services'))->assertRedirect(route('admin.services.gift'));
    }

    public function test_non_admin_users_cannot_access_service_sections(): void
    {
        $staffEmail = 'staff@example.com';
        Staff::create(['full_name' => 'Staff', 'role' => 'staff', 'email' => $staffEmail, 'phone' => '0771234567', 'hire_date' => now()->toDateString()]);
        $staff = User::factory()->create(['email' => $staffEmail]);

        $this->actingAs($staff)->get(route('admin.services.gift'))->assertForbidden();
        $this->actingAs(User::factory()->create())->get(route('admin.services.events'))->assertForbidden();
    }
}
