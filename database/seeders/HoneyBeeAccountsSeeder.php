<?php

namespace Database\Seeders;

use App\Models\Staff;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class HoneyBeeAccountsSeeder extends Seeder
{
    /** Create the HoneyBee admin account. */
    public function run(): void
    {
        DB::transaction(function (): void {

            // Create or update the User record (login credentials).
            $user = User::firstOrNew(['email' => 'sivarajahshakthitha25@gmail.com']);
            $user->name     = 'Sivarajah Shakthitha';
            $user->password = Hash::make('Shakthitha#1234');
            $user->email_verified_at = now();
            $user->save();

            // Create or update the Staff record (grants admin role).
            $staff = Staff::firstOrNew(['email' => 'sivarajahshakthitha25@gmail.com']);
            $staff->role      = 'admin';
            $staff->full_name = 'Sivarajah Shakthitha';
            if (! $staff->exists) {
                $staff->phone     = 'Not provided';
                $staff->hire_date = today();
            }
            $staff->save();

        });

        $this->command?->info('Admin account ready: sivarajahshakthitha25@gmail.com');
    }
}
