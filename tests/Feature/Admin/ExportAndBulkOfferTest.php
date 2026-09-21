<?php

namespace Tests\Feature\Admin;

use App\Exports\HoneyBeeExport;
use App\Models\Customer;
use App\Models\Event;
use App\Models\GiftDesign;
use App\Models\LaserWork;
use App\Models\Staff;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Maatwebsite\Excel\Facades\Excel;
use Tests\TestCase;

class ExportAndBulkOfferTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_download_customer_product_order_and_staff_excel_exports(): void
    {
        Excel::fake();
        $admin = $this->admin();
        Customer::create(['full_name' => 'Customer One', 'email' => 'customer@example.com', 'phone' => '0771234567', 'total_spent' => 1000, 'registered_date' => now()]);
        GiftDesign::create(['item_name' => 'Gift One', 'category' => 'Gift', 'price' => 1500]);
        LaserWork::create(['product_name' => 'Laser One', 'laser_type' => 'Engraving', 'price' => 2000]);
        Event::create(['event_name' => 'Event One', 'event_type' => 'Wedding', 'event_date' => now(), 'price' => 5000]);

        foreach (['customers', 'products', 'orders', 'staff'] as $export) {
            $this->actingAs($admin)->get(route("admin.exports.{$export}"))->assertOk();
        }

        Excel::assertDownloaded('honeybee-customers.xlsx', fn (HoneyBeeExport $export) => $export->headings() === ['Customer ID', 'Name', 'Email', 'Phone', 'Address', 'Registration Date', 'Total Spent']);
        Excel::assertDownloaded('honeybee-products.xlsx', fn (HoneyBeeExport $export) => $export->headings()[0] === 'Type');
        Excel::assertDownloaded('honeybee-orders.xlsx', fn (HoneyBeeExport $export) => $export->headings()[0] === 'Order ID');
        Excel::assertDownloaded('honeybee-staff.xlsx', fn (HoneyBeeExport $export) => $export->headings()[0] === 'Staff ID');
    }

    public function test_non_admin_cannot_export_or_manage_bulk_offers(): void
    {
        $user = User::factory()->create();
        $gift = GiftDesign::create(['item_name' => 'Gift One', 'category' => 'Gift', 'price' => 1000]);

        $this->actingAs($user)->get(route('admin.exports.customers'))->assertForbidden();
        $this->actingAs($user)->post(route('admin.bulk-offers.apply'), $this->bulkPayload([$gift->gift_design_id]))->assertForbidden();
    }

    public function test_excel_export_generates_a_real_xlsx_document(): void
    {
        $contents = Excel::raw(
            new HoneyBeeExport(collect([['Example', 1]]), ['Name', 'Value'], fn (array $row) => $row),
            \Maatwebsite\Excel\Excel::XLSX
        );

        $this->assertStringStartsWith('PK', $contents);
    }

    public function test_admin_can_apply_percentage_and_fixed_offers_without_changing_original_price(): void
    {
        $admin = $this->admin();
        $first = GiftDesign::create(['item_name' => 'Gift One', 'category' => 'Gift', 'price' => 5000]);
        $second = GiftDesign::create(['item_name' => 'Gift Two', 'category' => 'Gift', 'price' => 3000]);

        $this->actingAs($admin)->post(route('admin.bulk-offers.apply'), $this->bulkPayload([$first->gift_design_id, $second->gift_design_id], 'percentage', 20))
            ->assertSessionHasNoErrors();

        $this->assertDatabaseHas('gift_designs', ['gift_design_id' => $first->gift_design_id, 'price' => 5000, 'offer_price' => 4000]);
        $this->assertDatabaseHas('gift_designs', ['gift_design_id' => $second->gift_design_id, 'price' => 3000, 'offer_price' => 2400]);

        $this->actingAs($admin)->post(route('admin.bulk-offers.apply'), $this->bulkPayload([$first->gift_design_id], 'fixed', 1000))
            ->assertSessionHasNoErrors();

        $this->assertDatabaseHas('gift_designs', ['gift_design_id' => $first->gift_design_id, 'price' => 5000, 'offer_price' => 4000]);
    }

    public function test_admin_can_apply_and_remove_an_offer_for_an_entire_category(): void
    {
        $admin = $this->admin();
        $gift = GiftDesign::create(['item_name' => 'Gift One', 'category' => 'Gift', 'price' => 1000]);
        $frame = GiftDesign::create(['item_name' => 'Frame One', 'category' => 'Frame', 'price' => 1000]);
        $payload = ['product_type' => 'gift', 'scope' => 'category', 'category' => 'Gift', 'offer_type' => 'percentage', 'offer_value' => 10];

        $this->actingAs($admin)->post(route('admin.bulk-offers.apply'), $payload)->assertSessionHasNoErrors();
        $this->assertDatabaseHas('gift_designs', ['gift_design_id' => $gift->gift_design_id, 'offer_price' => 900]);
        $this->assertDatabaseHas('gift_designs', ['gift_design_id' => $frame->gift_design_id, 'offer_price' => null]);

        $this->actingAs($admin)->delete(route('admin.bulk-offers.remove'), ['product_type' => 'gift', 'scope' => 'category', 'category' => 'Gift'])
            ->assertSessionHasNoErrors();
        $this->assertDatabaseHas('gift_designs', ['gift_design_id' => $gift->gift_design_id, 'offer_price' => null]);
    }

    public function test_bulk_offer_rejects_empty_invalid_and_negative_price_requests(): void
    {
        $admin = $this->admin();
        $gift = GiftDesign::create(['item_name' => 'Gift One', 'category' => 'Gift', 'price' => 100]);

        $this->actingAs($admin)->from(route('admin.services.gift'))->post(route('admin.bulk-offers.apply'), $this->bulkPayload([], 'percentage', 20))
            ->assertSessionHasErrors('product_ids');
        $this->actingAs($admin)->from(route('admin.services.gift'))->post(route('admin.bulk-offers.apply'), $this->bulkPayload([$gift->gift_design_id], 'percentage', 100))
            ->assertSessionHasErrors('offer_value');
        $this->actingAs($admin)->from(route('admin.services.gift'))->post(route('admin.bulk-offers.apply'), $this->bulkPayload([$gift->gift_design_id], 'fixed', 100))
            ->assertSessionHasErrors('offer_value');
        $this->actingAs($admin)->from(route('admin.services.gift'))->post(route('admin.bulk-offers.apply'), $this->bulkPayload([999999], 'percentage', 20))
            ->assertSessionHasErrors('product_ids');
    }

    private function admin(): User
    {
        $user = User::factory()->create(['email' => 'admin@example.com']);
        Staff::create(['full_name' => 'Admin User', 'role' => 'admin', 'email' => $user->email, 'phone' => '0771234567', 'hire_date' => now()]);

        return $user;
    }

    private function bulkPayload(array $ids, string $type = 'percentage', int|float $value = 20): array
    {
        return ['product_type' => 'gift', 'scope' => 'selected', 'product_ids' => $ids, 'offer_type' => $type, 'offer_value' => $value];
    }
}
