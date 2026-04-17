@extends('layouts.app')

@section('title', 'Bahan Masuk')

@section('header')
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h2 class="text-2xl font-bold text-slate-800">Bahan Masuk</h2>
            <p class="text-sm text-slate-500 font-medium">Catat riwayat penerimaan dan penambahan stok bahan</p>
        </div>
        <div class="flex items-center gap-3" x-data>
            <button type="button" @click="$dispatch('open-create-modal')"
                class="btn-modern bg-blue-600 text-white hover:bg-blue-700 shadow-lg shadow-blue-600/20 flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Catat Stok Masuk
            </button>
        </div>
    </div>
@endsection

@section('content')
    <turbo-frame id="main-content">
        <div class="space-y-6">
            <!-- Main Table Card -->
            <div class="card-modern overflow-hidden bg-white/80 backdrop-blur-xl border-slate-200/60 shadow-xl shadow-slate-200/40">
                <!-- Table Header Control -->
                <div class="p-6 border-b border-slate-100 bg-slate-50/30">
                    <form id="filterForm" method="GET" action="{{ route('admin.bahan-masuk.index') }}" 
                        x-data="{ 
                            submitFilters() {
                                const start = this.$refs.startDate.value;
                                const end = this.$refs.endDate.value;
                                
                                // Submit if both dates are present OR both are empty
                                if ((start && end) || (!start && !end)) {
                                    if (start && end && new Date(end) < new Date(start)) {
                                        this.$refs.endDate.value = '';
                                        return;
                                    }
                                    this.$refs.submitBtn.click();
                                }
                            },
                            updateSearch() {
                                clearTimeout(this.searchTimeout);
                                this.searchTimeout = setTimeout(() => this.$refs.submitBtn.click(), 500);
                            }
                        }"
                        class="flex flex-col lg:flex-row items-center gap-4">
                        
                        <!-- Search Input -->
                        <div class="relative w-full lg:flex-1 group">
                            <input type="text" name="search" value="{{ request('search') }}"
                                @input="updateSearch()"
                                placeholder="Cari bahan, kode, atau spesifikasi..."
                                class="input-modern w-full pl-11 pr-4 py-2.5 text-sm bg-white focus:ring-blue-500/20">
                            <div class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 group-focus-within:text-blue-500 transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                </svg>
                            </div>
                        </div>

                        <!-- Date Filters -->
                        <div class="flex items-center gap-2 w-full lg:w-auto">
                            <div class="relative flex-1 lg:w-44 group">
                                <input type="date" name="start_date" x-ref="startDate" value="{{ request('start_date') }}"
                                    @change="submitFilters()"
                                    class="input-modern w-full pl-12 pr-2 py-2 text-xs bg-white focus:ring-blue-500/20">
                                <span class="absolute left-3 top-1/2 -translate-y-1/2 text-[9px] font-black text-slate-400 uppercase tracking-tighter pointer-events-none group-focus-within:text-blue-500 transition-colors">Dari</span>
                            </div>
                            <div class="text-slate-300 font-bold hidden sm:block">→</div>
                            <div class="relative flex-1 lg:w-44 group">
                                <input type="date" name="end_date" x-ref="endDate" value="{{ request('end_date') }}"
                                    @change="submitFilters()"
                                    :min="$refs.startDate?.value"
                                    class="input-modern w-full pl-16 pr-2 py-2 text-xs bg-white focus:ring-blue-500/20">
                                <span class="absolute left-3 top-1/2 -translate-y-1/2 text-[9px] font-black text-slate-400 uppercase tracking-tighter pointer-events-none group-focus-within:text-blue-500 transition-colors">Sampai</span>
                            </div>

                            <button type="submit" x-ref="submitBtn" class="hidden"></button>

                            @if(request('search') || request('start_date') || request('end_date'))
                                <a href="{{ route('admin.bahan-masuk.index') }}"
                                    class="p-2.5 bg-slate-100 text-slate-500 hover:bg-rose-50 hover:text-rose-600 rounded-xl transition-all border border-slate-200"
                                    title="Reset Semua Filter">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                </a>
                            @endif
                        </div>
                    </form>
                </div>
                    </form>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-slate-50/80">
                                <th
                                    class="w-1 px-6 py-4 text-[10px] font-bold text-slate-500 uppercase tracking-widest text-center">
                                    No</th>
                                <th
                                    class="px-6 py-4 text-[10px] font-bold text-slate-500 uppercase tracking-widest text-left">
                                    Tanggal</th>
                                <th
                                    class="px-6 py-4 text-[10px] font-bold text-slate-500 uppercase tracking-widest text-left">
                                    Bahan</th>
                                <th
                                    class="px-6 py-4 text-[10px] font-bold text-slate-500 uppercase tracking-widest text-center">
                                    Jumlah</th>
                                <th
                                    class="px-6 py-4 text-[10px] font-bold text-slate-500 uppercase tracking-widest text-left">
                                    Pemasok</th>
                                <th
                                    class="w-1 px-6 py-4 text-[10px] font-bold text-slate-500 uppercase tracking-widest text-right whitespace-nowrap">
                                    Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse($masuk as $index => $item)
                                <tr class="hover:bg-blue-50/20 transition-colors group">
                                    <td class="px-6 py-4 text-xs font-bold text-slate-400 text-center">
                                        {{ str_pad($masuk->firstItem() + $index, 2, '0', STR_PAD_LEFT) }}
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="flex flex-col">
                                            <span
                                                class="text-[10px] font-bold text-slate-600 leading-tight">{{ $item->created_at->translatedFormat('d M Y • H:i') }}</span>
                                            <span
                                                class="text-[9px] font-medium text-slate-400 mt-0.5">{{ $item->created_at->diffForHumans() }}</span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="flex flex-col">
                                            <span
                                                class="text-sm font-bold text-slate-800 leading-tight group-hover:text-blue-600 transition-colors">{{ $item->bahan->nama_bahan }}</span>
                                            <div class="flex items-center gap-1.5 mt-1">
                                                <span
                                                    class="text-[9px] font-black bg-slate-100 text-slate-500 px-1.5 py-0.5 rounded border border-slate-200/60 uppercase">{{ $item->bahan->kode_bahan }}</span>
                                                <span
                                                    class="text-[10px] font-medium text-slate-400 leading-none">{{ $item->bahan->spesifikasi }}</span>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        <div class="inline-flex flex-col items-center">
                                            <span class="text-sm font-black text-blue-600">+{{ $item->jumlah }}</span>
                                            <span
                                                class="text-[9px] font-bold text-slate-400 uppercase tracking-tighter">{{ $item->bahan->satuan->nama ?? 'Unit' }}</span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <span
                                            class="text-xs font-semibold text-slate-600">{{ $item->pemasok ?? '-' }}</span>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="flex justify-end gap-2 text-right">
                                            <button
                                                @click="$dispatch('open-delete-modal', { id: @js($item->id), name: @js($item->bahan->nama_bahan . ' (+' . $item->jumlah . ')') })"
                                                class="p-2.5 bg-rose-50 text-rose-600 hover:bg-rose-600 hover:text-white rounded-xl border border-rose-100 shadow-sm transition-all transform hover:-translate-y-0.5"
                                                title="Hapus Riwayat">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5-4h4a2 2 0 012 2v1H8V5a2 2 0 012-2z" />
                                                </svg>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-12 text-center">
                                        <div class="flex flex-col items-center">
                                            <div
                                                class="w-16 h-16 bg-slate-50 rounded-full flex items-center justify-center text-slate-300 mb-4">
                                                <svg class="w-8 h-8" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                                                </svg>
                                            </div>
                                            <h4 class="text-sm font-bold text-slate-500 uppercase tracking-widest">Belum ada
                                                riwayat stok masuk</h4>
                                            <p class="text-xs text-slate-400 mt-1 font-medium">Klik tombol di atas untuk
                                                mencatat penerimaan bahan baru</p>
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
                                {{ $masuk->firstItem() ?? 0 }} - {{ $masuk->lastItem() ?? 0 }} dari {{ $masuk->total() }}
                            </p>
                        </div>

                        @if ($masuk->lastPage() > 1)
                            <div class="flex items-center gap-1 bg-white p-1 rounded-2xl border border-slate-100 shadow-sm">
                                {{-- First Page --}}
                                @if (!$masuk->onFirstPage())
                                    <a href="{{ $masuk->url(1) }}"
                                        class="w-9 h-9 flex items-center justify-center rounded-xl bg-white text-slate-400 hover:text-blue-600 hover:bg-blue-50 transition-all border border-transparent hover:border-blue-100"
                                        title="First Page">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M11 19l-7-7 7-7m8 14l-7-7 7-7" />
                                        </svg>
                                    </a>
                                    <a href="{{ $masuk->previousPageUrl() }}"
                                        class="w-9 h-9 flex items-center justify-center rounded-xl bg-white text-slate-400 hover:text-blue-600 hover:bg-blue-50 transition-all border border-transparent hover:border-blue-100"
                                        title="Previous">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M15 19l-7-7 7-7" />
                                        </svg>
                                    </a>
                                @endif
                
                                @foreach ($masuk->getUrlRange(max(1, $masuk->currentPage() - 2), min($masuk->lastPage(), $masuk->currentPage() + 2)) as $page => $url)
                                    <a href="{{ $url }}"
                                        class="w-9 h-9 flex items-center justify-center rounded-xl text-[13px] font-bold transition-all
                                   {{ $page == $masuk->currentPage() ? 'bg-blue-600 text-white shadow-lg shadow-blue-600/30' : 'text-slate-500 hover:bg-slate-50 hover:text-blue-600' }}">
                                        {{ $page }}
                                    </a>
                                @endforeach
                
                                {{-- Next & Last Page --}}
                                @if ($masuk->hasMorePages())
                                    <a href="{{ $masuk->nextPageUrl() }}"
                                        class="w-9 h-9 flex items-center justify-center rounded-xl bg-white text-slate-400 hover:text-blue-600 hover:bg-blue-50 transition-all border border-transparent hover:border-blue-100"
                                        title="Next">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M9 5l7 7-7 7" />
                                        </svg>
                                    </a>
                                    <a href="{{ $masuk->url($masuk->lastPage()) }}"
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
                <h2 class="text-xl font-bold text-slate-800 mb-2">Hapus Riwayat?</h2>
                <p class="text-sm text-slate-500 mb-6 font-medium">Data <span class="text-slate-800 font-bold"
                        x-text="name"></span> akan dihapus dan stok akan dikurangi kembali secara otomatis.</p>
                <div class="flex items-center gap-3">
                    <button @click="open = false"
                        class="flex-1 btn-modern bg-slate-100 text-slate-600 hover:bg-slate-200">Batal</button>
                    <form x-bind:action="`/admin/bahan-masuk/${deleteId}`" method="POST" class="flex-1"
                        x-data="{ clicking: false }" @submit="clicking = true">
                        @csrf @method('DELETE')
                        <button type="submit" :disabled="clicking"
                            class="w-full btn-modern bg-red-600 text-white hover:bg-red-700 shadow-lg shadow-red-600/20 flex items-center justify-center gap-2">
                            <template x-if="clicking">
                                <div class="w-4 h-4 border-2 border-white/30 border-t-white rounded-full animate-spin">
                                </div>
                            </template>
                            <span x-text="clicking ? 'Menghapus...' : 'Ya, Hapus'"></span>
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Create Modal -->
        <div x-data="{ open: false }" @open-create-modal.window="open = true" x-show="open" x-cloak
            class="fixed inset-0 z-[60] flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm overflow-y-auto">
            <div x-show="open" x-transition:enter="transition ease-out duration-300 transform"
                x-transition:enter-start="opacity-0 -translate-y-8" x-transition:enter-end="opacity-100 translate-y-0"
                class="w-full max-w-md card-modern shadow-2xl !overflow-visible">
                <div class="p-6 border-b border-slate-100 flex items-center justify-between bg-slate-50/50">
                    <h2 class="text-lg font-bold text-slate-800 uppercase tracking-wide">Pencatatan Stok Masuk</h2>
                    <button @click="open = false" class="p-1 hover:bg-slate-100 rounded-lg transition-colors"><svg
                            class="w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path d="M6 18L18 6M6 6l12 12" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round" />
                        </svg></button>
                </div>
                <form action="{{ route('admin.bahan-masuk.store') }}" method="POST" class="p-6 space-y-4"
                    x-data="{ clicking: false }" @submit="clicking = true">
                    @csrf
                    <div x-data="{
                        dropdownOpen: false,
                        search: '',
                        items: [],
                        selected: null,
                        page: 1,
                        hasMore: true,
                        loading: false,
                    
                        async fetchData(reset = false) {
                            if (this.loading) return;
                            if (reset) {
                                this.page = 1;
                                this.items = [];
                                this.hasMore = true;
                            }
                            if (!this.hasMore) return;
                    
                            this.loading = true;
                            try {
                                const response = await fetch(`{{ route('api.bahan.search') }}?q=${this.search}&page=${this.page}`);
                                const data = await response.json();
                                this.items = [...this.items, ...data.items];
                                this.hasMore = data.hasMore;
                                this.page++;
                            } catch (e) {
                                console.error('Fetch error:', e);
                            } finally {
                                this.loading = false;
                            }
                        },
                    
                        handleScroll(e) {
                            const { scrollTop, scrollHeight, clientHeight } = e.target;
                            if (scrollHeight - scrollTop <= clientHeight + 50) {
                                this.fetchData();
                            }
                        },
                    
                        selectItem(item) {
                            this.selected = item;
                            this.dropdownOpen = false;
                            this.search = '';
                        }
                    }" x-init="$watch('search', () => fetchData(true));
                    fetchData();" class="relative">
                        <label
                            class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1.5 ml-1">Pilih
                            Bahan <span class="text-red-500">*</span></label>

                        <!-- Hidden Input for Form -->
                        <input type="hidden" name="bahan_id" :value="selected?.id" required>

                        <!-- Trigger Button -->
                        <button type="button" @click="dropdownOpen = !dropdownOpen"
                            class="input-modern w-full h-auto min-h-[44px] py-2 text-sm flex items-center justify-between bg-white text-left focus:ring-2 focus:ring-blue-500/20"
                            :class="dropdownOpen ? 'border-blue-400 ring-2 ring-blue-500/10' : ''">
                            <div class="flex flex-col truncate pr-2">
                                <span x-text="selected ? selected.nama_bahan : '-- Cari & Pilih Nama Bahan --'"
                                    :class="selected ? 'text-slate-800 font-bold leading-tight' : 'text-slate-400'"></span>
                                <template x-if="selected">
                                    <span class="text-[10px] text-slate-400 font-medium truncate"
                                        x-text="`${selected.kode_bahan} • ${selected.spesifikasi || '-'}`"></span>
                                </template>
                            </div>
                            <svg class="w-4 h-4 text-slate-400 transition-transform flex-shrink-0"
                                :class="dropdownOpen ? 'rotate-180' : ''" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>

                        <!-- Search Dropdown Popover (Force Scroll Fixed) -->
                        <div x-show="dropdownOpen" @click.away="dropdownOpen = false"
                            x-transition:enter="transition ease-out duration-200"
                            x-transition:enter-start="opacity-0 translate-y-2"
                            x-transition:enter-end="opacity-100 translate-y-0"
                            class="absolute z-[100] left-0 right-0 mt-1 w-full bg-white rounded-xl border border-slate-300 shadow-2xl overflow-hidden">

                            <!-- Search Input -->
                            <div class="p-2 border-b border-slate-200 bg-slate-50">
                                <div class="relative">
                                    <input type="text" x-model.debounce.300ms="search" placeholder="Cari bahan..."
                                        class="w-full pl-11 pr-4 py-2 text-xs border border-slate-300 rounded-lg focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 outline-none"
                                        @keydown.enter.prevent>
                                    <div
                                        class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                        </svg>
                                    </div>
                                </div>
                            </div>

                            <!-- Items List (Hardcoded Scroll) -->
                            <div style="max-height: 220px; overflow-y: scroll !important; -webkit-overflow-scrolling: touch; overscroll-behavior: contain;"
                                @scroll="const { scrollTop, scrollHeight, clientHeight } = $el; if (scrollHeight - scrollTop <= clientHeight + 100) fetchData();">
                                <template x-for="item in items" :key="item.id">
                                    <button type="button" @click="selectItem(item)"
                                        class="w-full px-4 py-3.5 text-left hover:bg-blue-50/60 group transition-all flex flex-col gap-0.5 border-b border-slate-100 last:border-0 pointer-events-auto">
                                        <span
                                            class="text-sm font-bold text-slate-700 group-hover:text-blue-600 transition-colors"
                                            x-text="item.nama_bahan"></span>
                                        <div class="flex items-center gap-2">
                                            <span class="text-[9px] font-black text-slate-400 uppercase tracking-widest"
                                                x-text="item.kode_bahan"></span>
                                            <span
                                                class="text-[10px] font-bold text-slate-300 group-hover:text-slate-400 truncate"
                                                x-text="`• ${item.spesifikasi || '-'}`"></span>
                                        </div>
                                    </button>
                                </template>

                                <!-- Loading State -->
                                <div x-show="loading" class="p-10 flex flex-col items-center justify-center">
                                    <div
                                        class="w-6 h-6 border-2 border-blue-600 border-t-transparent rounded-full animate-spin">
                                    </div>
                                    <p class="text-[9px] font-bold text-blue-600 uppercase tracking-widest mt-3">Memuat
                                        Data...</p>
                                </div>

                                <!-- Empty State -->
                                <div x-show="!loading && items.length === 0" class="p-10 text-center">
                                    <div
                                        class="w-12 h-12 bg-slate-50 rounded-full flex items-center justify-center mx-auto mb-3">
                                        <svg class="w-6 h-6 text-slate-200" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                                        </svg>
                                    </div>
                                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Bahan tidak
                                        ditemukan</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label
                                class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1.5 ml-1">Jumlah
                                Masuk <span class="text-red-500">*</span></label>
                            <input type="number" name="jumlah" required min="1"
                                oninvalid="this.setCustomValidity('Jumlah masuk minimal 1')"
                                oninput="this.setCustomValidity('')" class="input-modern w-full text-sm"
                                placeholder="E.g. 50">
                        </div>
                        <div>
                            <label
                                class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1.5 ml-1">Tanggal
                                Terima <span class="text-red-500">*</span></label>
                            <input type="date" name="tanggal_masuk" value="{{ date('Y-m-d') }}" required
                                class="input-modern w-full text-sm">
                        </div>
                    </div>
                    <div>
                        <label
                            class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1.5 ml-1">Pemasok
                            / Vendor (Opsional)</label>
                        <input type="text" name="pemasok" class="input-modern w-full text-sm"
                            placeholder="E.g. PT. Kimia Farma">
                    </div>
                    <div>
                        <label
                            class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1.5 ml-1">Keterangan
                            Opsional</label>
                        <textarea name="keterangan" rows="2" class="input-modern w-full text-sm py-2 resize-none"
                            placeholder="Catatan terkait penerimaan barang..."></textarea>
                    </div>
                    <div class="flex justify-end gap-3 pt-4">
                        <button type="button" @click="open = false" :disabled="clicking"
                            class="btn-modern px-5 bg-slate-100 text-slate-600 hover:bg-slate-200">Batalkan</button>
                        <button type="submit" :disabled="clicking"
                            class="btn-modern px-8 bg-blue-600 text-white hover:bg-blue-700 shadow-lg shadow-blue-600/20 flex items-center gap-2">
                            <template x-if="clicking">
                                <div class="w-4 h-4 border-2 border-white/30 border-t-white rounded-full animate-spin">
                                </div>
                            </template>
                            <span x-text="clicking ? 'Menyimpan...' : 'Simpan Record'"></span>
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Script triggers for Toast (Inside turbo-frame if applicable, but we do it globally) -->
        <!-- Custom CSS for Dropdown & Quality Polish -->
        <style>
            .custom-scrollbar::-webkit-scrollbar {
                width: 5px;
            }

            .custom-scrollbar::-webkit-scrollbar-track {
                background: transparent;
            }

            .custom-scrollbar::-webkit-scrollbar-thumb {
                background: #E2E8F0;
                border-radius: 10px;
            }

            .custom-scrollbar::-webkit-scrollbar-thumb:hover {
                background: #CBD5E1;
            }
        </style>
    </turbo-frame>
@endsection
