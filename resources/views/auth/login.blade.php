<x-guest-layout>
    <div class="min-h-screen flex flex-col md:flex-row bg-white">

        <div class="hidden md:flex md:w-1/2 bg-cover bg-right" 
            style="background-image: url('{{ asset('images/login-bg.jpeg') }}'); background-size: contain; background-repeat: no-repeat;">  
        </div>

        <div class="flex w-full md:w-1/2 items-center justify-end px-10 py-12">
            <div class="w-full max-w-md bg-white rounded-2xl shadow-2xl p-8 border border-gray-200">

                <div class="text-center mb-8">
                    <h1 class="text-2xl font-extrabold text-blue-900 uppercase tracking-wide leading-tight">
                        Sistem Informasi<br>
                        <span class="text-blue-600">Bahan Habis Pakai</span>
                    </h1>
                    <p class="text-sm text-gray-500 mt-1">TPPL - Politeknik Negeri Cilacap</p>
                </div>

                <x-auth-session-status class="mb-4" :status="session('status')" />

                <form method="POST" action="{{ route('login') }}" class="space-y-5">
                    @csrf

                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                        <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition" 
                            placeholder="example@email.com">
                        <x-input-error :messages="$errors->get('email')" class="mt-2 text-red-500 text-sm" />
                    </div>

                    <div class="relative">
                        <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Password</label>
                        <div class="relative">
                            <input id="password" type="password" name="password" required
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition pr-10"
                                placeholder="••••••••">
                            <button type="button" id="togglePassword" class="absolute inset-y-0 right-2 flex items-center px-2 text-gray-400 hover:text-gray-600">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path id="eyeIcon" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.477 0 8.268 2.943 9.542 7-1.274 4.057-5.065 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                            </button>
                        </div>
                        <x-input-error :messages="$errors->get('password')" class="mt-2 text-red-500 text-sm" />
                    </div>

                    <script>
                        const password = document.getElementById('password');
                        const togglePassword = document.getElementById('togglePassword');
                        togglePassword.addEventListener('click', () => {
                            const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
                            password.setAttribute('type', type);
                        });
                    </script>

                    <div class="flex items-center justify-between text-sm">
                        @if (Route::has('password.request'))
                            <a href="{{ route('password.request') }}" class="text-blue-600 hover:underline">
                                Lupa password?
                            </a>
                        @endif
                    </div>

                    <div>
                        <button type="submit"
                            class="w-full py-2.5 bg-blue-700 hover:bg-blue-800 text-white font-semibold rounded-lg shadow-md transition focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            Masuk
                        </button>
                    </div>
                </form>

                <p class="mt-8 text-center text-xs text-gray-500">
                    © {{ date('Y') }} TPPL — Politeknik Negeri Cilacap
                </p>
            </div>
        </div>
    </div>
</x-guest-layout>
