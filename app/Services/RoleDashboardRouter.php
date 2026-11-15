<?php

namespace App\Services;

use App\Models\Customer;
use App\Models\Staff;
use App\Models\User;

class RoleDashboardRouter
{
    public function routeNameFor(User $user): ?string
    {
        $staff = Staff::where('email', $user->email)->first();

        return match (strtolower(trim((string) $staff?->role))) {
            'admin' => 'admin.dashboard',
            'manager' => 'manager.dashboard',
            'staff' => 'staff.dashboard',
            default => ! $staff && Customer::where('email', $user->email)->exists()
                ? 'customer.dashboard'
                : null,
        };
    }
}
