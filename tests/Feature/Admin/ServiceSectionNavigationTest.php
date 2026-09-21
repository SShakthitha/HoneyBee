<?php

namespace Tests\Feature\Admin;

use App\Models\Event;
use App\Models\GiftDesign;
use App\Models\LaserWork;
use App\Models\Staff;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
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
        $admin = $this->admin();
        GiftDesign::create(['item_name' => 'Gift Only', 'category' => 'Gift', 'price' => 100]);
        LaserWork::create(['product_name' => 'Laser Only', 'laser_type' => 'Engraving', 'price' => 200]);
        Event::create(['event_name' => 'Event Only', 'event_type' => 'Wedding', 'event_date' => now(), 'price' => 300]);

        $this->actingAs($admin)->get(route('admin.services.gift'))->assertOk()->assertSee('Gift Only')->assertDontSee('Laser Only')->assertDontSee('Event Only')->assertSee(route('admin.gift.create'), false);
        $this->actingAs($admin)->get(route('admin.services.events'))->assertOk()->assertSee('Event Only')->assertDontSee('Gift Only')->assertDontSee('Laser Only')->assertSee(route('admin.event.create'), false);
        $this->actingAs($admin)->get(route('admin.services.laser'))->assertOk()->assertSee('Laser Only')->assertDontSee('Gift Only')->assertDontSee('Event Only')->assertSee(route('admin.laser.create'), false);
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
