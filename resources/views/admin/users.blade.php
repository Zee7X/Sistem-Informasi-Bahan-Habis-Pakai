@extends('layouts.app')

@section('title', 'Manajemen Pengguna')

@section('header')
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h2 class="text-2xl font-bold text-slate-800">Manajemen Pengguna</h2>
            <p class="text-sm text-slate-500 font-medium">Pengelolaan akses dan role personil laboratorium</p>
        </div>
        <div class="flex items-center gap-3" x-data>
            <button @click="$dispatch('open-create-modal')"
                class="btn-modern bg-blue-600 text-white hover:bg-blue-700 shadow-lg shadow-blue-600/20 flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Tambah User Baru
            </button>
        </div>
    </div>
@endsection

@section('content')
    <!-- Stats Cards (Simplified from data) -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div class="card-modern p-6 bg-gradient-to-br from-blue-600 to-indigo-700 text-white relative overflow-hidden transition-all hover:shadow-2xl hover:shadow-blue-600/20 group">
            <div class="relative z-10">
                <p class="text-blue-100 text-[10px] font-black uppercase tracking-widest mb-1 opacity-80">Total Pengguna</p>
                <h3 class="text-3xl font-black tracking-tight group-hover:scale-105 transition-transform duration-300">{{ $users->total() }}</h3>
                <p class="text-blue-200/60 text-[10px] font-medium mt-1 uppercase tracking-tighter">Registered Personnel</p>
            </div>
            <svg class="absolute -right-4 -bottom-4 w-32 h-32 text-white/10 transform rotate-12 group-hover:scale-110 transition-transform duration-500" fill="currentColor" viewBox="0 0 24 24">
                <path d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
            </svg>
        </div>
        <div class="card-modern p-6 bg-gradient-to-br from-emerald-500 to-teal-600 text-white relative overflow-hidden transition-all hover:shadow-2xl hover:shadow-emerald-600/20 group">
            <div class="relative z-10">
                <p class="text-emerald-100 text-[10px] font-black uppercase tracking-widest mb-1 opacity-80">Sistem Integrity</p>
                <h3 class="text-2xl font-black tracking-tight group-hover:scale-105 transition-transform duration-300">Verified Access</h3>
                <p class="text-emerald-200/60 text-[10px] font-medium mt-1 uppercase tracking-tighter">Secure Ecosystem</p>
            </div>
            <svg class="absolute -right-4 -bottom-4 w-32 h-32 text-white/10 transform -rotate-12 group-hover:scale-110 transition-transform duration-500" fill="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04M12 2.944a11.955 11.955 0 01-8.618 3.04m0 0V9a11.003 11.003 0 001.535 5.591M12 2.944a11.955 11.955 0 018.618 3.04m0 0V9a11.003 11.003 0 01-1.535 5.591M12 21a9.003 9.003 0 008.312-5.591M12 21a9.003 9.003 0 01-8.312-5.591M12 21c2.485 0 4.5-4.03 4.5-9s-2.015-9-4.5-9-4.5 4.03-4.5 9 2.015 9 4.5 9z" />
            </svg>
        </div>
        <div class="card-modern p-6 bg-gradient-to-br from-indigo-600 to-purple-700 text-white relative overflow-hidden transition-all hover:shadow-2xl hover:shadow-indigo-600/20 group">
            <div class="relative z-10">
                <p class="text-indigo-100 text-[10px] font-black uppercase tracking-widest mb-1 opacity-80">Total Mahasiswa</p>
                <h3 class="text-3xl font-black tracking-tight group-hover:scale-105 transition-transform duration-300">{{ \App\Models\User::where('role', 'mahasiswa')->count() }}</h3>
                <p class="text-indigo-200/60 text-[10px] font-medium mt-1 uppercase tracking-tighter">Active Students</p>
            </div>
            <svg class="absolute -right-4 -bottom-4 w-32 h-32 text-white/10 transform rotate-12 group-hover:scale-110 transition-transform duration-500" fill="currentColor" viewBox="0 0 24 24">
                <path d="M12 14l9-5-9-5-9 5 9 5z" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" />
                <path d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" />
            </svg>
        </div>
    </div>

    <!-- Main Table Card -->
    <div x-data="{ search: @json(request('search', '')) }"
        class="card-modern overflow-hidden bg-white/80 backdrop-blur-xl border-slate-200/60 shadow-xl shadow-slate-200/40">
        <!-- Table Header Control -->
        <div
            class="p-6 border-b border-slate-100 flex flex-col md:flex-row justify-between items-center gap-4 bg-slate-50/50">
            <div class="relative w-full md:w-96">
                <form method="GET" action="{{ route('admin.users') }}">
                    <input type="text" name="search" value="{{ request('search') }}"
                        oninput="clearTimeout(this.delay); this.delay = setTimeout(() => this.form.submit(), 500)"
                        placeholder="Cari nama, email, atau username..."
                        class="input-modern w-full pl-11 pr-4 py-2.5 text-sm">
                    <div class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-400">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </div>
                </form>
            </div>
            <div class="flex items-center gap-2">
                <span class="text-[10px] font-black text-slate-300 uppercase tracking-[0.2em]">User Management
                    Console</span>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50/80">
                        <th
                            class="w-1 px-6 py-4 text-[10px] font-bold text-slate-500 uppercase tracking-widest text-center">
                            No</th>
                        <th class="px-6 py-4 text-[10px] font-bold text-slate-500 uppercase tracking-widest text-left">
                            Profil Pengguna</th>
                        <th class="px-6 py-4 text-[10px] font-bold text-slate-500 uppercase tracking-widest text-left">
                            Kontak & Akses</th>
                        <th class="px-6 py-4 text-[10px] font-bold text-slate-500 uppercase tracking-widest text-center">
                            Role</th>
                        <th
                            class="w-1 px-6 py-4 text-[10px] font-bold text-slate-500 uppercase tracking-widest text-right whitespace-nowrap">
                            Aksi Control</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($users as $index => $user)
                        <tr class="hover:bg-blue-50/20 transition-colors group">
                            <td class="px-6 py-4 text-xs font-bold text-slate-400 text-center">
                                {{ str_pad($users->firstItem() + $index, 2, '0', STR_PAD_LEFT) }}
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex flex-col">
                                    <span
                                        class="text-sm font-bold text-slate-800 group-hover:text-blue-600 transition-colors leading-tight">{{ $user->name }}</span>
                                    <div class="flex items-center gap-1.5 mt-1">
                                        <span
                                            class="text-[9px] font-black bg-slate-100 text-slate-400 px-1.5 py-0.5 rounded border border-slate-200/60 uppercase tracking-tight">ID:
                                            #{{ $user->id }}</span>
                                        <span
                                            class="text-[10px] font-medium text-slate-400">{{ $user->username ?? 'No Username' }}</span>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex flex-col">
                                    <span
                                        class="text-[11px] font-bold text-slate-600 leading-none">{{ $user->email }}</span>
                                    @if ($user->role === 'mahasiswa')
                                        <span
                                            class="text-[9px] font-medium text-slate-400 mt-1 uppercase tracking-tighter">{{ $user->nim ?? '-' }}
                                            • {{ $user->kelas ?? '-' }}</span>
                                    @else
                                        <span
                                            class="text-[9px] mt-1 uppercase tracking-tighter text-blue-500 font-bold">System
                                            Personnel</span>
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span @class([
                                    'inline-flex px-3 py-1 rounded-full text-[9px] font-black uppercase tracking-wider border transition-all',
                                    'bg-indigo-500/10 text-indigo-600 border-indigo-500/20 shadow-sm shadow-indigo-500/5' =>
                                        $user->role === 'administrator' || $user->role === 'admin',
                                    'bg-blue-500/10 text-blue-600 border-blue-500/20 shadow-sm shadow-blue-500/5' =>
                                        $user->role === 'mahasiswa',
                                    'bg-emerald-500/10 text-emerald-600 border-emerald-500/20 shadow-sm shadow-emerald-500/5' =>
                                        $user->role === 'ketua_jurusan',
                                    'bg-slate-500/10 text-slate-600 border-slate-500/20' => !in_array(
                                        $user->role,
                                        ['administrator', 'admin', 'mahasiswa', 'ketua_jurusan']),
                                ])>
                                    {{ str_replace('_', ' ', $user->role) }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex justify-end gap-2">
                                    <button
                                        @click="$dispatch('open-delete-modal', { id: @js($user->id), name: @js($user->name) })"
                                        class="p-2.5 bg-rose-50 text-rose-600 hover:bg-rose-600 hover:text-white rounded-xl border border-rose-100 shadow-sm transition-all transform hover:-translate-y-0.5 active:scale-95"
                                        title="Hapus Data">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5-4h4a2 2 0 012 2v1H8V5a2 2 0 012-2z" />
                                        </svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center">
                                <div class="flex flex-col items-center">
                                    <div
                                        class="w-16 h-16 bg-slate-50 rounded-full flex items-center justify-center text-slate-300 mb-4">
                                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                                        </svg>
                                    </div>
                                    <h4 class="text-sm font-bold text-slate-500 uppercase tracking-widest">Tidak ada user
                                        ditemukan</h4>
                                    <p class="text-xs text-slate-400 mt-1 font-medium">Coba gunakan kata kunci pencarian
                                        lain atau tambahkan user baru</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="p-6 border-t border-slate-50 bg-slate-50/30">
            <div class="flex flex-col md:flex-row items-center justify-between gap-4">
                <div class="flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-blue-500"></span>
                    <p class="text-xs font-bold text-slate-700">
                        {{ $users->firstItem() ?? 0 }} - {{ $users->lastItem() ?? 0 }} dari {{ $users->total() }} data
                    </p>
                </div>

                @if ($users->lastPage() > 1)
                    <div class="flex items-center gap-1 bg-white p-1 rounded-2xl border border-slate-100 shadow-sm">
                        {{-- First Page --}}
                        @if (!$users->onFirstPage())
                            <a href="{{ $users->url(1) }}&search={{ request('search') }}"
                                class="w-9 h-9 flex items-center justify-center rounded-xl bg-white text-slate-400 hover:text-blue-600 hover:bg-blue-50 transition-all border border-transparent hover:border-blue-100"
                                title="First Page">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M11 19l-7-7 7-7m8 14l-7-7 7-7" />
                                </svg>
                            </a>
                            <a href="{{ $users->previousPageUrl() }}&search={{ request('search') }}"
                                class="w-9 h-9 flex items-center justify-center rounded-xl bg-white text-slate-400 hover:text-blue-600 hover:bg-blue-50 transition-all border border-transparent hover:border-blue-100"
                                title="Previous">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15 19l-7-7 7-7" />
                                </svg>
                            </a>
                        @endif

                        @foreach ($users->getUrlRange(max(1, $users->currentPage() - 2), min($users->lastPage(), $users->currentPage() + 2)) as $page => $url)
                            <a href="{{ $url }}&search={{ request('search') }}"
                                class="w-9 h-9 flex items-center justify-center rounded-xl text-[13px] font-bold transition-all
                               {{ $page == $users->currentPage() ? 'bg-blue-600 text-white shadow-lg shadow-blue-600/30' : 'text-slate-500 hover:bg-slate-50 hover:text-blue-600' }}">
                                {{ $page }}
                            </a>
                        @endforeach

                        {{-- Next & Last Page --}}
                        @if ($users->hasMorePages())
                            <a href="{{ $users->nextPageUrl() }}&search={{ request('search') }}"
                                class="w-9 h-9 flex items-center justify-center rounded-xl bg-white text-slate-400 hover:text-blue-600 hover:bg-blue-50 transition-all border border-transparent hover:border-blue-100"
                                title="Next">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 5l7 7-7 7" />
                                </svg>
                            </a>
                            <a href="{{ $users->url($users->lastPage()) }}&search={{ request('search') }}"
                                class="w-9 h-9 flex items-center justify-center rounded-xl bg-white text-slate-400 hover:text-blue-600 hover:bg-blue-50 transition-all border border-transparent hover:border-blue-100"
                                title="Last Page">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M13 5l7 7-7 7M5 5l7 7-7 7" />
                                </svg>
                            </a>
                        @endif
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Delete Modal -->
    <div x-data="{ open: false, deleteUserId: null, name: '' }"
        @open-delete-modal.window="open = true; deleteUserId = $event.detail.id; name = $event.detail.name" x-show="open"
        x-cloak class="fixed inset-0 z-[60] flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm">
        <div x-show="open" x-transition:enter="transition ease-out duration-300 transform"
            x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
            class="w-full max-w-sm card-modern shadow-2xl overflow-hidden p-6 text-center">
            <div class="w-16 h-16 bg-red-50 rounded-full flex items-center justify-center text-red-600 mx-auto mb-4">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5-4h4a2 2 0 012 2v1H8V5a2 2 0 012-2z" />
                </svg>
            </div>
            <h2 class="text-xl font-bold text-slate-800 mb-2">Hapus Pengguna?</h2>
            <p class="text-sm text-slate-500 mb-6 font-medium">Akses untuk <span class="text-slate-800 font-bold"
                    x-text="name"></span> akan dicabut secara permanen dari sistem.</p>
            <div class="flex items-center gap-3">
                <button @click="open = false"
                    class="flex-1 btn-modern bg-slate-100 text-slate-600 hover:bg-slate-200">Batal</button>
                <form :action="`/admin/users/${deleteUserId}`" method="POST" class="flex-1" x-data="{ clicking: false }"
                    @submit="clicking = true">
                    @csrf @method('DELETE')
                    <button type="submit" :disabled="clicking"
                        class="w-full btn-modern bg-red-600 text-white hover:bg-red-700 shadow-lg shadow-red-600/20 flex items-center justify-center gap-2">
                        <template x-if="clicking">
                            <div class="w-4 h-4 border-2 border-white/30 border-t-white rounded-full animate-spin"></div>
                        </template>
                        <span x-text="clicking ? 'Menghapus...' : 'Ya, Hapus'"></span>
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Create Modal -->
    <div x-data="{ open: false, roleID: '' }" @open-create-modal.window="open = true" x-show="open" x-cloak
        class="fixed inset-0 z-[60] flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm overflow-y-auto">
        <div x-show="open" x-transition:enter="transition ease-out duration-300 transform"
            x-transition:enter-start="opacity-0 -translate-y-8" x-transition:enter-end="opacity-100 translate-y-0"
            class="w-full max-w-md card-modern shadow-2xl overflow-hidden font-inter">
            <div class="p-5 border-b border-slate-100 flex items-center justify-between bg-slate-50/50">
                <h2 class="text-sm font-bold text-slate-800 uppercase tracking-wider">Tambah User Baru</h2>
                <button @click="open = false" class="p-1 hover:bg-slate-100 rounded-lg transition-colors"><svg
                        class="w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path d="M6 18L18 6M6 6l12 12" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                    </svg></button>
            </div>
            <form action="{{ route('admin.users') }}" method="POST" class="p-6 space-y-4" x-data="{ clicking: false }"
                @submit="clicking = true">
                @csrf
                <div>
                    <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1.5 ml-1">Nama
                        Lengkap <span class="text-red-500">*</span></label>
                    <input type="text" name="name" class="input-modern w-full text-sm" placeholder="E.g. Jhon Doe"
                        required>
                </div>
                <div>
                    <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1.5 ml-1">Email
                        Address <span class="text-red-500">*</span></label>
                    <input type="email" name="email" class="input-modern w-full text-sm"
                        placeholder="user@pnc.ac.id" required>
                </div>
                <div>
                    <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1.5 ml-1">Role
                        Akun <span class="text-red-500">*</span></label>
                    <select x-model="roleID" name="role" class="input-modern w-full text-sm" required>
                        <option value="" selected disabled>Pilih Role</option>
                        <option value="administrator">Administrator</option>
                        <option value="mahasiswa">Mahasiswa</option>
                        <option value="ketua_jurusan">Ketua Jurusan</option>
                    </select>
                </div>

                <div x-show="roleID === 'mahasiswa'" x-transition
                    class="space-y-4 bg-blue-50/50 p-4 rounded-xl border border-blue-100/50">
                    <div>
                        <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1.5 ml-1">NIM
                            Mahasiswa</label>
                        <input type="text" name="nim" class="input-modern w-full text-sm placeholder:opacity-50"
                            placeholder="E.g. 210102003">
                    </div>
                    <div>
                        <label
                            class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1.5 ml-1">Kelas</label>
                        <input type="text" name="kelas" class="input-modern w-full text-sm placeholder:opacity-50"
                            placeholder="E.g. TI-2C">
                    </div>
                </div>

                <div class="flex justify-end gap-3 pt-6">
                    <button type="button" @click="open = false" :disabled="clicking"
                        class="btn-modern px-5 bg-slate-100 text-slate-600 hover:bg-slate-200">Batalkan</button>
                    <button type="submit" :disabled="clicking"
                        class="btn-modern px-8 bg-blue-600 text-white hover:bg-blue-700 shadow-lg shadow-blue-600/20 flex items-center gap-2">
                        <template x-if="clicking">
                            <div class="w-4 h-4 border-2 border-white/30 border-t-white rounded-full animate-spin"></div>
                        </template>
                        <span x-text="clicking ? 'Menyimpan...' : 'Simpan User'"></span>
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection
