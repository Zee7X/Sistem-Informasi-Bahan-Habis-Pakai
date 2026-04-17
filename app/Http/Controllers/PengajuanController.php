<?php

namespace App\Http\Controllers;

use App\Models\Bahan;
use App\Models\PenggunaanBahan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PengajuanController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        
        if ($user->role === 'mahasiswa') {
            $pengajuan = PenggunaanBahan::where('requester_user_id', $user->id)
                ->with('bahan')
                ->latest()
                ->paginate(10);
        } else {
            $pengajuan = PenggunaanBahan::with(['bahan', 'requester'])
                ->latest()
                ->paginate(10);
        }

        return view('pengajuan.index', compact('pengajuan'));
    }

    public function create()
    {
        $bahan = Bahan::where('stok', '>', 0)->get();
        return view('pengajuan.create', compact('bahan'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'bahan_id' => 'required|exists:bahan,id',
            'jumlah' => 'required|integer|min:1',
            'tanggal_pemakaian' => 'required|date',
            'mata_kuliah' => 'required|string|max:200',
            'kelas' => 'required|string|max:100',
            'kelompok' => 'nullable|string|max:100',
            'keterangan' => 'nullable|string',
        ]);

        $bahan = Bahan::findOrFail($validated['bahan_id']);
        
        // Cek stok
        if ($bahan->stok < $validated['jumlah']) {
            return back()->with('error', 'Stok tidak mencukupi. Stok tersedia: ' . $bahan->stok);
        }

        $user = Auth::user();

        PenggunaanBahan::create([
            'tanggal_pemakaian' => $validated['tanggal_pemakaian'],
            'requester_user_id' => $user->id,
            'nama_pengisi' => $user->name,
            'nim_pengisi' => $user->nim,
            'bahan_id' => $validated['bahan_id'],
            'jumlah' => $validated['jumlah'],
            'satuan_id' => $bahan->satuan_id,
            'mata_kuliah' => $validated['mata_kuliah'],
            'kelas' => $validated['kelas'],
            'kelompok' => $validated['kelompok'],
            'keterangan' => $validated['keterangan'],
            'status' => 'pending',
        ]);

        return redirect()->route('pengajuan.index')->with('success', 'Pengajuan berhasil dikirim dan menunggu approval.');
    }

    public function approve(PenggunaanBahan $pengajuan)
    {
        if (Auth::user()->role !== 'admin') {
            abort(403);
        }

        if ($pengajuan->status !== 'pending') {
            return back()->with('error', 'Hanya pengajuan pending yang bisa di-approve.');
        }

        $bahan = $pengajuan->bahan;

        if ($bahan->stok < $pengajuan->jumlah) {
            return back()->with('error', 'Stok bahan tidak mencukupi untuk approval ini.');
        }

        // Jalankan transaksi stok
        $bahan->decrement('stok', $pengajuan->jumlah);
        
        $pengajuan->update([
            'status' => 'approved',
            'approved_by' => Auth::id(),
            'approved_at' => now(),
        ]);

        return back()->with('success', 'Pengajuan berhasil disetujui. Stok telah berkurang.');
    }

    public function reject(PenggunaanBahan $pengajuan)
    {
        if (Auth::user()->role !== 'admin') {
            abort(403);
        }

        $pengajuan->update([
            'status' => 'rejected',
            'approved_by' => Auth::id(),
            'approved_at' => now(),
        ]);

        return back()->with('success', 'Pengajuan telah ditolak.');
    }
}
