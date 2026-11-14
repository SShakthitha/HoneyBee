<?php

namespace Database\Seeders;

use App\Models\Customer;
use App\Models\Staff;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use InvalidArgumentException;

class HoneyBeeAccountsSeeder extends Seeder
{
    /** Provision the requested role accounts using passwords supplied at runtime. */
    public function run(): void
    {
        $accounts = [
            [
                'name' => 'Sivarajah Shakthitha',
                'email' => 'sivarajahshakthitha25@gmail.com',
                'password' => env('HONEYBEE_ADMIN_PASSWORD'),
                'role' => 'admin',
            ],
            [
                'name' => 'Shakthitha',
                'email' => 'shakthitha25@gmail.com',
                'password' => env('HONEYBEE_CUSTOMER_PASSWORD'),
                'role' => 'customer',
            ],
            [
                'name' => 'Shakthitha Sivarajah',
                'email' => 'shakthithasivarajah2002@gmail.com',
                'password' => env('HONEYBEE_STAFF_PASSWORD'),
                'role' => 'manager',
            ],
        ];

        foreach ($accounts as $account) {
            if (! is_string($account['password']) || $account['password'] === '') {
                throw new InvalidArgumentException('Set HONEYBEE_ADMIN_PASSWORD, HONEYBEE_CUSTOMER_PASSWORD, and HONEYBEE_STAFF_PASSWORD before running this seeder.');
            }
        }

        DB::transaction(function () use ($accounts): void {
            foreach ($accounts as $account) {
                $user = User::firstOrNew(['email' => $account['email']]);
                if (! $user->exists) {
                    $user->name = $account['name'];
                    $user->password = Hash::make($account['password']);
                    $user->email_verified_at = now();
                }
                $user->save();

                if ($account['role'] === 'customer') {
                    $customer = Customer::firstOrNew(['email' => $account['email']]);
                    if (! $customer->exists) {
                        $customer->full_name = $account['name'];
                        $customer->total_spent = 0;
                        $customer->registered_date = now();
                        $customer->save();
                    }

                    continue;
                }

                $staff = Staff::firstOrNew(['email' => $account['email']]);
                $isNewStaff = ! $staff->exists;
                $staff->role = $account['role'];
                if ($isNewStaff) {
                    $staff->full_name = $account['name'];
                    $staff->phone = 'Not provided';
                    $staff->hire_date = today();
                }
                $staff->save();
            }
        });

        $this->command?->info('HoneyBee role accounts created or updated. Passwords were not displayed.');
    }
}
