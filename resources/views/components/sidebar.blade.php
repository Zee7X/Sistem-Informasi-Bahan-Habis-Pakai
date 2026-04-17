@php
    $currentRoute = Route::currentRouteName();
@endphp

<div x-data="{ sidebarOpen: false }">
    <!-- Mobile Header -->
    <div class="md:hidden flex items-center justify-between p-4 bg-white border-b border-slate-200">
        <button @click="sidebarOpen = !sidebarOpen" class="p-2 rounded-lg hover:bg-slate-100 transition-colors">
            <svg class="w-6 h-6 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
            </svg>
        </button>
        <span class="font-bold text-slate-800 tracking-tight">Sistem BHP</span>
        <div class="w-8"></div>
    </div>

    <!-- Mobile Overlay -->
    <div x-show="sidebarOpen" x-transition:opacity
        class="fixed inset-0 z-40 bg-slate-900/20 backdrop-blur-[2px] md:hidden" @click="sidebarOpen = false"></div>

    <!-- Sidebar Container -->
    <aside :class="{ 'translate-x-0': sidebarOpen, '-translate-x-full': !sidebarOpen }"
        class="fixed md:relative z-50 w-64 h-screen bg-white border-r border-slate-200 flex flex-col transform md:translate-x-0 transition-transform duration-300 ease-in-out shadow-sm">

        <!-- Logo Section -->
        <div class="px-6 py-8 border-b border-slate-100/50">
            <div class="flex items-center gap-3">
                <div
                    class="w-8 h-8 rounded-lg bg-blue-600 flex items-center justify-center text-white shadow-sm ring-4 ring-blue-50">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z" />
                    </svg>
                </div>
                <h2 class="text-sm font-bold text-slate-900 tracking-tight uppercase">TPPL <span
                        class="text-blue-600">BHP</span></h2>
            </div>
        </div>

        <!-- Navigation Scroll Area -->
        <div class="flex-1 overflow-y-auto px-3 py-6 space-y-6">

            <!-- Section: Main -->
            <div>
                <p class="px-3 text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-3">Utama</p>
                <div class="space-y-1">
                    <a href="{{ route('dashboard') }}"
                        class="sidebar-link {{ $currentRoute == 'dashboard' ? 'sidebar-link-active' : 'sidebar-link-inactive' }}">
                        <svg class="w-5 h-5 {{ $currentRoute == 'dashboard' ? 'text-blue-600' : '' }}" fill="none"
                            stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                                d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
                        </svg>
                        <span>Dashboard</span>
                    </a>

                    <a href="{{ route('pengajuan.index') }}"
                        class="sidebar-link {{ in_array($currentRoute, ['pengajuan.index', 'pengajuan.create', 'pengajuan.show']) ? 'sidebar-link-active' : 'sidebar-link-inactive' }}">
                        <svg class="w-5 h-5 {{ in_array($currentRoute, ['pengajuan.index', 'pengajuan.create']) ? 'text-blue-600' : '' }}"
                            fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        <span>{{ auth()->user()->role === 'mahasiswa' ? 'Pengajuan Saya' : 'Transaksi Bahan' }}</span>
                    </a>
                </div>
            </div>

            <!-- Section: Inventory -->
            @if (auth()->user()->role === 'admin')
                <div>
                    <p class="px-3 text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-3">Inventori</p>
                    <div class="space-y-1">
                        <a href="{{ route('admin.bahan-masuk.index') }}" 
                           class="sidebar-link {{ $currentRoute == 'admin.bahan-masuk.index' ? 'sidebar-link-active' : 'sidebar-link-inactive' }}">
                            <svg class="w-5 h-5 {{ $currentRoute == 'admin.bahan-masuk.index' ? 'text-blue-600' : '' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                                    d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                            </svg>
                            <span>Bahan Masuk</span>
                        </a>
                    </div>
                </div>
            @endif

            <!-- Section: Master Data -->
            @if (auth()->user()->role !== 'mahasiswa')
                <div>
                    <p class="px-3 text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-3">Master Data</p>
                    <div class="space-y-1">
                        @if (auth()->user()->role === 'admin')
                            <a href="{{ route('admin.users') }}"
                                class="sidebar-link {{ $currentRoute == 'admin.users' ? 'sidebar-link-active' : 'sidebar-link-inactive' }}">
                                <svg class="w-5 h-5 {{ $currentRoute == 'admin.users' ? 'text-blue-600' : '' }}"
                                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                                        d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                                </svg>
                                <span>Master User</span>
                            </a>

                            <a href="{{ route('admin.satuan.index') }}"
                                class="sidebar-link {{ $currentRoute == 'admin.satuan.index' ? 'sidebar-link-active' : 'sidebar-link-inactive' }}">
                                <svg class="w-5 h-5 {{ $currentRoute == 'admin.satuan.index' ? 'text-blue-600' : '' }}"
                                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                                        d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                                </svg>
                                <span>Master Satuan</span>
                            </a>
                        @endif

                        <a href="{{ route('bahan') }}"
                            class="sidebar-link {{ $currentRoute == 'bahan' ? 'sidebar-link-active' : 'sidebar-link-inactive' }}">
                            <svg class="w-5 h-5 {{ $currentRoute == 'bahan' ? 'text-blue-600' : '' }}" fill="none"
                                stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                                    d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4m0 5c0 2.21-3.582 4-8 4s-8-1.79-8-4" />
                            </svg>
                            <span>Master Bahan</span>
                        </a>
                    </div>
                </div>
            @endif
        </div>

        <!-- Sidebar Footer -->
        <div class="px-6 py-6 border-t border-slate-100/50">
            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest opacity-70">
                &copy; {{ date('Y') }} — TPPL BHP PNC
            </p>
        </div>
    </aside>
</div>
