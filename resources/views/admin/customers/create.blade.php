@extends('layouts.admin')

@section('content')

<h2>Add Customer</h2>
<a href="{{ route('admin.customers.create') }}">
    + Add Customer
</a>

<form method="POST" action="{{ route('admin.customers.store') }}">
    @csrf

    <input type="text" name="full_name" placeholder="Full Name" required><br><br>

    <input type="email" name="email" placeholder="Email" required><br><br>

    <input type="text" name="phone" placeholder="Phone"><br><br>

    <textarea name="address" placeholder="Address"></textarea><br><br>

    <button type="submit">Save</button>
</form>

@endsection