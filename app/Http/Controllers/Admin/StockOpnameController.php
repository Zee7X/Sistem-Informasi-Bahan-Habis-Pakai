<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Bahan;
use App\Models\StockOpname;
use App\Services\StockOpnameService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class StockOpnameController extends Controller
{
    public function __construct(
        private readonly StockOpnameService $service
    ) {}

    public function index(Request $request): Response
    {
        $stockOpname = StockOpname::with(['bahan.satuan', 'createdBy'])
            ->when($request->search, function ($q) use ($request) {
                $q->whereHas('bahan', fn ($q2) =>
                    $q2->where('nama_bahan', 'like', "%{$request->search}%")
                       ->orWhere('kode_bahan', 'like', "%{$request->search}%")
                );
            })
            ->when($request->bahan_id, fn ($q) => $q->where('bahan_id', $request->bahan_id))
            ->when($request->jenis, fn ($q) => $q->where('jenis_penyesuaian', $request->jenis))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        $bahan   = Bahan::orderBy('nama_bahan')->get(['id', 'nama_bahan', 'kode_bahan', 'stok']);
        $filters = $request->only('search', 'bahan_id', 'jenis');
        return Inertia::render('Admin/StockOpname/Index', compact('stockOpname', 'bahan', 'filters'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'bahan_id'          => 'required|exists:bahan,id',
            'stok_sesuai'       => 'required|integer|min:0',
            'alasan'            => 'required|string|min:10|max:500',
            'jenis_penyesuaian' => 'required|in:rusak,kadaluarsa,hilang,koreksi_lain',
        ], [
            'alasan.required' => 'Alasan penyesuaian wajib diisi.',
            'alasan.min'      => 'Alasan minimal 10 karakter.',
        ]);

        try {
            $this->service->adjust($validated, Auth::user());
            return back()->with('success', 'Penyesuaian stok berhasil dicatat.');
        } catch (\RuntimeException $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}
