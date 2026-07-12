@extends('layouts.app')

@section('title', 'Customer Dashboard')

@section('content')

<style>
    .dashboard-section {
        padding: 50px 40px;
        min-height: 80vh;
        background-color: #fff;
    }

    .dashboard-container {
        max-width: 900px;
        margin: 0 auto;
    }

    .welcome-title {
        font-size: 32px;
        margin-bottom: 30px;
        color: #1a1a1a;
    }

    .welcome-title span {
        color: #f5a623;
    }

    .profile-card {
        background: #fff;
        border: 1px solid #eee;
        border-radius: 15px;
        padding: 35px;
        box-shadow: 0 5px 20px rgba(0,0,0,0.08);
    }

    .profile-card h2 {
        color: #f5a623;
        margin-bottom: 25px;
    }

    .profile-item {
        display: flex;
        justify-content: space-between;
        padding: 15px 0;
        border-bottom: 1px solid #eee;
        font-size: 16px;
    }

    .profile-item:last-child {
        border-bottom: none;
    }

    .profile-label {
        font-weight: bold;
        color: #333;
    }

    .profile-value {
        color: #666;
    }

    .edit-btn {
        display: inline-block;
        margin-top: 25px;
        background-color: transparent;
        color: #f5a623;
        border: 1px solid #f5a623;
        padding: 10px 25px;
        border-radius: 8px;
        text-decoration: none;
        font-weight: bold;
        transition: 0.3s;
    }

    .edit-btn:hover {
        background-color: #f5a623;
        color: #1a1a1a;
    }

</style>


<section class="dashboard-section">

    <div class="dashboard-container">

        <h1 class="welcome-title">
            Welcome, <span>{{ $customer->full_name }}</span> 🐝
        </h1>


        <div class="profile-card">

            <h2>My Profile</h2>


            <div class="profile-item">
                <span class="profile-label">Name</span>
                <span class="profile-value">
                    {{ $customer->full_name }}
                </span>
            </div>


            <div class="profile-item">
                <span class="profile-label">Email</span>
                <span class="profile-value">
                    {{ $customer->email }}
                </span>
            </div>


            <div class="profile-item">
                <span class="profile-label">Phone</span>
                <span class="profile-value">
                    {{ $customer->phone ?? 'Not Added' }}
                </span>
            </div>


            <div class="profile-item">
                <span class="profile-label">Address</span>
                <span class="profile-value">
                    {{ $customer->address ?? 'Not Added' }}
                </span>
            </div>


            <div class="profile-item">
                <span class="profile-label">Total Spent</span>
                <span class="profile-value">
                    Rs. {{ $customer->total_spent }}
                </span>
            </div>


            <a href="{{ route('customer.profile.edit') }}" class="edit-btn">
                Edit Profile
            </a>


        </div>

    </div>

</section>

@endsection