@extends('layouts.app')

@section('title', 'Daftar Pengajuan')

@section('header')
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h2 class="text-xl font-black text-slate-800 tracking-tight uppercase">Pengajuan Bahan</h2>
            <p class="text-[10px] text-slate-400 font-bold uppercase tracking-widest mt-1">Kelola dan pantau penggunaan bahan praktikum</p>
        </div>
        @if(auth()->user()->role === 'mahasiswa')
        <div class="flex items-center gap-3">
            <a href="{{ route('pengajuan.create') }}" 
               class="btn-modern bg-blue-600 text-white hover:bg-blue-700 shadow-lg shadow-blue-600/20 flex items-center gap-2 text-xs">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Buat Pengajuan Baru
            </a>
        </div>
        @endif
    </div>
@endsection

@section('content')
    <div class="card-modern overflow-hidden bg-white/80 backdrop-blur-xl border-slate-200/60 shadow-xl shadow-slate-200/40">
        <div class="p-5 border-b border-slate-100 flex flex-col md:flex-row justify-between items-center gap-4 bg-slate-50/50">
            <h3 class="text-[11px] font-black text-slate-700 uppercase tracking-widest">
                @if(auth()->user()->role === 'mahasiswa')
                    Riwayat Pengajuan Saya
                @else
                    Antrian Pengajuan Semua User
                @endif
            </h3>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50/80">
                        <th class="px-6 py-4 text-[10px] font-bold text-slate-500 uppercase tracking-widest">No</th>
                        <th class="px-6 py-4 text-[10px] font-bold text-slate-500 uppercase tracking-widest">Identitas Pengirim</th>
                        <th class="px-6 py-4 text-[10px] font-bold text-slate-500 uppercase tracking-widest">Bahan & Qty</th>
                        <th class="px-6 py-4 text-[10px] font-bold text-slate-500 uppercase tracking-widest">Peruntukan</th>
                        <th class="px-6 py-4 text-[10px] font-bold text-slate-500 uppercase tracking-widest text-center">Status</th>
                        <th class="px-6 py-4 text-[10px] font-bold text-slate-500 uppercase tracking-widest text-right">Aksi Control</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($pengajuan as $index => $p)
                    <tr class="hover:bg-blue-50/30 transition-colors group">
                        <td class="px-6 py-4 text-xs font-bold text-slate-400">
                            {{ str_pad($pengajuan->firstItem() + $index, 2, '0', STR_PAD_LEFT) }}
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex flex-col">
                                <span class="text-xs font-bold text-slate-700">{{ $p->nama_pengisi }}</span>
                                <span class="text-[9px] font-bold text-slate-400 uppercase tracking-wider mt-0.5">{{ $p->nim_pengisi ?? 'N/A' }} • {{ $p->kelas }}</span>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex flex-col">
                                <span class="text-xs font-bold text-slate-700">{{ $p->bahan->nama_bahan ?? $p->nama_bahan_text }}</span>
                                <span class="text-[10px] font-black text-blue-600 uppercase mt-0.5">{{ $p->jumlah }} {{ $p->satuan->nama ?? 'Unit' }}</span>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex flex-col">
                                <span class="text-[10px] font-bold text-slate-600">{{ $p->mata_kuliah }}</span>
                                <span class="text-[9px] font-medium text-slate-400 italic">Kelompok: {{ $p->kelompok ?? '-' }}</span>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-center">
                            @if($p->status === 'pending')
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-amber-500/10 text-amber-600 text-[9px] font-black uppercase tracking-wider border border-amber-500/20">
                                    <span class="w-1.5 h-1.5 rounded-full bg-amber-500 animate-pulse"></span>
                                    Pending Review
                                </span>
                            @elseif($p->status === 'approved')
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-emerald-500/10 text-emerald-600 text-[9px] font-black uppercase tracking-wider border border-emerald-500/20">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                    Approved
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-rose-500/10 text-rose-600 text-[9px] font-black uppercase tracking-wider border border-rose-500/20">
                                    <span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span>
                                    Rejected
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex justify-end gap-2">
                                @if(auth()->user()->role === 'admin' && $p->status === 'pending')
                                    <form action="{{ route('admin.pengajuan.approve', $p) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="p-2 bg-emerald-50 text-emerald-600 hover:bg-emerald-600 hover:text-white rounded-xl border border-emerald-100 transition-all shadow-sm" title="Approve">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                        </button>
                                    </form>
                                    <form action="{{ route('admin.pengajuan.reject', $p) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="p-2 bg-rose-50 text-rose-600 hover:bg-rose-600 hover:text-white rounded-xl border border-rose-100 transition-all shadow-sm" title="Reject">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                        </button>
                                    </form>
                                @else
                                    <button class="p-2 bg-slate-50 text-slate-300 rounded-xl border border-slate-100 cursor-not-allowed">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    </button>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center text-slate-400">Belum ada data pengajuan bahan.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <!-- Clinical Pagination -->
        <div class="p-5 border-t border-slate-50 bg-slate-50/30">
            <div class="flex flex-col md:flex-row items-center justify-between gap-4">
                <div class="flex items-center gap-2">
                    <span class="w-1.5 h-1.5 rounded-full bg-blue-500"></span>
                    <p class="text-[11px] font-bold text-slate-500 uppercase tracking-wider">
                        Menampilkan {{ $pengajuan->firstItem() ?? 0 }} - {{ $pengajuan->lastItem() ?? 0 }} dari {{ $pengajuan->total() }} data
                    </p>
                </div>
                
                @if ($pengajuan->lastPage() > 1)
                <div class="flex items-center gap-1 bg-white p-1 rounded-xl border border-slate-200/60 shadow-sm">
                    {{-- First Page --}}
                    @if (!$pengajuan->onFirstPage())
                        <a href="{{ $pengajuan->url(1) }}" class="w-8 h-8 flex items-center justify-center rounded-lg text-slate-400 hover:text-blue-600 hover:bg-blue-50 transition-all border border-transparent hover:border-blue-100" title="First Page">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 19l-7-7 7-7m8 14l-7-7 7-7"/></svg>
                        </a>
                        <a href="{{ $pengajuan->previousPageUrl() }}" class="w-8 h-8 flex items-center justify-center rounded-lg text-slate-400 hover:text-blue-600 hover:bg-blue-50 transition-all border border-transparent hover:border-blue-100" title="Previous">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                        </a>
                    @endif
                    
                    @foreach ($pengajuan->getUrlRange(max(1, $pengajuan->currentPage() - 1), min($pengajuan->lastPage(), $pengajuan->currentPage() + 1)) as $page => $url)
                        <a href="{{ $url }}" 
                           class="w-8 h-8 flex items-center justify-center rounded-lg text-xs font-bold transition-all
                           {{ $page == $pengajuan->currentPage() ? 'bg-blue-600 text-white shadow-md shadow-blue-200' : 'text-slate-500 hover:bg-slate-50 hover:text-blue-600' }}">
                            {{ $page }}
                        </a>
                    @endforeach

                    {{-- Next & Last Page --}}
                    @if ($pengajuan->hasMorePages())
                        <a href="{{ $pengajuan->nextPageUrl() }}" class="w-8 h-8 flex items-center justify-center rounded-lg text-slate-400 hover:text-blue-600 hover:bg-blue-50 transition-all border border-transparent hover:border-blue-100" title="Next">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                        </a>
                        <a href="{{ $pengajuan->url($pengajuan->lastPage()) }}" class="w-8 h-8 flex items-center justify-center rounded-lg text-slate-400 hover:text-blue-600 hover:bg-blue-50 transition-all border border-transparent hover:border-blue-100" title="Last Page">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 5l7 7-7 7M5 5l7 7-7 7"/></svg>
                        </a>
                    @endif
                </div>
                @endif
            </div>
        </div>
    </div>
@endsection
