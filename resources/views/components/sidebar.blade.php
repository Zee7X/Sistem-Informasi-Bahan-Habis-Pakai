@php
    $currentRoute = Route::currentRouteName();
@endphp

<div class="w-64 bg-white flex flex-col h-screen flex-shrink-0 p-6 font-sans shadow-lg rounded-r-2xl">
    <div class="flex items-center mb-8">
        <img src="{{ asset('images/tppl.png') }}" alt="TPPL Logo" class="w-8 h-8 object-contain mr-3">
        <span class="text-xl font-extrabold text-blue-900 uppercase tracking-wide">
            TPPL - <span class="text-blue-600">BHP</span>
        </span>
    </div>

    <div class="text-xs font-semibold text-gray-500 uppercase mb-2 tracking-wide">
        Dashboard
    </div>

    <a href="{{ route('dashboard') }}"
        class="flex items-center space-x-2 px-3 py-2 text-gray-700 font-medium transition rounded-xl shadow-sm hover:shadow-md
        {{ $currentRoute == 'dashboard' ? 'text-blue-700 bg-blue-50 shadow-md' : 'hover:text-blue-700 bg-white' }}">
        <img src="{{ asset('images/dashboard.png') }}" alt="Dashboard" class="w-5 h-5 object-contain">
        <span class="text-sm">Dashboard</span>
    </a>

    <div class="text-xs font-semibold text-gray-500 uppercase mt-6 mb-2 tracking-wide">
        Main
    </div>

    <a href="#"
        class="flex items-center space-x-2 px-3 py-2 text-gray-700 font-medium transition rounded-xl shadow-sm hover:shadow-md
        {{ $currentRoute == 'pengajuan' ? 'text-blue-700 bg-blue-50 shadow-md' : 'hover:text-blue-700 bg-white' }}">
        <img src="{{ asset('images/pengajuan.png') }}" alt="Pengajuan" class="w-5 h-5 object-contain">
        <span class="text-sm">Pengajuan</span>
    </a>

    <div x-data="{ open: {{ in_array($currentRoute, ['barang.masuk','barang.keluar']) ? 'true' : 'false' }} }" class="flex flex-col mt-2">
        <button @click="open = !open"
                class="flex items-center justify-between px-3 py-2 text-gray-700 font-medium transition rounded-xl shadow-sm hover:shadow-md
                {{ in_array($currentRoute, ['barang.masuk','barang.keluar']) ? 'text-blue-700 bg-blue-50 shadow-md' : 'hover:text-blue-700 bg-white' }}
                focus:outline-none">
            <div class="flex items-center space-x-2">
                <img src="{{ asset('images/barang.png') }}" alt="Barang" class="w-5 h-5 object-contain">
                <span class="text-sm">Barang</span>
            </div>
            <svg :class="{'transform rotate-90': open}" class="w-4 h-4 text-gray-400 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
            </svg>
        </button>

        <div x-show="open" x-transition x-cloak class="mt-1 ml-6 flex flex-col space-y-1">
            <a href="#"
                class="px-3 py-1 flex items-center space-x-2 text-gray-700 text-sm font-medium transition rounded-xl shadow-sm hover:shadow-md
                {{ $currentRoute == 'barang.masuk' ? 'text-blue-700 bg-blue-50 shadow-md' : 'hover:text-blue-700 bg-white' }}">
                <img src="{{ asset('images/masuk.png') }}" alt="Masuk" class="w-4 h-4 object-contain">
                <span>Masuk</span>
            </a>
            <a href="#"
                class="px-3 py-1 flex items-center space-x-2 text-gray-700 text-sm font-medium transition rounded-xl shadow-sm hover:shadow-md
                {{ $currentRoute == 'barang.keluar' ? 'text-blue-700 bg-blue-50 shadow-md' : 'hover:text-blue-700 bg-white' }}">
                <img src="{{ asset('images/keluar.png') }}" alt="Keluar" class="w-4 h-4 object-contain">
                <span>Keluar</span>
            </a>
        </div>
    </div>

    <div class="text-xs font-semibold text-gray-500 uppercase mt-6 mb-2 tracking-wide">
        Lainnya
    </div>

    @if(auth()->user()->role === 'admin')
    <a href="{{ route('admin.users') }}"
        class="flex items-center space-x-2 px-3 py-2 text-gray-700 font-medium transition rounded-xl shadow-sm hover:shadow-md
        {{ $currentRoute == 'admin.users' ? 'text-blue-700 bg-blue-50 shadow-md' : 'hover:text-blue-700 bg-white' }}">
        <img src="{{ asset('images/people.png') }}" alt="User" class="w-5 h-5 object-contain">
        <span class="text-sm">User</span>
    </a>
    @endif

    <div x-data="{ open: {{ in_array($currentRoute, ['master.barang','master.satuan']) ? 'true' : 'false' }} }" class="flex flex-col mt-2">
        <button @click="open = !open"
                class="flex items-center justify-between px-3 py-2 text-gray-700 font-medium transition rounded-xl shadow-sm hover:shadow-md
                {{ in_array($currentRoute, ['master.barang','master.satuan']) ? 'text-blue-700 bg-blue-50 shadow-md' : 'hover:text-blue-700 bg-white' }}
                focus:outline-none">
            <div class="flex items-center space-x-2">
                <img src="{{ asset('images/master-data.png') }}" alt="Master" class="w-5 h-5 object-contain">
                <span class="text-sm">Master</span>
            </div>
            <svg :class="{'transform rotate-90': open}" class="w-4 h-4 text-gray-400 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
            </svg>
        </button>

        <div x-show="open" x-transition x-cloak class="mt-1 ml-6 flex flex-col space-y-1">
            <a href="#"
                class="px-3 py-1 flex items-center space-x-2 text-gray-700 text-sm font-medium transition rounded-xl shadow-sm hover:shadow-md
                {{ $currentRoute == 'master.barang' ? 'text-blue-700 bg-blue-50 shadow-md' : 'hover:text-blue-700 bg-white' }}">
                <img src="{{ asset('images/flask.png') }}" alt="Barang" class="w-4 h-4 object-contain">
                <span>Barang</span>
            </a>
            <a href="#"
                class="px-3 py-1 flex items-center space-x-2 text-gray-700 text-sm font-medium transition rounded-xl shadow-sm hover:shadow-md
                {{ $currentRoute == 'master.satuan' ? 'text-blue-700 bg-blue-50 shadow-md' : 'hover:text-blue-700 bg-white' }}">
                <img src="{{ asset('images/atom.png') }}" alt="Satuan" class="w-4 h-4 object-contain">
                <span>Satuan</span>
            </a>
        </div>
    </div>
</div>

<style>
    [x-cloak] { display: none !important; }
</style>
