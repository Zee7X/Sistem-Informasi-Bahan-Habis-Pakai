<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Bahan;
use App\Models\ModulPraktikum;
use App\Models\ModulPraktikumItem;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class ModulPraktikumController extends Controller
{
    public function index(Request $request): Response
    {
        $moduls = ModulPraktikum::with(['items.bahan', 'createdBy'])
            ->when($request->search, fn ($q, $s) =>
                $q->where('nama_modul', 'like', "%{$s}%")
                  ->orWhere('kode_modul', 'like', "%{$s}%")
            )
            ->latest()
            ->paginate(12)
            ->withQueryString();

        $bahan   = Bahan::with('satuan')->orderBy('nama_bahan')->get();
        $filters = $request->only('search');
        return Inertia::render('Admin/ModulPraktikum/Index', compact('moduls', 'bahan', 'filters'));
    }

    public function create(): Response
    {
        $bahan = Bahan::with('satuan')->orderBy('nama_bahan')->get();
        return Inertia::render('Admin/ModulPraktikum/Create', compact('bahan'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'kode_modul'       => 'required|string|max:50|unique:modul_praktikum,kode_modul',
            'nama_modul'       => 'required|string|max:200',
            'deskripsi'        => 'nullable|string|max:1000',
            'is_active'        => 'boolean',
            'items'            => 'required|array|min:1',
            'items.*.bahan_id' => 'required|exists:bahan,id|distinct',
            'items.*.jumlah'   => 'required|numeric|min:0.01',
        ]);

        $modul = ModulPraktikum::create([
            'kode_modul'  => strtoupper($validated['kode_modul']),
            'nama_modul'  => $validated['nama_modul'],
            'deskripsi'   => $validated['deskripsi'] ?? null,
            'is_active'   => $validated['is_active'] ?? true,
            'created_by'  => Auth::id(),
        ]);

        foreach ($validated['items'] as $item) {
            ModulPraktikumItem::create([
                'modul_id' => $modul->id,
                'bahan_id' => $item['bahan_id'],
                'jumlah'   => $item['jumlah'],
            ]);
        }

        return redirect()->route('admin.modul-praktikum.index')
            ->with('success', "Modul '{$modul->nama_modul}' berhasil dibuat.");
    }

    public function edit(ModulPraktikum $modulPraktikum): Response
    {
        $modulPraktikum->load('items.bahan.satuan');
        $bahan = Bahan::with('satuan')->orderBy('nama_bahan')->get();
        return Inertia::render('Admin/ModulPraktikum/Edit', compact('modulPraktikum', 'bahan'));
    }

    public function update(Request $request, ModulPraktikum $modulPraktikum): RedirectResponse
    {
        $validated = $request->validate([
            'kode_modul' => 'required|string|max:50|unique:modul_praktikum,kode_modul,' . $modulPraktikum->id,
            'nama_modul' => 'required|string|max:200',
            'deskripsi'  => 'nullable|string|max:1000',
            'is_active'  => 'boolean',
        ]);

        $modulPraktikum->update([
            'kode_modul' => strtoupper($validated['kode_modul']),
            'nama_modul' => $validated['nama_modul'],
            'deskripsi'  => $validated['deskripsi'] ?? null,
            'is_active'  => $request->boolean('is_active'),
        ]);

        return redirect()->route('admin.modul-praktikum.index')
            ->with('success', "Modul '{$modulPraktikum->nama_modul}' berhasil diperbarui.");
    }

    public function destroy(ModulPraktikum $modulPraktikum): RedirectResponse
    {
        // Cek apakah modul pernah digunakan dalam pengajuan
        if ($modulPraktikum->pengajuanList()->exists()) {
            return back()->with('error', 'Modul tidak dapat dihapus karena sudah digunakan dalam pengajuan.');
        }

        $modulPraktikum->delete();
        return redirect()->route('admin.modul-praktikum.index')
            ->with('success', 'Modul berhasil dihapus.');
    }

    // ──────────────────────────────────────────────────────────────────────────
    // Items management
    // ──────────────────────────────────────────────────────────────────────────

    public function storeItem(Request $request, ModulPraktikum $modulPraktikum): RedirectResponse
    {
        $validated = $request->validate([
            'bahan_id' => 'required|exists:bahan,id',
            'jumlah'   => 'required|numeric|min:0.01',
        ]);

        // Cegah duplikasi bahan dalam modul yang sama
        $exists = $modulPraktikum->items()->where('bahan_id', $validated['bahan_id'])->exists();
        if ($exists) {
            return back()->with('error', 'Bahan sudah ada dalam modul ini.');
        }

        ModulPraktikumItem::create([
            'modul_id' => $modulPraktikum->id,
            'bahan_id' => $validated['bahan_id'],
            'jumlah'   => $validated['jumlah'],
        ]);

        return back()->with('success', 'Bahan berhasil ditambahkan ke modul.');
    }

    public function destroyItem(ModulPraktikum $modulPraktikum, ModulPraktikumItem $item): RedirectResponse
    {
        // Pastikan item memang milik modul ini
        abort_unless($item->modul_id === $modulPraktikum->id, 403);

        $item->delete();
        return back()->with('success', 'Bahan dihapus dari modul.');
    }
}
