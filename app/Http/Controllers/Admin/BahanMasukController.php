<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\BahanMasukService;
use App\Services\BahanService;
use App\Models\BahanMasuk;
use Exception;

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
        $filters = $request->only(['search', 'start_date', 'end_date']);
        $masuk = $this->service->list($filters);
        
        // For the creation modal select
        $bahan = $this->bahanService->all();
        
        return view('admin.bahan_masuk', compact('masuk', 'bahan'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'bahan_id' => 'required|exists:bahan,id',
            'jumlah' => 'required|integer|min:1',
            'tanggal_masuk' => 'required|date',
            'pemasok' => 'nullable|string|max:200',
            'keterangan' => 'nullable|string'
        ]);

        try {
            $this->service->create($data);
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
