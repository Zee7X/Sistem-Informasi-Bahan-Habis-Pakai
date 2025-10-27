@php
    $currentRoute = Route::currentRouteName();
@endphp

<div x-data="{ sidebarOpen: false }">

    <div class="md:hidden flex items-center justify-between p-4 bg-white shadow">
        <button @click="sidebarOpen = !sidebarOpen" class="focus:outline-none">
            <svg class="w-6 h-6 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M4 6h16M4 12h16M4 18h16"/>
            </svg>
        </button>
        <span class="font-bold text-blue-900">TPPL BHP</span>
    </div>

    <div x-show="sidebarOpen" x-transition class="fixed inset-0 z-40 bg-black bg-opacity-50 md:hidden" @click="sidebarOpen = false"></div>

    <aside :class="{'translate-x-0': sidebarOpen, '-translate-x-full': !sidebarOpen}" 
            class="fixed md:relative z-50 w-64 h-screen bg-white shadow-lg flex flex-col p-6 font-sans space-y-4 transform md:translate-x-0 transition-transform duration-300">

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

        <div x-data="{ open: {{ in_array($currentRoute, ['bahan.masuk','bahan.keluar']) ? 'true' : 'false' }} }" class="flex flex-col mt-2">
            <button @click="open = !open"
                    class="flex items-center justify-between px-3 py-2 text-gray-700 font-medium transition rounded-xl shadow-sm hover:shadow-md
                    {{ in_array($currentRoute, ['bahan.masuk','bahan.keluar']) ? 'text-blue-700 bg-blue-50 shadow-md' : 'hover:text-blue-700 bg-white' }}
                    focus:outline-none">
                <div class="flex items-center space-x-2">
                    <img src="{{ asset('images/barang.png') }}" alt="Bahan" class="w-5 h-5 object-contain">
                    <span class="text-sm">Bahan</span>
                </div>
                <svg :class="{'transform rotate-90': open}" class="w-4 h-4 text-gray-400 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                </svg>
            </button>

            <div x-show="open" x-transition x-cloak class="mt-1 ml-6 flex flex-col space-y-1">
                <a href="#"
                    class="px-3 py-1 flex items-center space-x-2 text-gray-700 text-sm font-medium transition rounded-xl shadow-sm hover:shadow-md
                    {{ $currentRoute == 'bahan.masuk' ? 'text-blue-700 bg-blue-50 shadow-md' : 'hover:text-blue-700 bg-white' }}">
                    <img src="{{ asset('images/masuk.png') }}" alt="Masuk" class="w-4 h-4 object-contain">
                    <span>Masuk</span>
                </a>
                <a href="#"
                    class="px-3 py-1 flex items-center space-x-2 text-gray-700 text-sm font-medium transition rounded-xl shadow-sm hover:shadow-md
                    {{ $currentRoute == 'bahan.keluar' ? 'text-blue-700 bg-blue-50 shadow-md' : 'hover:text-blue-700 bg-white' }}">
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

        <div x-data="{ open: {{ in_array($currentRoute, ['bahan','admin.satuan']) ? 'true' : 'false' }} }" class="flex flex-col mt-2">
            <button @click="open = !open"
                    class="flex items-center justify-between px-3 py-2 text-gray-700 font-medium transition rounded-xl shadow-sm hover:shadow-md
                    {{ in_array($currentRoute, ['bahan','admin.satuan']) ? 'text-blue-700 bg-blue-50 shadow-md' : 'hover:text-blue-700 bg-white' }}
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
                <a href="{{ route('bahan') }}"
                    class="px-3 py-1 flex items-center space-x-2 text-gray-700 text-sm font-medium transition rounded-xl shadow-sm hover:shadow-md
                    {{ $currentRoute == 'bahan' ? 'text-blue-700 bg-blue-50 shadow-md' : 'hover:text-blue-700 bg-white' }}">
                    <img src="{{ asset('images/flask.png') }}" alt="Bahan" class="w-4 h-4 object-contain">
                    <span>Bahan</span>
                </a>

                @if(auth()->user()->role === 'admin')
                <a href="{{ route('admin.satuan') }}" 
                    class="px-3 py-1 flex items-center space-x-2 text-gray-700 text-sm font-medium transition rounded-xl shadow-sm hover:shadow-md
                    {{ $currentRoute == 'admin.satuan' ? 'text-blue-700 bg-blue-50 shadow-md' : 'hover:text-blue-700 bg-white' }}">
                    <img src="{{ asset('images/atom.png') }}" alt="Satuan" class="w-4 h-4 object-contain">
                    <span>Satuan</span>
                </a>
                @endif
            </div>
        </div>

    </aside>
</div>

<style>
    [x-cloak] { display: none !important; }
</style>
