<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pengajuan;
use App\Models\PengajuanItem;
use App\Models\Bahan;
use Illuminate\Http\Request;
use Inertia\Inertia;

class LaporanController extends Controller
{
    public function index(Request $request)
    {
        $query = Pengajuan::with(['user', 'items.bahan', 'modul', 'completer'])
            ->whereIn('status', ['completed', 'rejected', 'approved', 'pending_review']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('start_date')) {
            $query->whereDate('created_at', '>=', $request->start_date);
        }

        if ($request->filled('end_date')) {
            $query->whereDate('created_at', '<=', $request->end_date);
        }

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('kode_pengajuan', 'like', "%{$s}%")
                  ->orWhereHas('user', fn ($u) => $u->where('name', 'like', "%{$s}%"));
            });
        }

        $laporan = $query->latest()->paginate(20)->withQueryString();

        // Agregat untuk summary card
        $summary = [
            'total_completed' => Pengajuan::where('status', 'completed')->count(),
            'total_rejected'  => Pengajuan::where('status', 'rejected')->count(),
            'top_bahan'       => PengajuanItem::selectRaw('nama_bahan_snapshot, SUM(jumlah) as total')
                ->whereHas('pengajuan', fn ($q) => $q->where('status', 'completed'))
                ->groupBy('nama_bahan_snapshot')
                ->orderByDesc('total')
                ->take(5)
                ->get(),
        ];

        return Inertia::render('Admin/Laporan/Index', compact('laporan', 'summary'));
    }
}
