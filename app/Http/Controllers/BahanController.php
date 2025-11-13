<?php

namespace App\Http\Controllers;

use App\Http\Requests\BahanRequest;
use App\Models\Bahan;
use App\Services\BahanService;
use Illuminate\Http\Request;
use App\Services\SatuanService;

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
        $search = $request->query('search');
        $bahan = $this->service->list($search);
        $satuan = $this->satuanService->all();
        return view('bahan', compact('bahan', 'satuan'));
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
