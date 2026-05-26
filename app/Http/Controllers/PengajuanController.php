<?php

namespace App\Http\Controllers;

use App\Models\Bahan;
use App\Models\ModulPraktikum;
use App\Models\Pengajuan;
use App\Services\PengajuanService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class PengajuanController extends Controller
{
    public function __construct(
        private readonly PengajuanService $service
    ) {}

    // ──────────────────────────────────────────────────────────────────────────
    // ADMIN: Lihat semua pengajuan
    // ──────────────────────────────────────────────────────────────────────────

    public function index(Request $request): Response
    {
        $query = Pengajuan::with(['user', 'modul', 'items'])
            ->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('kode_pengajuan', 'like', "%{$search}%")
                  ->orWhereHas('user', fn ($u) => $u->where('name', 'like', "%{$search}%"));
            });
        }

        $pengajuan = $query->paginate(15)->withQueryString();
        $filters   = $request->only('status', 'search');

        return Inertia::render('Admin/Pengajuan/Index', compact('pengajuan', 'filters'));
    }

    public function show(Pengajuan $pengajuan): Response
    {
        $pengajuan->load(['user', 'modul', 'items.bahan.satuan', 'approver', 'completer']);
        return Inertia::render('Admin/Pengajuan/Show', compact('pengajuan'));
    }

    // ──────────────────────────────────────────────────────────────────────────
    // MAHASISWA: Pengajuan milik sendiri
    // ──────────────────────────────────────────────────────────────────────────

    public function myIndex(Request $request): Response
    {
        $pengajuan = Pengajuan::with(['items', 'modul'])
            ->where('user_id', Auth::id())
            ->when($request->search, function ($q) use ($request) {
                $q->where(function ($q2) use ($request) {
                    $q2->where('kode_pengajuan', 'like', "%{$request->search}%")
                       ->orWhere('mata_kuliah', 'like', "%{$request->search}%")
                       ->orWhere('kelompok', 'like', "%{$request->search}%");
                });
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        $filters = $request->only('search');
        return Inertia::render('Mahasiswa/Pengajuan/Index', compact('pengajuan', 'filters'));
    }

    public function create(): Response
    {
        $moduls = ModulPraktikum::active()->with('items.bahan.satuan')->orderBy('nama_modul')->get();
        $bahan  = Bahan::where('stok', '>', 0)->with('satuan')->orderBy('nama_bahan')->get();

        return Inertia::render('Mahasiswa/Pengajuan/Create', compact('moduls', 'bahan'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'jenis'           => 'required|in:modul,mandiri',
            'modul_id'        => 'nullable|exists:modul_praktikum,id|required_if:jenis,modul',
            'mata_kuliah'     => 'nullable|string|max:200',
            'kelas'           => 'nullable|string|max:100',
            'kelompok'        => 'nullable|string|max:100',
            'jumlah_anggota'  => 'nullable|integer|min:1|max:20',
            'tanggal_pakai'   => 'required|date|after_or_equal:today',
            'keterangan'      => 'nullable|string|max:1000',
            'items'           => 'required|array|min:1',
            'items.*.bahan_id'=> 'required|exists:bahan,id',
            'items.*.jumlah'  => 'required|numeric|min:0.01',
        ]);

        try {
            $this->service->store($validated, Auth::user());
            return redirect()->route('mahasiswa.pengajuan.index')
                ->with('success', 'Pengajuan berhasil dikirim dan menunggu review laboran.');
        } catch (\RuntimeException $e) {
            return back()->with('error', $e->getMessage())->withInput();
        }
    }

    public function showMahasiswa(Pengajuan $pengajuan): Response
    {
        // Pastikan mahasiswa hanya bisa lihat pengajuannya sendiri
        abort_unless($pengajuan->user_id === Auth::id(), 403);
        $pengajuan->load(['items.bahan.satuan', 'modul', 'approver', 'completer']);
        return Inertia::render('Mahasiswa/Pengajuan/Show', compact('pengajuan'));
    }

    // ──────────────────────────────────────────────────────────────────────────
    // ADMIN: Aksi state machine
    // ──────────────────────────────────────────────────────────────────────────

    public function approve(Pengajuan $pengajuan): RedirectResponse
    {
        try {
            $this->service->approve($pengajuan, Auth::user());
            return back()->with('success', "Pengajuan {$pengajuan->kode_pengajuan} berhasil disetujui.");
        } catch (\RuntimeException $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function reject(Request $request, Pengajuan $pengajuan): RedirectResponse
    {
        $request->validate([
            'reject_reason' => 'required|string|min:10|max:500',
        ], [
            'reject_reason.required' => 'Alasan penolakan wajib diisi.',
            'reject_reason.min'      => 'Alasan penolakan minimal 10 karakter.',
        ]);

        try {
            $this->service->reject($pengajuan, Auth::user(), $request->reject_reason);
            return back()->with('success', "Pengajuan {$pengajuan->kode_pengajuan} telah ditolak.");
        } catch (\RuntimeException $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * CRITICAL: Stok dipotong di sini, bukan saat approve.
     */
    public function complete(Pengajuan $pengajuan): RedirectResponse
    {
        try {
            $this->service->complete($pengajuan, Auth::user());
            return back()->with('success', "Pengajuan {$pengajuan->kode_pengajuan} selesai. Stok telah diperbarui.");
        } catch (\RuntimeException $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}
