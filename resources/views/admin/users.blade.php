@extends('layouts.app')

@section('content')
<div x-data="{ search: @json(request('search', '')) }" class="overflow-x-auto p-4">

    <div class="flex flex-col md:flex-row justify-between items-center mb-4 space-y-2 md:space-y-0">
        <p class="text-gray-800 font-medium text-lg">User Management</p>

        <div class="flex items-center space-x-2">
            <form @submit.prevent="window.location.href='{{ route('admin.users') }}?search=' + encodeURIComponent(search)">
                <input 
                    type="text" 
                    x-model="search" 
                    placeholder="Search..." 
                    class="border border-gray-300 rounded-lg p-2 focus:ring-blue-400 focus:border-blue-400"
                >
            </form>

            <button @click="$dispatch('open-create-modal')" class="px-4 py-2 bg-blue-400 text-white font-semibold rounded-lg shadow hover:bg-blue-500 hover:shadow-md transition">
                Tambah User
            </button>
        </div>
    </div>

    <div class="shadow-md rounded-lg bg-white p-4">
        <table class="min-w-full divide-y divide-gray-200 table-auto rounded-lg">
            <thead class="bg-blue-50 rounded-t-lg">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Username</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Action</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($users as $index => $user)
                <tr class="hover:bg-blue-50">
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">{{ $users->firstItem() + $index }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">{{ $user->name }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">{{ $user->email }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-center text-sm">
                        <div class="flex justify-center space-x-2">
                            <a href="#" class="p-2 bg-blue-100 rounded-lg shadow hover:bg-blue-200 transition">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.477 0 8.268 2.943 9.542 7-1.274 4.057-5.065 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                            </a>

                            <button @click="$dispatch('open-delete-modal', {{ $user->id }})" type="button" class="p-2 bg-red-100 rounded-lg shadow hover:bg-red-200 transition">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5-4h4a2 2 0 012 2v1H8V5a2 2 0 012-2z" />
                                </svg>
                            </button>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="px-6 py-4 text-center text-gray-500">No users found.</td>
                </tr>
                @endforelse
            </tbody>
        </table>

        @if ($users->lastPage() > 1)
        <div class="mt-4 flex justify-end">
            <nav class="inline-flex rounded-lg overflow-hidden shadow bg-white space-x-1">
                @if ($users->onFirstPage())
                    <span class="px-3 py-2 text-gray-400 bg-gray-100 cursor-not-allowed rounded">«</span>
                @else
                    <a href="{{ $users->previousPageUrl() }}&search={{ request('search') }}" class="px-3 py-2 bg-white text-gray-700 hover:bg-blue-100 rounded">«</a>
                @endif

                @foreach ($users->getUrlRange(1, $users->lastPage()) as $page => $url)
                    @if ($page == $users->currentPage())
                        <span class="px-3 py-2 bg-blue-600 text-white font-semibold rounded">{{ $page }}</span>
                    @else
                        <a href="{{ $url }}&search={{ request('search') }}" class="px-3 py-2 bg-white text-gray-700 hover:bg-blue-100 rounded">{{ $page }}</a>
                    @endif
                @endforeach

                @if ($users->hasMorePages())
                    <a href="{{ $users->nextPageUrl() }}&search={{ request('search') }}" class="px-3 py-2 bg-white text-gray-700 hover:bg-blue-100 rounded">»</a>
                @else
                    <span class="px-3 py-2 text-gray-400 bg-gray-100 cursor-not-allowed rounded">»</span>
                @endif
            </nav>
        </div>
        @endif
    </div>
</div>

<div x-data="{ open: false, deleteUserId: null }"
     @open-delete-modal.window="open = true; deleteUserId = $event.detail"
     x-show="open" x-cloak
     class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
    <div class="bg-white rounded-xl shadow-lg w-96 p-6">
        <h2 class="text-lg font-bold mb-4">Konfirmasi Delete</h2>
        <p class="mb-4">Apakah kamu yakin ingin menghapus user ini?</p>
        <div class="flex justify-end space-x-2">
            <button @click="open = false" class="px-4 py-2 rounded-lg bg-gray-200 hover:bg-gray-300">Batal</button>
            <form :action="`/admin/users/${deleteUserId}`" method="POST">
                @csrf
                @method('DELETE')
                <button type="submit" class="px-4 py-2 rounded-lg bg-red-600 text-white hover:bg-red-700">Hapus</button>
            </form>
        </div>
    </div>
</div>

<div x-data="{ open: false, role: '' }"
     @open-create-modal.window="open = true"
     x-show="open" x-cloak
     class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
    <div class="bg-white rounded-xl shadow-lg w-96 p-6">
        <h2 class="text-lg font-bold mb-4">Tambah User</h2>
        <form action="" method="POST" class="space-y-4">
            @csrf
            <div>
                <label class="block text-sm font-medium text-gray-700">Nama</label>
                <input type="text" name="name" class="mt-1 block w-full border border-gray-300 rounded-lg p-2 focus:ring-blue-400 focus:border-blue-400" required>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">Email</label>
                <input type="email" name="email" class="mt-1 block w-full border border-gray-300 rounded-lg p-2 focus:ring-blue-400 focus:border-blue-400" required>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">Role</label>
                <select x-model="role" name="role" class="mt-1 block w-full border border-gray-300 rounded-lg p-2 focus:ring-blue-400 focus:border-blue-400" required>
                    <option value="" selected disabled>Pilih Role</option>
                    <option value="administrator">Administrator</option>
                    <option value="mahasiswa">Mahasiswa</option>
                    <option value="ketua_jurusan">Ketua Jurusan</option>
                </select>
            </div>

            <div x-show="role === 'mahasiswa'" x-transition class="space-y-2">
                <div>
                    <label class="block text-sm font-medium text-gray-700">NIM</label>
                    <input type="text" name="nim" class="mt-1 block w-full border border-gray-300 rounded-lg p-2 focus:ring-blue-400 focus:border-blue-400">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Kelas</label>
                    <input type="text" name="kelas" class="mt-1 block w-full border border-gray-300 rounded-lg p-2 focus:ring-blue-400 focus:border-blue-400">
                </div>
            </div>

            <div class="flex justify-end space-x-2 mt-4">
                <button type="button" @click="open = false" class="px-4 py-2 rounded-lg bg-gray-200 hover:bg-gray-300">Batal</button>
                <button type="submit" class="px-4 py-2 rounded-lg bg-blue-400 text-white hover:bg-blue-500">Simpan</button>
            </div>
        </form>
    </div>
</div>
@endsection
