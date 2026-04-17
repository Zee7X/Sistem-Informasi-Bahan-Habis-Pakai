@extends('layouts.app')

@section('title', 'Buat Pengajuan')

@section('header')
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h2 class="text-xl font-black text-slate-800 tracking-tight uppercase">Form Pengajuan Bahan</h2>
            <p class="text-[10px] text-slate-400 font-bold uppercase tracking-widest mt-1">Silakan isi detail bahan yang dibutuhkan untuk praktikum</p>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('pengajuan.index') }}" 
               class="btn-modern bg-slate-100 text-slate-600 hover:bg-slate-200 flex items-center gap-2 text-xs font-bold">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                Kembali ke Daftar
            </a>
        </div>
    </div>
@endsection

@section('content')
    <div class="max-w-2xl mx-auto">
        <div class="card-modern bg-white overflow-hidden shadow-2xl shadow-slate-200/50">
            <div class="p-6 border-b border-slate-100 bg-slate-50/50">
                <h3 class="text-[11px] font-black text-slate-700 uppercase tracking-[0.2em]">Detail Pengajuan</h3>
            </div>
            
            <form action="{{ route('pengajuan.store') }}" method="POST" class="p-8 space-y-6">
                @csrf
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="md:col-span-2">
                        <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-2 ml-1">Pilih Bahan Praktikum</label>
                        <select name="bahan_id" required class="input-modern w-full text-sm appearance-none bg-no-repeat bg-[right_1rem_center]" style="background-image: url('data:image/svg+xml;charset=US-ASCII,%3Csvg%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20width%3D%22292.4%22%20height%3D%22292.4%22%3E%3Cpath%20fill%3D%22%23CBD5E1%22%20d%3D%22M287%2069.4a17.6%2017.6%200%200%200-13-5.4H18.4c-5%200-9.3%201.8-12.9%205.4A17.6%2017.6%200%200%200%200%2082.2c0%205%201.8%209.3%205.4%2012.9l128%20127.9c3.6%203.6%207.8%205.4%2012.8%205.4s9.2-1.8%2012.8-5.4L287%2095c3.5-3.5%205.4-7.8%205.4-12.8%200-5-1.9-9.2-5.5-12.8z%22%2F%3E%3C%2Fsvg%3E'); background-size: .65em auto;">
                            <option value="">-- Cari & Pilih Bahan --</option>
                            @foreach($bahan as $b)
                                <option value="{{ $b->id }}">{{ $b->nama_bahan }} (Stok: {{ $b->stok }} {{ $b->satuan->nama ?? '' }})</option>
                            @endforeach
                        </select>
                        @error('bahan_id') <p class="text-red-500 text-[10px] mt-1 font-bold">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-2 ml-1">Jumlah Pemakaian</label>
                        <input type="number" name="jumlah" required min="1" placeholder="0" class="input-modern w-full text-sm">
                        @error('jumlah') <p class="text-red-500 text-[10px] mt-1 font-bold">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-2 ml-1">Tanggal Praktikum</label>
                        <input type="date" name="tanggal_pemakaian" required class="input-modern w-full text-sm" value="{{ date('Y-m-d') }}">
                        @error('tanggal_pemakaian') <p class="text-red-500 text-[10px] mt-1 font-bold">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div class="space-y-6 pt-4 border-t border-slate-50">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-2 ml-1">Mata Kuliah</label>
                            <input type="text" name="mata_kuliah" required placeholder="E.g. Praktikum Kimia Dasar" class="input-modern w-full text-sm">
                        </div>
                        <div>
                            <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-2 ml-1">Kelas</label>
                            <input type="text" name="kelas" required placeholder="E.g. TI-2022" class="input-modern w-full text-sm" value="{{ auth()->user()->kelas }}">
                        </div>
                    </div>

                    <div>
                        <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-2 ml-1">Kelompok (Opsional)</label>
                        <input type="text" name="kelompok" placeholder="E.g. Kelompok 5" class="input-modern w-full text-sm">
                    </div>

                    <div>
                        <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-2 ml-1">Keterangan Tambahan</label>
                        <textarea name="keterangan" rows="3" placeholder="Tuliskan tujuan pemakaian atau rincian lainnya..." class="input-modern w-full text-sm py-3 resize-none"></textarea>
                    </div>
                </div>

                <div class="pt-6">
                    <button type="submit" class="w-full btn-modern bg-blue-600 text-white hover:bg-blue-700 shadow-xl shadow-blue-600/20 py-4 text-xs font-black uppercase tracking-widest">
                        Kirim Pengajuan Sekarang
                    </button>
                    <p class="text-center text-[9px] text-slate-400 mt-4 font-bold uppercase tracking-wider">
                        Pengajuan akan diperiksa dan divalidasi oleh Laboran sebelum stok dikurangi
                    </p>
                </div>
            </form>
        </div>
    </div>
@endsection
