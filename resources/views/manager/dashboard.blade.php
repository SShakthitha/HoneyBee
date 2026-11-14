@extends('layouts.staff')

@section('title', 'Manager Dashboard')

@section('content')
<div class="page-head">
    <div>
        <p class="text-muted mb-1">HoneyBee operations</p>
        <h1>Manager <span>Dashboard</span></h1>
    </div>
    <span class="text-muted">{{ $staff->full_name }}</span>
</div>

<section class="honey-card mb-4" style="background:linear-gradient(135deg,#1a1a1a,#303030);color:#fff;border-left:5px solid #f5a623">
    <h2 class="h3" style="color:#f5a623">Welcome back, {{ $staff->full_name }}.</h2>
    <p class="mb-0">Review your staff team and their current service assignments.</p>
</section>

<section class="row g-3 mb-4" aria-label="Staff overview">
    <div class="col-sm-6 col-xl-4">
        <div class="honey-card h-100">
            <p class="text-muted mb-1">Staff members</p>
            <p class="display-6 fw-bold mb-0">{{ $staffCount }}</p>
        </div>
    </div>
    <div class="col-sm-6 col-xl-4">
        <div class="honey-card h-100">
            <p class="text-muted mb-1">Recorded assignments</p>
            <p class="display-6 fw-bold mb-0">{{ $assignmentCount }}</p>
        </div>
    </div>
    <div class="col-sm-12 col-xl-4 d-flex align-items-stretch">
        <a class="btn btn-honey w-100 d-flex align-items-center justify-content-center gap-2" href="{{ route('staff.staff.index') }}">
            <i class="fa-solid fa-user-tie" aria-hidden="true"></i> Manage staff
        </a>
    </div>
</section>

<section class="honey-card">
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3">
        <h2 class="h4 mb-0">Recent staff assignments</h2>
        <a href="{{ route('staff.staff.index') }}" class="btn btn-sm btn-outline-dark">View staff list</a>
    </div>
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead><tr><th scope="col">Staff member</th><th scope="col">Service</th><th scope="col">Assigned</th><th scope="col">Status</th></tr></thead>
            <tbody>
            @forelse($recentAssignments as $assignment)
                <tr>
                    <td>{{ $assignment->staff?->full_name ?? 'Unassigned staff' }}</td>
                    <td>{{ $assignment->service?->service_name ?? $assignment->service?->service_type ?? 'Service unavailable' }}</td>
                    <td>{{ $assignment->assigned_date?->format('d M Y') ?? $assignment->assigned_date ?? '-' }}</td>
                    <td><span class="badge-status status-{{ strtolower($assignment->status) }}">{{ ucfirst($assignment->status) }}</span></td>
                </tr>
            @empty
                <tr><td colspan="4" class="text-center text-muted py-4">No staff assignments are recorded.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
</section>
@endsection
