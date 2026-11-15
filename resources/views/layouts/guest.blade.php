<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.ico') }}">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">

    <title>HoneyBee - Account</title>
    <meta name="description" content="Sign in or create your HoneyBee Gifts & Designs account to manage orders and discover handcrafted gifts and services.">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="{{ asset('css/responsive.css') }}">
</head>

<body class="font-sans antialiased">
    <div class="min-h-screen flex flex-col justify-center items-center px-4 py-10 auth-bg">

        <div class="auth-brand">
            <a href="/">
                <x-application-logo class="w-20 h-20 fill-current text-amber-400" />
            </a>
        </div>

        <div class="auth-panel {{ request()->routeIs('login') ? 'auth-panel--login' : 'auth-panel--default' }} w-full">
            {{ $slot }}
        </div>

    </div>
</body>
</html>
