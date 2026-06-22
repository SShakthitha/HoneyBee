@extends('layouts.admin')

@section('content')

<h2>Edit Customer</h2>

<form method="POST" action="{{ route('admin.customers.update', $customer->customer_id) }}">
    @csrf
    @method('PUT')

    <input type="text" name="full_name" value="{{ $customer->full_name }}" required><br><br>

    <input type="email" name="email" value="{{ $customer->email }}" required><br><br>

    <input type="text" name="phone" value="{{ $customer->phone }}"><br><br>

    <textarea name="address">{{ $customer->address }}</textarea><br><br>

    <button type="submit">Update</button>
</form>

@endsection