@extends('layouts.app')

@section('title', 'Edit Profile')

@section('content')

<style>
    .profile-page { max-width: 860px; margin: 3rem auto; padding: 2rem 1.5rem; background: radial-gradient(circle at top right, rgba(245,166,35,.16), transparent 34%), linear-gradient(135deg, #1A1A1A, #34302a); border: 1px solid rgba(245,166,35,.25); border-radius: 22px; box-shadow: 0 20px 50px rgba(26,26,26,.18); }
    .profile-header { background: rgba(0,0,0,.28); border-left: 6px solid #F5A623; border-radius: 18px; padding: 1.8rem 2rem; margin-bottom: 1.5rem; }
    .profile-header h1 { color: #FFD166; font-family: 'Playfair Display', serif; font-size: clamp(1.8rem, 4vw, 2.35rem); margin: 0 0 .35rem; }
    .profile-header p { color: rgba(255,255,255,.76); margin: 0; }
    .profile-card { background: rgba(0,0,0,.58); border: 1px solid rgba(245,166,35,.22); border-radius: 14px; padding: 2rem; }
    .profile-field { margin-bottom: 1.25rem; }
    .profile-field label { color: #FFD166; display: block; font-size: .82rem; font-weight: 700; letter-spacing: .06em; margin-bottom: .45rem; text-transform: uppercase; }
    .profile-field input, .profile-field textarea { background: rgba(255,255,255,.96); border: 1px solid rgba(255,209,102,.45); border-radius: 9px; color: #1A1A1A; font: inherit; padding: .78rem 1rem; width: 100%; }
    .profile-field input:focus, .profile-field textarea:focus { border-color: #F5A623; box-shadow: 0 0 0 .2rem rgba(245,166,35,.2); outline: 0; }
    .profile-field textarea { min-height: 120px; resize: vertical; }
    .profile-save { background: #F5A623; border: 0; border-radius: 50px; color: #1A1A1A; cursor: pointer; font: inherit; font-weight: 700; padding: .75rem 1.6rem; transition: .2s ease; }
    .profile-save:hover { background: #FFD166; transform: translateY(-2px); }
    @media (max-width: 520px) { .profile-page { margin: 2rem 1rem; padding: 1rem; } .profile-header, .profile-card { padding: 1.35rem; } }
</style>

<div class="profile-page">
    <section class="profile-header">
        <h1>Edit Profile</h1>
        <p>Keep your contact details up to date for your HoneyBee orders.</p>
    </section>

    <section class="profile-card">
        <form method="POST" action="{{ route('customer.profile.update') }}">
            @csrf
            @method('PUT')

            <div class="profile-field">
                <label for="full_name">Full Name</label>
                <input id="full_name" type="text" name="full_name" value="{{ old('full_name', $customer->full_name) }}" required>
            </div>

            <div class="profile-field">
                <label for="phone">Phone</label>
                <input id="phone" type="text" name="phone" value="{{ old('phone', $customer->phone) }}">
            </div>

            <div class="profile-field">
                <label for="address">Address</label>
                <textarea id="address" name="address">{{ old('address', $customer->address) }}</textarea>
            </div>

            <button type="submit" class="profile-save">Save Changes</button>
        </form>
    </section>
</div>

@endsection
