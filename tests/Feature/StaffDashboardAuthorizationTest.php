<?php

namespace Tests\Feature;

use App\Exports\HoneyBeeExport;
use App\Models\Customer;
use App\Models\Event;
use App\Models\GiftDesign;
use App\Models\LaserWork;
use App\Models\Order;
use App\Models\Staff;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;
use Tests\TestCase;

class StaffDashboardAuthorizationTest extends TestCase
{
    use RefreshDatabase;

    private function staffUser(): User
    {
        $email = 'operator@example.com';
        Staff::create(['full_name' => 'Operations User', 'role' => 'staff', 'email' => $email, 'phone' => '0771234567', 'hire_date' => now()->toDateString()]);

        return User::factory()->create(['email' => $email]);
    }

    public function test_staff_can_access_their_operational_workspace(): void
    {
        $user = $this->staffUser();
        $this->actingAs($user)->get(route('staff.dashboard'))->assertOk();
        $this->actingAs($user)->get(route('staff.products.index'))->assertOk();
        $this->actingAs($user)->get(route('staff.gallery.index'))->assertOk();
    }

    public function test_staff_product_menu_exposes_only_staff_product_routes_and_tracks_the_active_type(): void
    {
        $user = $this->staffUser();
        $this->actingAs($user)->get(route('staff.products.index'))
            ->assertOk()
            ->assertSee('<ul class="staff-products-menu">', false)
            ->assertSee('<details class="sidebar-services" open>', false)
            ->assertSee('css/sidebar-dropdown.css?v=', false)
            ->assertSee(route('staff.products.index'), false)
            ->assertSee(route('staff.products.category', 'gift'), false)
            ->assertSee(route('staff.products.category', 'laser'), false)
            ->assertSee(route('staff.products.category', 'event'), false)
            ->assertSee(route('staff.products.create', 'gift'), false)
            ->assertSee(route('staff.products.create', 'laser'), false)
            ->assertSee(route('staff.products.create', 'event'), false)
            ->assertSee('View Gifts')
            ->assertSee('View Laser Work')
            ->assertSee('View Events')
            ->assertSee('fa-calendar-days', false)
            ->assertSee('fa-wand-magic-sparkles', false)
            ->assertSee('fa-list-check', false)
            ->assertDontSee(route('admin.services.gift'), false);

        $this->actingAs($user)->get(route('staff.products.create', 'laser'))
            ->assertOk()
            ->assertSee('class="active" href="'.route('staff.products.create', 'laser').'"', false);

        $laser = LaserWork::create(['product_name' => 'Menu Laser', 'laser_type' => 'Engraving', 'price' => 1200]);
        $this->actingAs($user)->get(route('staff.products.edit', ['laser', $laser->getKey()]))
            ->assertOk()
            ->assertSee('class="active" href="'.route('staff.products.category', 'laser').'"', false);
    }

    public function test_staff_can_list_add_edit_delete_and_view_each_product_category(): void
    {
        $user = $this->staffUser();
        $definitions = [
            ['gift', GiftDesign::class, 'gift_designs', 'item_name', ['item_name' => 'Test Gift', 'category' => 'Gift', 'price' => 1500]],
            ['laser', LaserWork::class, 'laser_works', 'product_name', ['product_name' => 'Test Laser', 'laser_type' => 'Engraving', 'price' => 2500]],
            ['event', Event::class, 'events', 'event_name', ['event_name' => 'Test Event', 'event_type' => 'Wedding', 'event_date' => now()->toDateString(), 'price' => 3500]],
        ];

        foreach ($definitions as [$type, $class, $table, $nameField, $payload]) {
            $this->actingAs($user)->get(route('staff.products.category', $type))->assertOk();
            $this->actingAs($user)->get(route('staff.products.create', $type))->assertOk();
            $this->actingAs($user)->post(route('staff.products.store', $type), $payload)
                ->assertRedirect(route('staff.products.category', $type));

            $product = $class::query()->latest()->firstOrFail();
            $this->assertDatabaseHas($table, [$nameField => $payload[$nameField]]);
            $this->actingAs($user)->get(route('staff.products.category', $type))
                ->assertOk()
                ->assertSee(route('staff.products.export'), false)
                ->assertSee(route('staff.products.create', $type), false)
                ->assertSee('staff-product-table', false)
                ->assertSee('Select all shown')
                ->assertSee('Original Price')
                ->assertSee('Current Offer')
                ->assertSee('Created')
                ->assertSee($payload[$nameField])
                ->assertSee(route('staff.products.show', [$type, $product->getKey()]), false)
                ->assertSee(route('staff.products.edit', [$type, $product->getKey()]), false)
                ->assertSee(route('staff.products.destroy', [$type, $product->getKey()]), false);
            $this->actingAs($user)->get(route('staff.products.show', [$type, $product->getKey()]))
                ->assertOk()->assertSee($payload[$nameField])->assertSee(route('staff.products.category', $type), false);
            $this->actingAs($user)->get(route('staff.products.edit', [$type, $product->getKey()]))
                ->assertOk()->assertSee('Save changes')->assertSee(route('staff.products.category', $type), false);

            $updatedName = 'Updated '.$payload[$nameField];
            $payload[$nameField] = $updatedName;
            $this->actingAs($user)->put(route('staff.products.update', [$type, $product->getKey()]), $payload)
                ->assertRedirect(route('staff.products.category', $type));
            $this->assertDatabaseHas($table, [$nameField => $updatedName]);

            $this->actingAs($user)->delete(route('staff.products.destroy', [$type, $product->getKey()]))
                ->assertRedirect(route('staff.products.category', $type));
            $this->assertDatabaseMissing($table, [$nameField => $updatedName]);
        }
    }

    public function test_staff_can_export_products_through_a_staff_authorized_route(): void
    {
        Excel::shouldReceive('download')
            ->once()
            ->withArgs(fn ($export, $filename) => $export instanceof HoneyBeeExport && $filename === 'honeybee-staff-products.xlsx')
            ->andReturn(response('spreadsheet'));
        $user = $this->staffUser();

        $this->actingAs($user)->get(route('staff.products.export'))->assertOk();
        $this->actingAs($user)->get(route('admin.exports.products'))->assertForbidden();
    }

    public function test_staff_cannot_access_admin_customer_or_staff_management(): void
    {
        $user = $this->staffUser();
        $this->actingAs($user)->get(route('admin.customers.index'))->assertForbidden();
        $this->actingAs($user)->get(route('admin.customers.edit', 123))->assertForbidden();
        $this->actingAs($user)->delete(route('admin.customers.delete', 123))->assertForbidden();
        $this->actingAs($user)->get(route('admin.staff'))->assertForbidden();
        $this->actingAs($user)->get(route('admin.services.gift'))->assertForbidden();
        $this->actingAs($user)->get(route('admin.gift.edit', 123))->assertForbidden();
        $this->actingAs($user)->post(route('admin.gift.store'))->assertForbidden();
    }

    public function test_non_staff_cannot_access_the_staff_workspace(): void
    {
        $this->actingAs(User::factory()->create())->get(route('staff.dashboard'))->assertForbidden();
    }

    public function test_staff_can_handle_operational_resources_but_cannot_change_role(): void
    {
        Storage::fake('public');
        $user = $this->staffUser();
        $customer = Customer::create(['full_name' => 'Customer', 'email' => 'customer@example.com', 'phone' => '0710000000', 'registered_date' => now()]);
        $order = Order::create(['customer_id' => $customer->customer_id, 'order_date' => now(), 'status' => 'pending']);

        $this->actingAs($user)->put(route('staff.orders.status', $order->order_id), ['status' => 'processing'])->assertRedirect();
        $this->assertDatabaseHas('orders', ['order_id' => $order->order_id, 'status' => 'processing']);

        $this->actingAs($user)->post(route('staff.products.store', 'gift'), ['item_name' => 'Gift box', 'category' => 'Gift', 'price' => 1500])->assertRedirect(route('staff.products.category', 'gift'));
        $this->assertDatabaseHas('gift_designs', ['item_name' => 'Gift box']);

        $this->actingAs($user)->post(route('staff.gallery.store'), ['title' => 'New gallery item', 'category' => 'Events', 'image' => UploadedFile::fake()->image('event.jpg')])->assertRedirect(route('staff.gallery.index'));
        $this->assertDatabaseCount('gallery_images', 1);

        $this->actingAs($user)->put(route('staff.profile.update'), ['full_name' => 'New Name', 'phone' => '0720000000', 'role' => 'admin'])->assertRedirect(route('staff.profile'));
        $this->assertDatabaseHas('staff', ['email' => $user->email, 'full_name' => 'New Name', 'role' => 'staff']);
    }
}
