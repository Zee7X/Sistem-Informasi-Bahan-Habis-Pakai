<nav class="sticky top-0 z-40 w-full px-4 py-3 md:px-8 bg-slate-50/80 backdrop-blur-md border-b border-slate-200/50">
    <div class="max-w-7xl mx-auto flex justify-end items-center">
        <div class="flex items-center gap-2">
            <!-- Notifications -->
            <button class="p-2 text-slate-400 hover:text-slate-600 hover:bg-slate-100 rounded-lg transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                        d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                </svg>
            </button>

            <!-- Divider -->
            <div class="w-px h-6 bg-slate-200 mx-1"></div>

            <!-- Profile Dropdown -->
            <div x-data="{ open: false }" class="relative">
                <button @click="open = !open"
                    class="flex items-center gap-3 p-1 rounded-lg hover:bg-slate-100 transition-all group">
                    <div
                        class="w-8 h-8 rounded-lg bg-slate-800 flex items-center justify-center text-white font-bold text-xs shadow-sm uppercase">
                        {{ substr(Auth::user()->name, 0, 1) }}
                    </div>
                    <div class="hidden md:flex flex-col items-start leading-tight">
                        <span class="text-xs font-bold text-slate-900">{{ Auth::user()->name }}</span>
                        <span class="text-[10px] font-medium text-slate-400 uppercase tracking-wider">{{ Auth::user()->role }}</span>
                    </div>
                    <svg :class="{ 'rotate-180': open }" class="w-3 h-3 text-slate-400 transition-transform duration-200 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                    </svg>
                </button>

                <!-- Dropdown -->
                <div x-show="open" x-transition:enter="transition ease-out duration-200"
                    x-transition:enter-start="opacity-0 scale-95 translate-y-2"
                    x-transition:enter-end="opacity-100 scale-100 translate-y-0" @click.away="open = false"
                    class="absolute right-0 mt-2 w-56 bg-white rounded-xl shadow-lg border border-slate-200 p-2 z-50 overflow-hidden" x-cloak>
                    
                    <div class="px-3 py-2 border-b border-slate-50 mb-1">
                        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Akun</p>
                        <p class="text-[11px] font-semibold text-slate-700 truncate">{{ Auth::user()->email }}</p>
                    </div>

                    <a href="{{ route('profile.edit') }}"
                        class="flex items-center gap-2 px-3 py-2 text-xs font-medium text-slate-600 hover:bg-slate-50 hover:text-slate-900 rounded-lg transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"
                                stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                        Profil Saya
                    </a>

                    <form method="POST" action="{{ route('logout') }}" class="mt-1">
                        @csrf
                        <button type="submit"
                            class="w-full flex items-center gap-2 px-3 py-2 text-xs font-medium text-rose-600 hover:bg-rose-50 rounded-lg transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path
                                    d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"
                                    stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                            Log Out
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</nav>
