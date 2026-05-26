<?php

namespace App\Http\Controllers\KetuaJurusan;

use App\Http\Controllers\Controller;
use App\Models\BahanMasuk;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class BahanMasukController extends Controller
{
    public function index(Request $request): Response
    {
        $masuk = BahanMasuk::with(['bahan.satuan', 'createdBy', 'approvedByKjur'])
            ->when($request->status, fn ($q) => $q->where('status_kjur', $request->status))
            ->when($request->search, function ($q) use ($request) {
                $q->where(function ($q2) use ($request) {
                    $q2->whereHas('bahan', fn ($q3) =>
                        $q3->where('nama_bahan', 'like', "%{$request->search}%")
                           ->orWhere('kode_bahan', 'like', "%{$request->search}%")
                    )->orWhere('pemasok', 'like', "%{$request->search}%")
                     ->orWhere('no_faktur', 'like', "%{$request->search}%");
                });
            })
            ->latest()
            ->paginate(15)
            ->withQueryString();

        $filters = $request->only('status', 'search');
        return Inertia::render('KetuaJurusan/BahanMasuk/Index', compact('masuk', 'filters'));
    }

    /** Ketua Jurusan menyetujui pembelian stok. */
    public function approve(BahanMasuk $masuk): RedirectResponse
    {
        if ($masuk->status_kjur !== 'pending') {
            return back()->with('error', 'Hanya pembelian berstatus pending yang dapat disetujui.');
        }

        $masuk->update([
            'status_kjur'      => 'approved',
            'approved_by_kjur' => Auth::id(),
        ]);

        return back()->with('success', 'Pembelian stok berhasil disetujui.');
    }
}
