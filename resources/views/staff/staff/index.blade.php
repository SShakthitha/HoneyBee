@extends('layouts.staff')
@section('title', 'Staff Management')
@section('content')
<div class="page-head"><h1>Staff <span>Management</span></h1><a class="btn btn-honey" href="{{ route('staff.staff.create') }}"><i class="fa-solid fa-plus"></i> Add staff</a></div>
<section class="honey-card">
    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead><tr><th scope="col">Name</th><th scope="col">Role</th><th scope="col">Email</th><th scope="col">Phone</th><th scope="col">Hired</th><th scope="col">Assignments</th><th scope="col">Actions</th></tr></thead>
            <tbody>
            @forelse($members as $member)
                <tr>
                    <td><strong>{{ $member->full_name }}</strong></td>
                    <td>{{ ucfirst($member->role) }}</td>
                    <td>{{ $member->email }}</td>
                    <td>{{ $member->phone }}</td>
                    <td>{{ $member->hire_date ? \Carbon\Carbon::parse($member->hire_date)->format('d M Y') : '-' }}</td>
                    <td>
                        @forelse($member->handles as $assignment)
                            <div>{{ $assignment->service?->service_name ?? $assignment->service?->service_type ?? 'Service unavailable' }} <span class="text-muted">({{ ucfirst($assignment->status) }})</span></div>
                        @empty
                            <span class="text-muted">None</span>
                        @endforelse
                    </td>
                    <td class="text-end text-nowrap">
                        <a class="btn btn-sm btn-outline-dark" href="{{ route('staff.staff.edit', $member->staff_id) }}">Edit</a>
                        @if($member->handles_count === 0 && ! $member->has_login_account)
                            <form class="d-inline" method="POST" action="{{ route('staff.staff.destroy', $member->staff_id) }}" onsubmit="return confirm('Delete this staff member?')">@csrf @method('DELETE')<button class="btn btn-sm btn-outline-danger" type="submit">Delete</button></form>
                        @elseif($member->has_login_account)
                            <button class="btn btn-sm btn-outline-secondary" type="button" disabled title="Staff login accounts cannot be deleted">Delete</button>
                        @else
                            <button class="btn btn-sm btn-outline-secondary" type="button" disabled title="Staff with assignment history cannot be deleted">Delete</button>
                        @endif
                    </td>
                </tr>
            @empty
                <tr><td colspan="7" class="text-center text-muted py-4">No regular staff members found.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
</section>
@endsection
