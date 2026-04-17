@extends('layouts.app')

@section('title', 'Master Satuan')

@section('header')
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h2 class="text-2xl font-bold text-slate-800">Master Satuan</h2>
            <p class="text-sm text-slate-500 font-medium">Manajemen kategori satuan barang habis pakai</p>
        </div>
        <div class="flex items-center gap-3" x-data>
            <button type="button" @click="$dispatch('open-create-modal')"
                class="btn-modern bg-blue-600 text-white hover:bg-blue-700 shadow-lg shadow-blue-600/20 flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Tambah Satuan Baru
            </button>
        </div>
    </div>
@endsection

@section('content')
    <div class="space-y-6">
        <!-- Main Table Card -->
        <div x-data="{ search: '{{ request('search') }}' }"
            class="card-modern overflow-hidden bg-white/80 backdrop-blur-xl border-slate-200/60 shadow-xl shadow-slate-200/40">
            <!-- Table Header Control -->
            <div
                class="p-6 border-b border-slate-100 flex flex-col md:flex-row justify-between items-center gap-4 bg-slate-50/50">
                <div class="relative w-full md:w-96">
                    <form id="filterForm" method="GET" action="{{ route('admin.satuan.index') }}">
                        <input type="text" name="search" value="{{ request('search') }}"
                            oninput="clearTimeout(this.delay); this.delay = setTimeout(() => this.form.submit(), 500)"
                            placeholder="Cari nama satuan..." class="input-modern w-full pl-11 pr-4 py-2.5 text-sm">
                        <div class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-400">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </div>
                    </form>
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
                                Nama Satuan</th>
                            <th
                                class="w-1 px-6 py-4 text-[10px] font-bold text-slate-500 uppercase tracking-widest text-right whitespace-nowrap">
                                Aksi Control</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($satuan as $index => $item)
                            <tr class="hover:bg-blue-50/20 transition-colors group">
                                <td class="px-6 py-4 text-xs font-bold text-slate-400 text-center">
                                    {{ str_pad($satuan->firstItem() + $index, 2, '0', STR_PAD_LEFT) }}
                                </td>
                                <td class="px-6 py-4">
                                    <span
                                        class="inline-flex px-3 py-1.5 rounded-full bg-blue-500/10 text-blue-600 border border-blue-500/20 text-[10px] font-black uppercase tracking-wider shadow-sm shadow-blue-500/5 transition-transform group-hover:scale-105">
                                        {{ $item->nama }}
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex justify-end gap-2">
                                        <button
                                            @click="$dispatch('open-edit-modal', { id: {{ $item->id }}, nama: '{{ $item->nama }}' })"
                                            class="p-2.5 bg-indigo-50 text-indigo-600 hover:bg-indigo-600 hover:text-white rounded-xl border border-indigo-100 shadow-sm transition-all transform hover:-translate-y-0.5 active:scale-95"
                                            title="Sunting Data">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                            </svg>
                                        </button>
                                        <button
                                            @click="$dispatch('open-delete-modal', { id: @js($item->id), name: @js($item->nama) })"
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
                                <td colspan="3" class="px-6 py-12 text-center">
                                    <div class="flex flex-col items-center">
                                        <div
                                            class="w-16 h-16 bg-slate-50 rounded-full flex items-center justify-center text-slate-300 mb-4">
                                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                                            </svg>
                                        </div>
                                        <h4 class="text-sm font-bold text-slate-500 uppercase tracking-widest">Tidak ada
                                            data ditemukan</h4>
                                        <p class="text-xs text-slate-400 mt-1 font-medium">Coba gunakan kata kunci pencarian
                                            lain atau tambahkan data baru</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Clinical Pagination -->
            <div class="p-6 border-t border-slate-50 bg-slate-50/30">
                <div class="flex flex-col md:flex-row items-center justify-between gap-4">
                    <div class="flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-blue-500"></span>
                        <p class="text-xs font-bold text-slate-700">
                            {{ $satuan->firstItem() ?? 0 }} - {{ $satuan->lastItem() ?? 0 }} dari {{ $satuan->total() }}
                        </p>
                    </div>

                    @if ($satuan->lastPage() > 1)
                        <div class="flex items-center gap-1 bg-white p-1 rounded-2xl border border-slate-100 shadow-sm">
                            {{-- First Page --}}
                            @if (!$satuan->onFirstPage())
                                <a href="{{ $satuan->url(1) }}&search={{ request('search') }}"
                                    class="w-9 h-9 flex items-center justify-center rounded-xl bg-white text-slate-400 hover:text-blue-600 hover:bg-blue-50 transition-all border border-transparent hover:border-blue-100"
                                    title="First Page">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M11 19l-7-7 7-7m8 14l-7-7 7-7" />
                                    </svg>
                                </a>
                                <a href="{{ $satuan->previousPageUrl() }}&search={{ request('search') }}"
                                    class="w-9 h-9 flex items-center justify-center rounded-xl bg-white text-slate-400 hover:text-blue-600 hover:bg-blue-50 transition-all border border-transparent hover:border-blue-100"
                                    title="Previous">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M15 19l-7-7 7-7" />
                                    </svg>
                                </a>
                            @endif

                            @foreach ($satuan->getUrlRange(max(1, $satuan->currentPage() - 2), min($satuan->lastPage(), $satuan->currentPage() + 2)) as $page => $url)
                                <a href="{{ $url }}&search={{ request('search') }}"
                                    class="w-9 h-9 flex items-center justify-center rounded-xl text-[13px] font-bold transition-all
                           {{ $page == $satuan->currentPage() ? 'bg-blue-600 text-white shadow-lg shadow-blue-600/30' : 'text-slate-500 hover:bg-slate-50 hover:text-blue-600' }}">
                                    {{ $page }}
                                </a>
                            @endforeach

                            {{-- Next & Last Page --}}
                            @if ($satuan->hasMorePages())
                                <a href="{{ $satuan->nextPageUrl() }}&search={{ request('search') }}"
                                    class="w-9 h-9 flex items-center justify-center rounded-xl bg-white text-slate-400 hover:text-blue-600 hover:bg-blue-50 transition-all border border-transparent hover:border-blue-100"
                                    title="Next">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 5l7 7-7 7" />
                                    </svg>
                                </a>
                                <a href="{{ $satuan->url($satuan->lastPage()) }}&search={{ request('search') }}"
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
    </div>

    <!-- Delete Modal -->
    <div x-data="{ open: false, deleteId: null, name: '' }"
        @open-delete-modal.window="open = true; deleteId = $event.detail.id; name = $event.detail.name" x-show="open"
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
            <h2 class="text-xl font-bold text-slate-800 mb-2">Hapus Satuan?</h2>
            <p class="text-sm text-slate-500 mb-6 font-medium">Satuan <span class="text-slate-800 font-bold"
                    x-text="name"></span> akan dihapus permanen.</p>
            <div class="flex items-center gap-3">
                <button @click="open = false"
                    class="flex-1 btn-modern bg-slate-100 text-slate-600 hover:bg-slate-200">Batal</button>
                <form x-bind:action="`/admin/satuan/${deleteId}`" method="POST" class="flex-1" x-data="{ clicking: false }"
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
    <div x-data="{ open: false }" @open-create-modal.window="open = true" x-show="open" x-cloak
        class="fixed inset-0 z-[60] flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm">
        <div x-show="open" x-transition:enter="transition ease-out duration-300 transform"
            x-transition:enter-start="opacity-0 -translate-y-8" x-transition:enter-end="opacity-100 translate-y-0"
            class="w-full max-w-sm card-modern shadow-2xl overflow-hidden font-inter">
            <div class="p-5 border-b border-slate-100 flex items-center justify-between bg-slate-50/50">
                <h2 class="text-sm font-bold text-slate-800 uppercase tracking-wider">Tambah Satuan Baru</h2>
                <button @click="open = false" class="p-1 hover:bg-slate-100 rounded-lg transition-colors"><svg
                        class="w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path d="M6 18L18 6M6 6l12 12" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                    </svg></button>
            </div>
            <form action="{{ route('admin.satuan.store') }}" method="POST" class="p-6 space-y-4"
                x-data="{ clicking: false }" @submit="clicking = true">
                @csrf
                <div>
                    <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1.5 ml-1">Nama
                        Satuan <span class="text-red-500">*</span></label>
                    <input type="text" name="nama" required
                        oninvalid="this.setCustomValidity('Nama satuan wajib diisi')" oninput="this.setCustomValidity('')"
                        class="input-modern w-full text-sm" placeholder="E.g. Gram atau Liter">
                </div>
                <div class="flex justify-end gap-3 pt-4">
                    <button type="button" @click="open = false" :disabled="clicking"
                        class="btn-modern px-5 bg-slate-100 text-slate-600 hover:bg-slate-200">Batalkan</button>
                    <button type="submit" :disabled="clicking"
                        class="btn-modern px-8 bg-blue-600 text-white hover:bg-blue-700 shadow-lg shadow-blue-600/20 flex items-center gap-2">
                        <template x-if="clicking">
                            <div class="w-4 h-4 border-2 border-white/30 border-t-white rounded-full animate-spin"></div>
                        </template>
                        <span x-text="clicking ? 'Menyimpan...' : 'Simpan Satuan'"></span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Edit Modal -->
    <div x-data="{ open: false, id: null, nama: '' }" @open-edit-modal.window="open = true; id = $event.detail.id; nama = $event.detail.nama"
        x-show="open" x-cloak
        class="fixed inset-0 z-[60] flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm">
        <div x-show="open" x-transition:enter="transition ease-out duration-300 transform"
            x-transition:enter-start="opacity-0 translate-y-8" x-transition:enter-end="opacity-100 translate-y-0"
            class="w-full max-w-sm card-modern shadow-2xl overflow-hidden font-inter">
            <div class="p-5 border-b border-slate-100 flex items-center justify-between bg-slate-50/50">
                <h2 class="text-sm font-bold text-slate-800 uppercase tracking-wider">Sunting Satuan</h2>
                <button @click="open = false" class="p-1 hover:bg-slate-100 rounded-lg transition-colors"><svg
                        class="w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path d="M6 18L18 6M6 6l12 12" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                    </svg></button>
            </div>
            <form x-bind:action="`/admin/satuan/${id}`" method="POST" class="p-6 space-y-4" x-data="{ clicking: false }"
                @submit="clicking = true">
                @csrf @method('PUT')
                <div>
                    <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1.5 ml-1">Nama
                        Satuan <span class="text-red-500">*</span></label>
                    <input type="text" name="nama" x-model="nama" required
                        oninvalid="this.setCustomValidity('Nama satuan wajib diisi')" oninput="this.setCustomValidity('')"
                        class="input-modern w-full text-sm">
                </div>
                <div class="flex justify-end gap-3 pt-4">
                    <button type="button" @click="open = false" :disabled="clicking"
                        class="btn-modern px-5 bg-slate-100 text-slate-600 hover:bg-slate-200">Batalkan</button>
                    <button type="submit" :disabled="clicking"
                        class="btn-modern px-8 bg-blue-600 text-white hover:bg-blue-700 shadow-lg shadow-blue-600/20 flex items-center gap-2">
                        <template x-if="clicking">
                            <div class="w-4 h-4 border-2 border-white/30 border-t-white rounded-full animate-spin"></div>
                        </template>
                        <span x-text="clicking ? 'Memperbarui...' : 'Perbarui Data'"></span>
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection
