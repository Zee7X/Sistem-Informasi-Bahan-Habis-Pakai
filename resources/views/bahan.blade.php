@extends('layouts.app')

@section('content')
    <div 
        x-data="{ search: @json(request('search', '')) }"
        x-init="
            $watch('search', value => {
                if (window.location.pathname !== '{{ route('bahan') }}') {
                    search = ''
                }
            })
        "
        x-effect="
            if (window.location.pathname !== '{{ route('bahan') }}') {
                search = ''
            }
        "
        class="overflow-x-auto p-4"
    >

    <div class="flex flex-col md:flex-row justify-between items-center mb-4 space-y-2 md:space-y-0">
        <p class="text-gray-800 font-medium text-lg">Master Bahan</p>

        <div class="flex items-center space-x-2">
            <form method="GET" action="{{ route('bahan') }}">
                <input 
                    type="text"
                    name="search"
                    value="{{ request('search') }}"
                    placeholder="Search..."
                    class="border border-gray-300 rounded-lg p-2 focus:ring-blue-400 focus:border-blue-400"
                >
            </form>

            <button 
                @click="$dispatch('open-create-modal')" 
                class="px-4 py-2 bg-blue-400 text-white font-semibold rounded-lg shadow hover:bg-blue-500 hover:shadow-md transition"
            >
                Tambah Bahan
            </button>
        </div>
    </div>

    <div class="shadow-md rounded-lg bg-white p-4 overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200 table-auto rounded-lg">
            <thead class="bg-blue-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">No</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Kode</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Nama Bahan</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Spesifikasi</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Stok</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Minimal Stok</th>
                    <th class="px-6 py-3 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">Aksi</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($bahan as $index => $b)
                <tr class="hover:bg-blue-50">
                    <td class="px-6 py-4 text-sm text-gray-700">{{ $bahan->firstItem() + $index }}</td>
                    <td class="px-6 py-4 text-sm text-gray-700">{{ $b->kode_bahan }}</td>
                    <td class="px-6 py-4 text-sm text-gray-700">{{ $b->nama_bahan }}</td>
                    <td class="px-6 py-4 text-sm text-gray-700">{{ $b->spesifikasi }}</td>
                    <td class="px-6 py-4 text-sm text-gray-700">{{ $b->stok }}</td>
                    <td class="px-6 py-4 text-sm text-gray-700">{{ $b->minimal_stok }}</td>
                    <td class="px-6 py-4 text-center">
                        <div class="flex justify-center space-x-2">
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
                                class="p-2 bg-blue-100 rounded-lg shadow hover:bg-blue-200 transition"
                            >
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.477 0 8.268 2.943 9.542 7-1.274 4.057-5.065 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                            </button>
                            <button 
                                @click="$dispatch('open-delete-modal', { id: @js($b->id), name: @js($b->nama_bahan) })" 
                                class="p-2 bg-red-100 rounded-lg shadow hover:bg-red-200 transition"
                            >
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5-4h4a2 2 0 012 2v1H8V5a2 2 0 012-2z" />
                                </svg>
                            </button>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="9" class="text-center py-4 text-gray-500">Tidak ada data bahan.</td>
                </tr>
                @endforelse
            </tbody>
        </table>

        @if ($bahan->lastPage() > 1)
        <div class="mt-4 flex justify-end">
            <nav class="inline-flex rounded-lg overflow-hidden shadow bg-white space-x-1">
                @if ($bahan->onFirstPage())
                    <span class="px-3 py-2 text-gray-400 bg-gray-100 cursor-not-allowed rounded">«</span>
                @else
                    <a href="{{ $bahan->previousPageUrl() }}&search={{ request('search') }}" class="px-3 py-2 bg-white text-gray-700 hover:bg-blue-100 rounded">«</a>
                @endif

                @foreach ($bahan->getUrlRange(1, $bahan->lastPage()) as $page => $url)
                    @if ($page == $bahan->currentPage())
                        <span class="px-3 py-2 bg-blue-600 text-white font-semibold rounded">{{ $page }}</span>
                    @else
                        <a href="{{ $url }}&search={{ request('search') }}" class="px-3 py-2 bg-white text-gray-700 hover:bg-blue-100 rounded">{{ $page }}</a>
                    @endif
                @endforeach

                @if ($bahan->hasMorePages())
                    <a href="{{ $bahan->nextPageUrl() }}&search={{ request('search') }}" class="px-3 py-2 bg-white text-gray-700 hover:bg-blue-100 rounded">»</a>
                @else
                    <span class="px-3 py-2 text-gray-400 bg-gray-100 cursor-not-allowed rounded">»</span>
                @endif
            </nav>
        </div>
        @endif
    </div>
</div>
    <div 
        x-data="{ open: false, deleteId: null, name: '' }"
        @open-delete-modal.window="open = true; deleteId = $event.detail.id; name = $event.detail.name"
        x-show="open" x-cloak
        class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50"
    >
        <div class="bg-white rounded-xl shadow-lg w-96 p-6">
            <h2 class="text-lg font-bold mb-4">Konfirmasi Hapus</h2>
            <p class="mb-4">Apakah kamu yakin ingin menghapus bahan <span class="font-semibold text-red-600" x-text="name"></span>?</p>
            <div class="flex justify-end space-x-2">
                <button @click="open = false" class="px-4 py-2 rounded-lg bg-gray-200 hover:bg-gray-300">Batal</button>
                <form :action="" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="px-4 py-2 rounded-lg bg-red-600 text-white hover:bg-red-700">Hapus</button>
                </form>
            </div>
        </div>
    </div>

    <div 
        x-data="{ open: false }"
        @open-create-modal.window="open = true"
        x-show="open" x-cloak
        class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50"
    >
        <div class="bg-white rounded-xl shadow-lg w-[90%] max-w-lg p-6">
            <h2 class="text-lg font-bold mb-4">Tambah Bahan</h2>
            <form action="{{ route('bahan.store') }}" method="POST" class="space-y-3">
                @csrf
                <div>
                    <label class="block text-sm font-medium text-gray-700">Kode Bahan</label>
                    <input type="text" name="kode_bahan" required class="mt-1 w-full border border-gray-300 rounded-lg p-2 focus:ring-blue-400 focus:border-blue-400">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Nama Bahan</label>
                    <input type="text" name="nama_bahan" required class="mt-1 w-full border border-gray-300 rounded-lg p-2 focus:ring-blue-400 focus:border-blue-400">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Spesifikasi</label>
                    <input name="spesifikasi" class="mt-1 w-full border border-gray-300 rounded-lg p-2 focus:ring-blue-400 focus:border-blue-400"></input>
                </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Minimal Stok</label>
                        <input type="number" name="minimal_stok" required class="mt-1 w-full border border-gray-300 rounded-lg p-2 focus:ring-blue-400 focus:border-blue-400">
                    </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Satuan</label>
                    <select name="satuan_id" required class="mt-1 w-full border border-gray-300 rounded-lg p-2">
                        <option value=""disabled selected>-- Pilih Satuan --</option>
                        @foreach($satuan as $s)
                            <option value="{{ $s->id }}">{{ $s->nama }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Lokasi</label>
                    <input type="text" name="lokasi" required class="mt-1 w-full border border-gray-300 rounded-lg p-2 focus:ring-blue-400 focus:border-blue-400">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Keterangan</label>
                    <input type="text" name="keterangan" class="mt-1 w-full border border-gray-300 rounded-lg p-2 focus:ring-blue-400 focus:border-blue-400">
                </div>

                <div class="flex justify-end space-x-2 mt-4">
                    <button type="button" @click="open = false" class="px-4 py-2 rounded-lg bg-gray-200 hover:bg-gray-300">Batal</button>
                    <button type="submit" class="px-4 py-2 rounded-lg bg-blue-500 text-white hover:bg-blue-600">Simpan</button>
                </div>
            </form>
        </div>
    </div>

    <div 
    x-data="{ open: false, form: {} }"
    @open-edit-modal.window="open = true; form = $event.detail"
    x-show="open" x-cloak
    class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50"
>
    <div class="bg-white rounded-xl shadow-lg w-[90%] max-w-lg p-6">
        <h2 class="text-lg font-bold mb-4">Edit Bahan</h2>
        <form :action="`/bahan/${form.id}`" method="POST" class="space-y-3">
            @csrf
            @method('PUT')
            <div>
                <label class="block text-sm font-medium text-gray-700">Kode Bahan</label>
                <input type="text" name="kode_bahan" x-model="form.kode_bahan" required class="mt-1 w-full border border-gray-300 rounded-lg p-2">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">Nama Bahan</label>
                <input type="text" name="nama_bahan" x-model="form.nama_bahan" required class="mt-1 w-full border border-gray-300 rounded-lg p-2">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">Spesifikasi</label>
                <input name="spesifikasi" x-model="form.spesifikasi" class="mt-1 w-full border border-gray-300 rounded-lg p-2"></input>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">Minimal Stok</label>
                <input name="minimal_stok" type="number" x-model="form.minimal_stok" class="mt-1 w-full border border-gray-300 rounded-lg p-2"></input>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">Satuan</label>
                <select name="satuan_id" x-model="form.satuan_id" class="mt-1 w-full border border-gray-300 rounded-lg p-2">
                    <option value=""disabled selected>-- Pilih Satuan --</option>
                    @foreach($satuan as $s)
                        <option value="{{ $s->id }}">{{ $s->nama }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">Lokasi</label>
                <input type="text" name="lokasi" x-model="form.lokasi" class="mt-1 w-full border border-gray-300 rounded-lg p-2">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">Keterangan</label>
                <input type="text" name="keterangan" x-model="form.keterangan" class="mt-1 w-full border border-gray-300 rounded-lg p-2">
            </div>

            <div class="flex justify-end space-x-2 mt-4">
                <button type="button" @click="open = false" class="px-4 py-2 rounded-lg bg-gray-200 hover:bg-gray-300">Batal</button>
                <button type="submit" class="px-4 py-2 rounded-lg bg-blue-500 text-white hover:bg-blue-600">Update</button>
            </div>
        </form>
    </div>
</div>
@endsection
