<?php

namespace App\Http\Controllers;

use App\Models\Handles;
use App\Models\Staff;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ManagerDashboardController extends Controller
{
    public function index(Request $request): View
    {
        $staff = Staff::where('email', $request->user()->email)->firstOrFail();
        $staffCount = Staff::where('role', 'staff')->count();
        $assignmentCount = Handles::whereHas('staff', fn ($query) => $query->where('role', 'staff'))->count();
        $recentAssignments = Handles::with(['staff', 'service'])
            ->whereHas('staff', fn ($query) => $query->where('role', 'staff'))
            ->latest('assigned_date')
            ->take(8)
            ->get();

        return view('manager.dashboard', compact('staff', 'staffCount', 'assignmentCount', 'recentAssignments'));
    }
}
