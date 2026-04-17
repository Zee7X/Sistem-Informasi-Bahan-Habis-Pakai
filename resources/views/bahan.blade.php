@extends('layouts.app')

@section('title', 'Data Bahan')

@section('header')
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h2 class="text-2xl font-bold text-slate-800">Master Bahan</h2>
            <p class="text-sm text-slate-500 font-medium">Manajemen daftar bahan habis pakai laboratorium</p>
        </div>
        @if (auth()->user()->role === 'admin')
            <div class="flex items-center gap-3" x-data>
                <button type="button" @click="$dispatch('open-create-modal')"
                    class="btn-modern bg-blue-600 text-white hover:bg-blue-700 shadow-lg shadow-blue-600/20 flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Tambah Bahan Baru
                </button>
            </div>
        @endif
    </div>
@endsection

@section('content')
    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div class="card-modern p-6 bg-gradient-to-br from-blue-600 to-indigo-700 text-white relative overflow-hidden transition-all hover:shadow-2xl hover:shadow-blue-600/20 group">
            <div class="relative z-10">
                <p class="text-blue-100 text-[10px] font-black uppercase tracking-widest mb-1 opacity-80">Total Jenis Bahan</p>
                <h3 class="text-3xl font-black tracking-tight group-hover:scale-105 transition-transform duration-300">{{ $totalBahan }}</h3>
                <p class="text-blue-200/60 text-[10px] font-medium mt-1 uppercase tracking-tighter">Inventory Managed</p>
            </div>
            <svg class="absolute -right-4 -bottom-4 w-32 h-32 text-white/10 transform rotate-12 group-hover:scale-110 transition-transform duration-500" fill="currentColor" viewBox="0 0 24 24">
                <path d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z" />
            </svg>
        </div>

        <div class="card-modern p-6 bg-gradient-to-br from-emerald-500 to-teal-600 text-white relative overflow-hidden transition-all hover:shadow-2xl hover:shadow-emerald-600/20 group">
            <div class="relative z-10">
                <p class="text-emerald-100 text-[10px] font-black uppercase tracking-widest mb-1 opacity-80">Stok Aman</p>
                <h3 class="text-3xl font-black tracking-tight group-hover:scale-105 transition-transform duration-300">{{ $stokAman }} <span class="text-xs font-bold opacity-60 uppercase">Item</span></h3>
                <p class="text-emerald-200/60 text-[10px] font-medium mt-1 uppercase tracking-tighter">Ready to Use</p>
            </div>
            <svg class="absolute -right-4 -bottom-4 w-32 h-32 text-white/10 transform -rotate-12 group-hover:scale-110 transition-transform duration-500" fill="currentColor" viewBox="0 0 24 24">
                <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
        </div>

        <div class="card-modern p-6 bg-gradient-to-br from-rose-500 to-red-700 text-white relative overflow-hidden transition-all hover:shadow-2xl hover:shadow-rose-600/20 group">
            <div class="relative z-10">
                <p class="text-rose-100 text-[10px] font-black uppercase tracking-widest mb-1 opacity-80">Perlu Re-stock</p>
                <h3 class="text-3xl font-black tracking-tight group-hover:scale-105 transition-transform duration-300">{{ $perluRestock }} <span class="text-xs font-bold opacity-60 uppercase">Item</span></h3>
                <p class="text-rose-200/60 text-[10px] font-medium mt-1 uppercase tracking-tighter">Attention Required</p>
            </div>
            <svg class="absolute -right-4 -bottom-4 w-32 h-32 text-white/10 transform rotate-45 group-hover:scale-110 transition-transform duration-500" fill="currentColor" viewBox="0 0 24 24">
                <path d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
            </svg>
        </div>
    </div>

    <!-- Main Table Card -->
    <div x-data="{ search: '{{ request('search') }}' }"
        class="card-modern overflow-hidden bg-white/80 backdrop-blur-xl border-slate-200/60 shadow-xl shadow-slate-200/40">
        <!-- Table Header Control -->
        <div
            class="p-6 border-b border-slate-100 flex flex-col md:flex-row justify-between items-center gap-4 bg-slate-50/50">
            <div class="relative w-full md:w-96">
                <form id="filterForm" method="GET" action="{{ route('bahan') }}">
                    <input type="text" name="search" value="{{ request('search') }}"
                        oninput="clearTimeout(this.delay); this.delay = setTimeout(() => this.form.submit(), 500)"
                        placeholder="Cari kode atau nama bahan..." class="input-modern w-full pl-11 pr-4 py-2.5 text-sm">
                    <div class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-400">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </div>
            </div>
            <div class="flex items-center gap-2">
                <span class="text-xs font-bold text-slate-400 uppercase tracking-widest">Urutkan:</span>
                <select name="sort" onchange="this.form.submit()"
                    class="text-sm font-semibold text-slate-600 bg-transparent border-none focus:ring-0 cursor-pointer">
                    <option value="latest" {{ request('sort') == 'latest' ? 'selected' : '' }}>Terbaru</option>
                    <option value="name_asc" {{ request('sort') == 'name_asc' ? 'selected' : '' }}>Nama (A-Z)</option>
                    <option value="stok_low" {{ request('sort') == 'stok_low' ? 'selected' : '' }}>Stok Terendah</option>
                    <option value="stok_high" {{ request('sort') == 'stok_high' ? 'selected' : '' }}>Stok Tertinggi</option>
                </select>
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
                            Identitas Bahan</th>
                        <th class="px-6 py-4 text-[10px] font-bold text-slate-500 uppercase tracking-widest text-center">
                            Stok</th>
                        <th class="px-6 py-4 text-[10px] font-bold text-slate-500 uppercase tracking-widest text-center">
                            Min. Stok</th>
                        <th class="px-6 py-4 text-[10px] font-bold text-slate-500 uppercase tracking-widest text-center">
                            Status</th>
                        <th class="px-6 py-4 text-[10px] font-bold text-slate-500 uppercase tracking-widest text-center">
                            Update Terakhir</th>
                        <th
                            class="w-1 px-6 py-4 text-[10px] font-bold text-slate-500 uppercase tracking-widest text-right whitespace-nowrap">
                            Aksi Control</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($bahan as $index => $b)
                        <tr class="hover:bg-blue-50/20 transition-colors group">
                            <td class="px-6 py-4 text-xs font-bold text-slate-400 text-center">
                                {{ str_pad($bahan->firstItem() + $index, 2, '0', STR_PAD_LEFT) }}
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex flex-col">
                                    <span
                                        class="text-sm font-bold text-slate-800 group-hover:text-blue-600 transition-colors leading-tight">{{ $b->nama_bahan }}</span>
                                    <div class="flex items-center gap-1.5 mt-1">
                                        <span
                                            class="text-[9px] font-black bg-slate-100 text-slate-500 px-1.5 py-0.5 rounded border border-slate-200/60 uppercase tracking-wider">{{ $b->kode_bahan }}</span>
                                        <span
                                            class="text-[10px] font-medium text-slate-400 truncate max-w-[250px]">{{ $b->spesifikasi }}</span>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <div class="flex flex-col items-center">
                                    <span class="text-sm font-black text-slate-800">{{ $b->stok }}</span>
                                    <span
                                        class="text-[9px] font-bold text-blue-400 uppercase tracking-tighter">{{ $b->satuan->nama ?? 'Pcs' }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <div class="flex flex-col items-center">
                                    <span class="text-sm font-bold text-slate-500">{{ $b->minimal_stok }}</span>
                                    <span
                                        class="text-[9px] font-bold text-slate-400 uppercase tracking-tighter">{{ $b->satuan->nama ?? 'Pcs' }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-center">
                                @if ($b->stok < $b->minimal_stok)
                                    <span
                                        class="inline-flex items-center px-3 py-1.5 rounded-full bg-rose-500/10 text-rose-600 text-[9px] font-black uppercase tracking-wider border border-rose-500/20 shadow-sm shadow-rose-500/5">
                                        Low Stock
                                    </span>
                                @else
                                    <span
                                        class="inline-flex items-center px-3 py-1.5 rounded-full bg-emerald-500/10 text-emerald-600 text-[9px] font-black uppercase tracking-wider border border-emerald-500/20 shadow-sm shadow-emerald-500/5">
                                        Healthy
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-center">
                                <div class="flex flex-col items-center">
                                    <span
                                        class="text-[10px] font-bold text-slate-600">{{ $b->updated_at ? $b->updated_at->translatedFormat('d M Y • H:i') : '-' }}</span>
                                    <span
                                        class="text-[9px] font-medium text-slate-400 mt-0.5">{{ $b->updated_at ? $b->updated_at->diffForHumans() : '' }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex justify-end gap-2">
                                    @if (auth()->user()->role === 'admin')
                                        <button
                                            @click="$dispatch('open-edit-modal', { 
                                        id: {{ $b->id }},
                                        kode_bahan: @js($b->kode_bahan),
                                        nama_bahan: @js($b->nama_bahan),
                                        spesifikasi: @js($b->spesifikasi),
                                        satuan_id: {{ $b->satuan_id }},
                                        lokasi: @js($b->lokasi),
                                        minimal_stok: @js($b->minimal_stok),
                                        keterangan: @js($b->keterangan)
                                    })"
                                            class="p-2.5 bg-indigo-50 text-indigo-600 hover:bg-indigo-600 hover:text-white rounded-xl border border-indigo-100 shadow-sm transition-all transform hover:-translate-y-0.5 active:scale-95"
                                            title="Sunting Data">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                            </svg>
                                        </button>
                                        <button
                                            @click="$dispatch('open-delete-modal', { id: @js($b->id), name: @js($b->nama_bahan) })"
                                            class="p-2.5 bg-rose-50 text-rose-600 hover:bg-rose-600 hover:text-white rounded-xl border border-rose-100 shadow-sm transition-all transform hover:-translate-y-0.5 active:scale-95"
                                            title="Hapus Data">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5-4h4a2 2 0 012 2v1H8V5a2 2 0 012-2z" />
                                            </svg>
                                        </button>
                                    @else
                                        <span
                                            class="text-[9px] font-black text-slate-300 uppercase tracking-widest border border-slate-100 px-2 py-1 rounded-lg">View
                                            Only</span>
                                    @endif
                                </div>
                            </td>
                            <td class="px-0 py-0"></td> {{-- Spacer Column Body --}}
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-6 py-12 text-center">
                                <div class="flex flex-col items-center">
                                    <div
                                        class="w-16 h-16 bg-slate-50 rounded-full flex items-center justify-center text-slate-300 mb-4">
                                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                                        </svg>
                                    </div>
                                    <h4 class="text-sm font-bold text-slate-500 uppercase tracking-widest">Tidak ada data
                                        ditemukan</h4>
                                    <p class="text-xs text-slate-400 mt-1 font-medium">Coba gunakan kata kunci pencarian
                                        lain atau tambahkan data baru</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Modern Pagination -->
        <div class="p-6 border-t border-slate-50 bg-slate-50/30">
            <div class="flex flex-col md:flex-row items-center justify-between gap-4">
                <div class="flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-blue-500"></span>
                    <p class="text-xs font-bold text-slate-700">
                        {{ $bahan->firstItem() ?? 0 }} - {{ $bahan->lastItem() ?? 0 }} dari {{ $bahan->total() }}
                    </p>
                </div>

                @if ($bahan->lastPage() > 1)
                    <div class="flex items-center gap-1 bg-white p-1 rounded-2xl border border-slate-100 shadow-sm">
                        {{-- First Page --}}
                        @if (!$bahan->onFirstPage())
                            <a href="{{ $bahan->url(1) }}&search={{ request('search') }}"
                                class="w-9 h-9 flex items-center justify-center rounded-xl bg-white text-slate-400 hover:text-blue-600 hover:bg-blue-50 transition-all border border-transparent hover:border-blue-100"
                                title="First Page">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M11 19l-7-7 7-7m8 14l-7-7 7-7" />
                                </svg>
                            </a>
                            <a href="{{ $bahan->previousPageUrl() }}&search={{ request('search') }}"
                                class="w-9 h-9 flex items-center justify-center rounded-xl bg-white text-slate-400 hover:text-blue-600 hover:bg-blue-50 transition-all border border-transparent hover:border-blue-100"
                                title="Previous">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15 19l-7-7 7-7" />
                                </svg>
                            </a>
                        @endif

                        @foreach ($bahan->getUrlRange(max(1, $bahan->currentPage() - 2), min($bahan->lastPage(), $bahan->currentPage() + 2)) as $page => $url)
                            <a href="{{ $url }}&search={{ request('search') }}"
                                class="w-9 h-9 flex items-center justify-center rounded-xl text-[13px] font-bold transition-all
                           {{ $page == $bahan->currentPage() ? 'bg-blue-600 text-white shadow-lg shadow-blue-600/30' : 'text-slate-500 hover:bg-slate-50 hover:text-blue-600' }}">
                                {{ $page }}
                            </a>
                        @endforeach

                        {{-- Next & Last Page --}}
                        @if ($bahan->hasMorePages())
                            <a href="{{ $bahan->nextPageUrl() }}&search={{ request('search') }}"
                                class="w-9 h-9 flex items-center justify-center rounded-xl bg-white text-slate-400 hover:text-blue-600 hover:bg-blue-50 transition-all border border-transparent hover:border-blue-100"
                                title="Next">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 5l7 7-7 7" />
                                </svg>
                            </a>
                            <a href="{{ $bahan->url($bahan->lastPage()) }}&search={{ request('search') }}"
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
            <h2 class="text-xl font-bold text-slate-800 mb-2">Hapus Bahan?</h2>
            <p class="text-sm text-slate-500 mb-6 font-medium">Data bahan <span class="text-slate-800 font-bold"
                    x-text="name"></span> akan dihapus permanen. Tindakan ini tidak bisa dibatalkan.</p>
            <div class="flex items-center gap-3">
                <button @click="open = false"
                    class="flex-1 btn-modern bg-slate-100 text-slate-600 hover:bg-slate-200">Batal</button>
                <form :action="`/bahan/${deleteId}`" method="POST" class="flex-1" x-data="{ clicking: false }" @submit="clicking = true">
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
            class="w-full max-w-xl card-modern shadow-2xl overflow-hidden">
            <div class="p-6 border-b border-slate-100 flex items-center justify-between bg-slate-50/50">
                <h2 class="text-lg font-bold text-slate-800 uppercase tracking-wide">Tambah Bahan Baru</h2>
                <button @click="open = false" class="p-1 hover:bg-slate-100 rounded-lg transition-colors"><svg
                        class="w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path d="M6 18L18 6M6 6l12 12" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                    </svg></button>
            </div>
            <form action="{{ route('bahan.store') }}" method="POST" class="p-6 space-y-4" x-data="{ clicking: false }" @submit="clicking = true">
                @csrf
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label
                            class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1.5 ml-1">Kode
                            Bahan <span class="text-red-500">*</span></label>
                        <input type="text" name="kode_bahan" required placeholder="E.g. BHN-001"
                            oninvalid="this.setCustomValidity('Kode bahan wajib diisi')"
                            oninput="this.setCustomValidity('')" class="input-modern w-full text-sm">
                    </div>
                    <div>
                        <label
                            class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1.5 ml-1">Nama
                            Bahan <span class="text-red-500">*</span></label>
                        <input type="text" name="nama_bahan" required placeholder="E.g. Ethanol 96%"
                            oninvalid="this.setCustomValidity('Nama bahan wajib diisi')"
                            oninput="this.setCustomValidity('')" class="input-modern w-full text-sm">
                    </div>
                </div>
                <div>
                    <label
                        class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1.5 ml-1">Spesifikasi
                        Singkat <span class="text-red-500">*</span></label>
                    <input name="spesifikasi" required placeholder="Ukuran, Brand, atau Grade"
                        oninvalid="this.setCustomValidity('Spesifikasi wajib diisi')" oninput="this.setCustomValidity('')"
                        class="input-modern w-full text-sm">
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label
                            class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1.5 ml-1">Minimal
                            Stok <span class="text-red-500">*</span></label>
                        <input type="number" name="minimal_stok" required placeholder="E.g. 100" min="0"
                            oninvalid="this.setCustomValidity('Stok minimal wajib diisi angka')"
                            oninput="this.setCustomValidity('')" class="input-modern w-full text-sm">
                    </div>
                    <div>
                        <label
                            class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1.5 ml-1">Satuan
                            Dasar <span class="text-red-500">*</span></label>
                        <select name="satuan_id" required oninvalid="this.setCustomValidity('Silakan pilih satuan')"
                            oninput="this.setCustomValidity('')"
                            class="input-modern w-full text-sm appearance-none bg-no-repeat bg-[right_1rem_center]"
                            style="background-image: url('data:image/svg+xml;charset=US-ASCII,%3Csvg%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20width%3D%22292.4%22%20height%3D%22292.4%22%3E%3Cpath%20fill%3D%22%23CBD5E1%22%20d%3D%22M287%2069.4a17.6%2017.6%200%200%200-13-5.4H18.4c-5%200-9.3%201.8-12.9%205.4A17.6%2017.6%200%200%200%200%2082.2c0%205%201.8%209.3%205.4%2012.9l128%20127.9c3.6%203.6%207.8%205.4%2012.8%205.4s9.2-1.8%2012.8-5.4L287%2095c3.5-3.5%205.4-7.8%205.4-12.8%200-5-1.9-9.2-5.5-12.8z%22%2F%3E%3C%2Fsvg%3E'); background-size: .65em auto;">
                            <option value="">Pilih Satuan</option>
                            @foreach ($satuan as $s)
                                <option value="{{ $s->id }}">{{ $s->nama }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div>
                    <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1.5 ml-1">Lokasi
                        Penyimpanan (Opsional)</label>
                    <input type="text" name="lokasi" placeholder="E.g. Rak A-02"
                        class="input-modern w-full text-sm">
                </div>
                <div>
                    <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1.5 ml-1">Catatan
                        Tambahan (Opsional)</label>
                    <textarea name="keterangan" rows="2" class="input-modern w-full text-sm py-2 resize-none"
                        placeholder="Informasi opsional..."></textarea>
                </div>

                <div class="flex justify-end gap-3 pt-4">
                    <button type="button" @click="open = false" :disabled="clicking"
                        class="btn-modern px-6 bg-slate-100 text-slate-600 hover:bg-slate-200">Batalkan</button>
                    <button type="submit" :disabled="clicking"
                        class="btn-modern px-8 bg-blue-600 text-white hover:bg-blue-700 shadow-lg shadow-blue-600/20 flex items-center gap-2">
                        <template x-if="clicking">
                            <div class="w-4 h-4 border-2 border-white/30 border-t-white rounded-full animate-spin"></div>
                        </template>
                        <span x-text="clicking ? 'Menyimpan...' : 'Simpan Data'"></span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Edit Modal -->
    <div x-data="{ open: false, form: {} }" @open-edit-modal.window="open = true; form = $event.detail" x-show="open" x-cloak
        class="fixed inset-0 z-[60] flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm">
        <div x-show="open" x-transition:enter="transition ease-out duration-300 transform"
            x-transition:enter-start="opacity-0 translate-y-8" x-transition:enter-end="opacity-100 translate-y-0"
            class="w-full max-w-xl card-modern shadow-2xl overflow-hidden">
            <div class="p-6 border-b border-slate-100 flex items-center justify-between bg-slate-50/50">
                <h2 class="text-lg font-bold text-slate-800 uppercase tracking-wide">Sunting Data Bahan</h2>
                <button @click="open = false" class="p-1 hover:bg-slate-100 rounded-lg transition-colors"><svg
                        class="w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path d="M6 18L18 6M6 6l12 12" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                    </svg></button>
            </div>
            <form :action="`/bahan/${form.id}`" method="POST" class="p-6 space-y-4" x-data="{ clicking: false }" @submit="clicking = true">
                @csrf
                @method('PUT')
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label
                            class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1.5 ml-1">Kode
                            Bahan <span class="text-red-500">*</span></label>
                        <input type="text" name="kode_bahan" x-model="form.kode_bahan" required
                            oninvalid="this.setCustomValidity('Kode bahan wajib diisi')"
                            oninput="this.setCustomValidity('')" class="input-modern w-full text-sm">
                    </div>
                    <div>
                        <label
                            class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1.5 ml-1">Nama
                            Bahan <span class="text-red-500">*</span></label>
                        <input type="text" name="nama_bahan" x-model="form.nama_bahan" required
                            oninvalid="this.setCustomValidity('Nama bahan wajib diisi')"
                            oninput="this.setCustomValidity('')" class="input-modern w-full text-sm">
                    </div>
                </div>
                <div>
                    <label
                        class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1.5 ml-1">Spesifikasi
                        <span class="text-red-500">*</span></label>
                    <input name="spesifikasi" x-model="form.spesifikasi" required
                        oninvalid="this.setCustomValidity('Spesifikasi wajib diisi')" oninput="this.setCustomValidity('')"
                        class="input-modern w-full text-sm">
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label
                            class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1.5 ml-1">Minimal
                            Stok <span class="text-red-500">*</span></label>
                        <input name="minimal_stok" type="number" x-model="form.minimal_stok" required min="0"
                            oninvalid="this.setCustomValidity('Stok minimal wajib diisi angka')"
                            oninput="this.setCustomValidity('')" class="input-modern w-full text-sm">
                    </div>
                    <div>
                        <label
                            class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1.5 ml-1">Satuan
                            <span class="text-red-500">*</span></label>
                        <select name="satuan_id" x-model="form.satuan_id" required
                            oninvalid="this.setCustomValidity('Silakan pilih satuan')"
                            oninput="this.setCustomValidity('')"
                            class="input-modern w-full text-sm appearance-none bg-no-repeat bg-[right_1rem_center]"
                            style="background-image: url('data:image/svg+xml;charset=US-ASCII,%3Csvg%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20width%3D%22292.4%22%20height%3D%22292.4%22%3E%3Cpath%20fill%3D%22%23CBD5E1%22%20d%3D%22M287%2069.4a17.6%2017.6%200%200%200-13-5.4H18.4c-5%200-9.3%201.8-12.9%205.4A17.6%2017.6%200%200%200%200%2082.2c0%205%201.8%209.3%205.4%2012.9l128%20127.9c3.6%203.6%207.8%205.4%2012.8%205.4s9.2-1.8%2012.8-5.4L287%2095c3.5-3.5%205.4-7.8%205.4-12.8%200-5-1.9-9.2-5.5-12.8z%22%2F%3E%3C%2Fsvg%3E'); background-size: .65em auto;">
                            @foreach ($satuan as $s)
                                <option value="{{ $s->id }}">{{ $s->nama }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div>
                    <label class="block text-[10px] font-bold text-slate-300 uppercase tracking-widest mb-1.5 ml-1">Lokasi
                        (Opsional)</label>
                    <input type="text" name="lokasi" x-model="form.lokasi" class="input-modern w-full text-sm">
                </div>
                <div>
                    <label
                        class="block text-[10px] font-bold text-slate-300 uppercase tracking-widest mb-1.5 ml-1">Keterangan
                        (Opsional)</label>
                    <textarea name="keterangan" x-model="form.keterangan" rows="2"
                        class="input-modern w-full text-sm py-2 resize-none"></textarea>
                </div>

                <div class="flex justify-end gap-3 pt-4">
                    <button type="button" @click="open = false" :disabled="clicking"
                        class="btn-modern px-6 bg-slate-100 text-slate-600 hover:bg-slate-200">Batalkan</button>
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
