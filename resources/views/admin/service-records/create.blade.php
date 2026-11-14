@extends('layouts.admin')

@section('title', 'Add Bookable Service')

@section('content')
<div class="topbar"><h1>Add <span>Bookable Service</span></h1><a href="{{ route('admin.service-records.index') }}" class="btn btn-outline-dark">Back to Services</a></div>
@if ($businesses->isEmpty())
    <div class="alert alert-warning" role="alert">Add the business record for this service before creating it. No business has been selected or assumed.</div>
@endif
@include('admin.service-records.form', ['action' => route('admin.service-records.store'), 'method' => 'POST', 'service' => null])
@endsection
