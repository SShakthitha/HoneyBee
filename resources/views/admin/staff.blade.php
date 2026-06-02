@extends('layouts.admin')

@section('title', 'Staff')

@section('content')

<div class="topbar">
    <h1>All <span>Staff</span></h1>
    <a href="/admin/staff/create">+ Add Staff</a>
</div>

<div style="background: #fff; border-radius: 15px; padding: 30px; box-shadow: 0 5px 20px rgba(0,0,0,0.08);">
    @if($staff->isEmpty())
        <p style="text-align: center; color: #777; padding: 40px;">No staff added yet! 🐝</p>
    @else
        <table style="width: 100%; border-collapse: collapse;">
            <thead>
                <tr style="background: #f5a623; color: #1a1a1a;">
                    <th style="padding: 12px; text-align: left;">ID</th>
                    <th style="padding: 12px; text-align: left;">Name</th>
                    <th style="padding: 12px; text-align: left;">Role</th>
                    <th style="padding: 12px; text-align: left;">Email</th>
                    <th style="padding: 12px; text-align: left;">Phone</th>
                    <th style="padding: 12px; text-align: left;">Hire Date</th>
                </tr>
            </thead>
            <tbody>
                @foreach($staff as $member)
                <tr style="border-bottom: 1px solid #eee;">
                    <td style="padding: 12px;">#{{ $member->staff_id }}</td>
                    <td style="padding: 12px;">{{ $member->full_name }}</td>
                    <td style="padding: 12px;">{{ $member->role }}</td>
                    <td style="padding: 12px;">{{ $member->email }}</td>
                    <td style="padding: 12px;">{{ $member->phone }}</td>
                    <td style="padding: 12px;">{{ $member->hire_date }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    @endif
</div>

@endsection