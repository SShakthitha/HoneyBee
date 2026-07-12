@extends('layouts.app')

@section('title', 'Edit Profile')

@section('content')

<div style="padding:50px 40px;">

<div style="
max-width:700px;
margin:auto;
background:white;
padding:35px;
border-radius:15px;
box-shadow:0 5px 20px rgba(0,0,0,0.08);
">


<h2 style="color:#f5a623; margin-bottom:25px;">
Edit Profile
</h2>


<form method="POST" action="{{ route('customer.profile.update') }}">

@csrf
@method('PUT')


<label>Full Name</label>

<input type="text"
name="full_name"
value="{{ $customer->full_name }}"
style="width:100%; padding:10px; margin:10px 0 20px; color:black;">


<label>Phone</label>

<input type="text"
name="phone"
value="{{ $customer->phone }}"
style="width:100%; padding:10px; margin:10px 0 20px; color:black;">


<label>Address</label>

<textarea name="address"
style="width:100%; padding:10px; margin:10px 0 20px; color:black;">{{ $customer->address }}</textarea>


<button type="submit"
style="
background:#f5a623;
border:none;
padding:12px 30px;
border-radius:8px;
font-weight:bold;
cursor:pointer;">
Save Changes
</button>


</form>


</div>

</div>

@endsection