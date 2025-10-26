<nav class="bg-white border border-gray-200 rounded-lg shadow p-4">
    <div class="flex justify-end items-center">
        <div x-data="{ open: false }" class="relative">
            <button @click="open = !open" class="inline-flex items-center px-3 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:text-blue-700 focus:outline-none transition">
                {{ Auth::user()->name }}
                <svg class="ml-1 h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5.293 7.293L12 14l6.707-6.707" />
                </svg>
            </button>

            <!-- Dropdown -->
            <div x-show="open" @click.away="open = false" class="absolute right-0 mt-2 w-48 bg-white rounded-xl shadow-lg z-50">
                <a href="{{ route('profile.edit') }}" class="block px-4 py-2 text-gray-700 hover:bg-blue-50 hover:text-blue-700 rounded-xl transition">
                    Profile
                </a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="w-full text-left block px-4 py-2 text-gray-700 hover:bg-blue-50 hover:text-blue-700 rounded-xl transition">
                        Log Out
                    </button>
                </form>
            </div>
        </div>
    </div>
</nav>
