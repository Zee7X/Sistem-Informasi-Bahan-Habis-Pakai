<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Sistem Informasi Bahan Habis Pakai') }}</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased bg-gray-200">
    <div class="flex min-h-screen">
        
        @include('components.sidebar')

        <div class="flex-1 flex flex-col p-6 space-y-4">
            @include('layouts.navigation')

            @hasSection('header')
                <header class="bg-gray-100 shadow rounded-lg p-4">
                    <div class="max-w-7xl mx-auto">
                        @yield('header')
                    </div>
                </header>
            @endif

            <main class="flex-1 bg-gray-100 rounded-xl shadow p-6">
                <turbo-frame id="main-content">
                    @yield('content')
                </turbo-frame>
            </main>
        </div>
    </div>

    <style>
        [x-cloak] { display: none !important; }
    </style>
</body>
</html>
