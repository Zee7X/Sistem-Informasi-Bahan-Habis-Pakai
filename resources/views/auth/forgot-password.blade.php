<x-guest-layout>
    <div class="min-h-screen flex items-center justify-center bg-gray-50 px-4 py-12">
        <div class="w-full max-w-md bg-white rounded-2xl shadow-lg p-8 border border-gray-200">

            <h1 class="text-2xl font-bold text-blue-900 mb-2 text-center">Reset Password</h1>
            <p class="text-sm text-gray-500 mb-6 text-center">
                Masukkan email kamu dan kami akan mengirim link untuk membuat password baru.
            </p>

            <x-auth-session-status class="mb-4" :status="session('status')" />

            <form method="POST" action="{{ route('password.email') }}" class="space-y-5">
                @csrf

                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                    <input id="email" type="email" name="email" :value="old('email')" required autofocus
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition"
                        placeholder="example@email.com">
                    <x-input-error :messages="$errors->get('email')" class="mt-2 text-red-500 text-sm" />
                </div>

                <div>
                    <button type="submit"
                        class="w-full py-2.5 bg-blue-700 hover:bg-blue-800 text-white font-semibold rounded-lg shadow-md transition focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        Kirim Link Reset Password
                    </button>
                </div>
            </form>

            <p class="mt-6 text-center text-xs text-gray-400">
                © {{ date('Y') }} TPPL — Politeknik Negeri Cilacap
            </p>
        </div>
    </div>
</x-guest-layout>
