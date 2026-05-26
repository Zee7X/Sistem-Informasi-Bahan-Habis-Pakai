<?php

namespace App\Http\Controllers;

use App\Http\Requests\BahanRequest;
use App\Models\Bahan;
use App\Services\BahanService;
use Illuminate\Http\Request;
use App\Services\SatuanService;
use Inertia\Inertia;

class BahanController extends Controller
{
    protected BahanService $service;
    protected SatuanService $satuanService;

    public function __construct(BahanService $service, SatuanService $satuanService)
    {
        $this->service = $service;
        $this->satuanService = $satuanService;
    }

    public function index(Request $request)
    {
        $filters = $request->only('search', 'sort');
        $bahan   = $this->service->list($filters['search'] ?? null, $filters['sort'] ?? 'latest');
        $satuan  = $this->satuanService->all();

        $stats = [
            'total'       => Bahan::count(),
            'stok_aman'   => Bahan::whereColumn('stok', '>=', 'minimal_stok')->count(),
            'perlu_restock' => Bahan::whereColumn('stok', '<', 'minimal_stok')->count(),
        ];

        return Inertia::render('Admin/Bahan/Index', compact('bahan', 'satuan', 'filters', 'stats'));
    }

    public function store(BahanRequest $request)
    {
        $this->service->create($request->validated());
        return redirect()->back()->with('success', 'Bahan berhasil ditambahkan.');
    }

    public function update(BahanRequest $request, Bahan $bahan)
    {
        $this->service->update($bahan, $request->validated());
        return redirect()->back()->with('success', 'Bahan berhasil diperbarui.');
    }

    public function destroy(Bahan $bahan)
    {
        try {
            $this->service->delete($bahan);
            return redirect()->back()->with('success', 'Bahan berhasil dihapus.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }
}
