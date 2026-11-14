@extends('layouts.admin')

@section('title', 'Edit Bookable Service')

@section('content')
<div class="topbar"><h1>Edit <span>Bookable Service</span></h1><a href="{{ route('admin.service-records.index') }}" class="btn btn-outline-dark">Back to Services</a></div>
@include('admin.service-records.form', ['action' => route('admin.service-records.update', $service->service_id), 'method' => 'PUT', 'service' => $service])
@endsection
