@extends('layouts.admin')

@section('title', 'Staff')

@section('content')

<div class="topbar">
    <h1>All <span>Staff</span></h1>
    <a class="btn btn-outline-dark" href="{{ route('admin.exports.staff') }}"><i class="fa-solid fa-file-excel me-1"></i> Export Staff</a>
</div>

<!-- TOP BAR -->
<div style="display:flex; justify-content:space-between; align-items:center; margin:15px 0;">

    <!-- Add Button -->
    <a href="{{ route('admin.staff.create') }}"
       style="background:#f5a623; color:black; padding:8px 12px; text-decoration:none; border-radius:5px;">
        <b>+ Add Staff</b>
    </a>

    <!-- Search -->
    <form method="GET" action="{{ route('admin.staff') }}" style="display:flex; gap:5px;">
        <input type="text"
               name="search"
               placeholder="Search..."
               value="{{ request('search') }}"
               style="padding:8px; width:200px;">

        <button type="submit"
                style="padding:8px; background:#f5a623; color:black;">
            <b>Search</b>
        </button>
    </form>

</div>

<!-- TABLE BOX -->
<div style="background: #fff; border-radius:15px; padding:30px; box-shadow:0 5px 20px rgba(0,0,0,0.08);">

    @if($staff->isEmpty())
        <p style="text-align:center; color: #777;">No staff found</p>
    @else

    <table style="width:100%; border-collapse:collapse; table-layout:fixed;">

        <!-- HEADER -->
        <thead>
            <tr style="background:#f5a623; color:#1a1a1a;">
                <th style="padding:12px; text-align:left;">ID</th>
                <th style="padding:12px; text-align:left;">Name</th>
                <th style="padding:12px; text-align:left;">Role</th>
                <th style="padding:12px; text-align:left;">Email</th>
                <th style="padding:12px; text-align:left;">Phone</th>
                <th style="padding:12px; text-align:left;">Hire Date</th>
                <th style="padding:12px; text-align:left; width:120px;">Actions</th>
            </tr>
        </thead>

        <!-- BODY -->
        <tbody>

        @foreach($staff as $member)
        <tr style="border-bottom:1px solid #eee;">

            <td style="padding:12px; vertical-align:middle;">#{{ $member->staff_id }}</td>
            <td style="padding:12px; vertical-align:middle;">{{ $member->full_name }}</td>
            <td style="padding:12px; vertical-align:middle;">{{ $member->role }}</td>
            <td style="padding:12px; vertical-align:middle;">{{ $member->email }}</td>
            <td style="padding:12px; vertical-align:middle;">{{ $member->phone }}</td>
            <td style="padding:12px; vertical-align:middle;">{{ $member->hire_date }}</td>

            <!-- ACTIONS -->
            <td style="padding:12px; vertical-align:middle; white-space:nowrap;">

                <!-- EDIT -->
                <a href="{{ route('admin.staff.edit', $member->staff_id) }}"
                   style="color:blue; font-size:16px; margin-right:12px; text-decoration:none;">
                    <i class="fa-solid fa-pen-to-square"></i>
                </a>

                <!-- DELETE -->
                <form action="{{ route('admin.staff.delete', $member->staff_id) }}"
                      method="POST"
                      style="display:inline;"
                      onsubmit="return confirm('Do you want to remove!');">

                    @csrf
                    @method('DELETE')

                    <button type="submit"
                            style="background:none;border:none;color:red;font-size:16px;cursor:pointer;">
                        <i class="fa-solid fa-trash"></i>
                    </button>

                </form>

            </td>

        </tr>
        @endforeach

        </tbody>
    </table>

    @endif

</div>

@endsection
