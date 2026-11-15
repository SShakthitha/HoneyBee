@component('mail::message')
# Your Password Reset Code

Hi {{ $user->name }},

We received a request to reset the password for your **{{ config('app.name') }}** account.

Use the code below to reset your password:

@component('mail::panel')
# {{ $otp }}
@endcomponent

This code is valid for **{{ $expiryMinutes }} minutes** and can only be used **once**.

@component('mail::button', ['url' => route('password.reset'), 'color' => 'primary'])
Go to Reset Page
@endcomponent

If you did not request a password reset, you can safely ignore this email — your password will remain unchanged.

Thanks,
**{{ config('app.name') }} Team**

---
<small>For security, never share this code with anyone. {{ config('app.name') }} staff will never ask you for it.</small>
@endcomponent
