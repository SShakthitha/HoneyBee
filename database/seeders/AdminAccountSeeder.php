<?php

namespace Database\Seeders;

use App\Models\Staff;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use InvalidArgumentException;

class AdminAccountSeeder extends Seeder
{
    /** Create or update the initial administrator using a runtime secret. */
    public function run(): void
    {
        $email = 'sivarajahshakthitha25@gmail.com';
        $password = env('HONEYBEE_ADMIN_PASSWORD');

        if (! is_string($password) || mb_strlen($password) < 16) {
            throw new InvalidArgumentException(
                'Set HONEYBEE_ADMIN_PASSWORD to a new password with at least 16 characters before running this seeder.'
            );
        }

        DB::transaction(function () use ($email, $password): void {
            User::updateOrCreate(
                ['email' => $email],
                [
                    'name' => 'Sivarajah Shakthitha',
                    'password' => Hash::make($password),
                    'email_verified_at' => now(),
                ]
            );

            Staff::updateOrCreate(
                ['email' => $email],
                [
                    'business_id' => null,
                    'full_name' => 'Sivarajah Shakthitha',
                    'role' => 'admin',
                    'phone' => 'Not provided',
                    'hire_date' => today(),
                ]
            );
        });

        $this->command?->info('Administrator account provisioned. Password was not displayed.');
    }
}
