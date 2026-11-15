<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\Staff;
use App\Models\User;
use Database\Seeders\HoneyBeeAccountsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class HoneyBeeAccountsSeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_account_seeder_is_repeatable_and_stores_hashed_passwords(): void
    {
        $values = [
            'HONEYBEE_ADMIN_PASSWORD' => 'admin-seed-test-password',
            'HONEYBEE_CUSTOMER_PASSWORD' => 'customer-seed-test-password',
            'HONEYBEE_STAFF_PASSWORD' => 'staff-seed-test-password',
        ];
        $previous = [];

        foreach ($values as $key => $value) {
            $previous[$key] = getenv($key);
            putenv($key.'='.$value);
            $_ENV[$key] = $value;
            $_SERVER[$key] = $value;
        }

        try {
            app(HoneyBeeAccountsSeeder::class)->run();
            app(HoneyBeeAccountsSeeder::class)->run();
        } finally {
            foreach ($previous as $key => $value) {
                if ($value === false) {
                    putenv($key);
                    unset($_ENV[$key], $_SERVER[$key]);
                } else {
                    putenv($key.'='.$value);
                    $_ENV[$key] = $value;
                    $_SERVER[$key] = $value;
                }
            }
        }

        $admin = User::where('email', 'sivarajahshakthitha25@gmail.com')->firstOrFail();
        $customer = User::where('email', 'shakthitha25@gmail.com')->firstOrFail();
        $staffUser = User::where('email', 'shakthithasivarajah2002@gmail.com')->firstOrFail();

        $this->assertSame(3, User::count());
        $this->assertSame(2, Staff::count());
        $this->assertSame(1, Customer::count());
        $this->assertSame('admin', Staff::where('email', $admin->email)->value('role'));
        $this->assertSame('manager', Staff::where('email', $staffUser->email)->value('role'));
        $this->assertDatabaseHas('customers', ['email' => $customer->email]);
        $this->assertTrue(Hash::check('admin-seed-test-password', $admin->password));
        $this->assertTrue(Hash::check('customer-seed-test-password', $customer->password));
        $this->assertTrue(Hash::check('staff-seed-test-password', $staffUser->password));
        $this->assertNotSame('admin-seed-test-password', $admin->password);
    }

    public function test_reseeding_preserves_an_existing_account_password_hash(): void
    {
        $values = [
            'HONEYBEE_ADMIN_PASSWORD' => 'admin-seed-test-password',
            'HONEYBEE_CUSTOMER_PASSWORD' => 'customer-seed-test-password',
            'HONEYBEE_STAFF_PASSWORD' => 'staff-seed-test-password',
        ];
        $previous = [];

        foreach ($values as $key => $value) {
            $previous[$key] = getenv($key);
            putenv($key.'='.$value);
            $_ENV[$key] = $value;
            $_SERVER[$key] = $value;
        }

        try {
            app(HoneyBeeAccountsSeeder::class)->run();
            $staff = User::where('email', 'shakthithasivarajah2002@gmail.com')->firstOrFail();
            $preservedHash = Hash::make('existing-password-to-preserve');
            $staff->update(['password' => $preservedHash]);
            app(HoneyBeeAccountsSeeder::class)->run();
        } finally {
            foreach ($previous as $key => $value) {
                if ($value === false) {
                    putenv($key);
                    unset($_ENV[$key], $_SERVER[$key]);
                } else {
                    putenv($key.'='.$value);
                    $_ENV[$key] = $value;
                    $_SERVER[$key] = $value;
                }
            }
        }

        $this->assertSame($preservedHash, $staff->fresh()->password);
        $this->assertSame('manager', Staff::where('email', $staff->email)->value('role'));
        $this->assertSame(3, User::count());
    }
}
