@extends('layouts.admin')

@section('title', 'Edit Staff')

@section('content')

<h2>Edit Staff</h2>

<form action="{{ route('admin.staff.update', $member->staff_id) }}" method="POST">
    @csrf
    @method('PUT')

    <input type="text" name="full_name" value="{{ $member->full_name }}">
    <input type="text" name="role" value="{{ $member->role }}">
    <input type="email" name="email" value="{{ $member->email }}">
    <input type="text" name="phone" value="{{ $member->phone }}">

    <button type="submit">Update</button>
</form>

@endsection