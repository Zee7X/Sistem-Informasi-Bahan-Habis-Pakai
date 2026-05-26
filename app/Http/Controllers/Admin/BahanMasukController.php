<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\BahanMasukService;
use App\Services\BahanService;
use App\Models\BahanMasuk;
use Exception;
use Inertia\Inertia;

class BahanMasukController extends Controller
{
    protected BahanMasukService $service;
    protected BahanService $bahanService;

    public function __construct(BahanMasukService $service, BahanService $bahanService)
    {
        $this->service = $service;
        $this->bahanService = $bahanService;
    }

    public function index(Request $request)
    {
        $filters  = $request->only(['search', 'start_date', 'end_date']);
        $bahanMasuk = $this->service->list($filters);
        $bahan    = $this->bahanService->all();

        return Inertia::render('Admin/BahanMasuk/Index', compact('bahanMasuk', 'bahan', 'filters'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'bahan_id'      => 'required|exists:bahan,id',
            'jumlah'        => 'required|integer|min:1',
            'tanggal_masuk' => 'required|date',
            'pemasok'       => 'nullable|string|max:200',
            'no_faktur'     => 'nullable|string|max:100',
            'harga_satuan'  => 'nullable|numeric|min:0',
            'keterangan'    => 'nullable|string|max:500',
        ]);

        try {
            $this->service->create($data, \Illuminate\Support\Facades\Auth::user());
            return redirect()->back()->with('success', 'Data stok masuk berhasil dicatat.');
        } catch (Exception $e) {
            return redirect()->back()->with('error', 'Gagal mencatat stok masuk: ' . $e->getMessage());
        }
    }

    public function destroy(BahanMasuk $masuk)
    {
        try {
            $this->service->delete($masuk);
            return redirect()->back()->with('success', 'Riwayat stok masuk berhasil dihapus.');
        } catch (Exception $e) {
            return redirect()->back()->with('error', 'Gagal menghapus riwayat: ' . $e->getMessage());
        }
    }
}
