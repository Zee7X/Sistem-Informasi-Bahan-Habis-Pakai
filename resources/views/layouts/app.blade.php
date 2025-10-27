<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Sistem Informasi Bahan Habis Pakai') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased bg-gray-200">
    <div class="flex h-screen gap-x-6">
        @include('components.sidebar')

        <div class="flex-1 flex flex-col overflow-hidden p-6 space-y-4">
            @include('layouts.navigation')

            @hasSection('header')
                <header class="bg-gray-100 shadow rounded-lg p-4 w-full">
                    @yield('header')
                </header>
            @endif

            <main class="flex-1 flex flex-col overflow-auto p-6 bg-gray-100 rounded-xl shadow space-y-4">
                <turbo-frame id="main-content">
                    @yield('content')
                </turbo-frame>
            </main>
        </div>
    </div>
</body>
</html>
