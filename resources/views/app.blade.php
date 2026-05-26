<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Sistem Manajemen Bahan Habis Pakai Laboratorium">
    @viteReactRefresh
    @vite('resources/js/app.jsx')
    @inertiaHead
</head>
<body class="bg-dark-bg text-text-primary font-sans antialiased">
    @inertia
</body>
</html>
