<?php

namespace Tests\Feature\Admin;

use App\Exports\HoneyBeeExport;
use App\Models\Business;
use App\Models\Customer;
use App\Models\Order;
use App\Models\Service;
use App\Models\Staff;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Tests\TestCase;

class ServiceOrderAdminPresentationTest extends TestCase
{
    use RefreshDatabase;

    private function placeServiceOrder(): Order
    {
        $user = User::factory()->create(['email' => 'customer@example.com']);
        Customer::create([
            'full_name' => 'Customer',
            'email' => $user->email,
            'registered_date' => now(),
        ]);
        $business = Business::create([
            'business_name' => 'HoneyBee Shop',
            'contact_email' => 'shop@example.com',
            'phone' => '0771234567',
        ]);
        $service = Service::create([
            'business_id' => $business->business_id,
            'service_name' => 'Custom decoration',
            'service_type' => 'event',
            'description' => 'Custom styling for a celebration.',
            'availability_status' => 'available',
            'price' => 5000,
        ]);

        $this->actingAs($user)->post(route('orders.store'), [
            'submission_token' => str_repeat('s', 64),
            'service_id' => $service->service_id,
            'payment_method' => 'bank_transfer',
        ])->assertRedirect(route('orders.index'));

        return Order::sole();
    }

    private function admin(): User
    {
        Staff::create([
            'full_name' => 'Admin',
            'role' => 'admin',
            'email' => 'admin@example.com',
            'phone' => '0771234567',
            'hire_date' => now()->toDateString(),
        ]);

        return User::factory()->create(['email' => 'admin@example.com']);
    }

    public function test_admin_order_listing_and_details_show_service_order_lines_and_total(): void
    {
        $order = $this->placeServiceOrder();
        $admin = $this->admin();

        $listing = $this->actingAs($admin)->get(route('admin.orders.data'))->assertOk()->json('data.0');
        $this->assertStringContainsString('Custom decoration', $listing['items']);
        $this->assertStringContainsString('Qty: 1 × Rs 5,000.00', $listing['items']);
        $this->assertSame('<strong>Rs 5,000.00</strong>', $listing['total']);

        $this->get(route('admin.orders.show', $order->order_id))
            ->assertOk()
            ->assertSee('Custom decoration')
            ->assertSee('Rs 5,000.00')
            ->assertSee('Order Total');
    }

    public function test_admin_order_export_includes_service_order_as_one_service_line(): void
    {
        $this->placeServiceOrder();
        $admin = $this->admin();
        $temporaryFile = tempnam(sys_get_temp_dir(), 'honeybee-export-');
        file_put_contents($temporaryFile, 'test');

        try {
            Excel::shouldReceive('download')
                ->once()
                ->withArgs(function (HoneyBeeExport $export, string $filename): bool {
                    $rows = $export->collection();

                    return $filename === 'honeybee-orders.xlsx'
                        && $rows->count() === 1
                        && $rows->first()[4] === 'Custom decoration'
                        && $rows->first()[5] === 'service'
                        && $rows->first()[6] === 1
                        && (float) $rows->first()[7] === 5000.0
                        && (float) $rows->first()[8] === 5000.0;
                })
                ->andReturn(new BinaryFileResponse($temporaryFile));

            $this->actingAs($admin)->get(route('admin.exports.orders'))->assertOk();
        } finally {
            @unlink($temporaryFile);
        }
    }
}
