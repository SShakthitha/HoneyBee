<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="font-sans antialiased">
    <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 auth-bg">

        <div>
            <a href="/">
                <x-application-logo class="w-20 h-20 fill-current text-amber-400" />
            </a>
        </div>

        <div class="w-full sm:max-w-3xl mt-6 px-8 py-8 bg-black/80 backdrop-blur-md border border-amber-500/30 shadow-2xl overflow-hidden sm:rounded-xl text-black">
            {{ $slot }}
        </div>

    </div>
</body>
</html>