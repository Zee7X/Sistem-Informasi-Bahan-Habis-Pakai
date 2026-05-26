<?php

namespace App\Http\Controllers;

use App\Models\Bahan;
use App\Models\BahanMasuk;
use App\Models\Pengajuan;
use App\Models\PengajuanItem;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function index(Request $request): Response
    {
        $user = $request->user();

        return match ($user->role) {
            'admin'         => $this->adminDashboard($user),
            'mahasiswa'     => $this->mahasiswaDashboard($user),
            'ketua_jurusan' => $this->kjurDashboard($user),
            default         => abort(403),
        };
    }

    // ──────────────────────────────────────────────────────────────────────────

    private function adminDashboard(User $user): Response
    {
        $stats = [
            'total_bahan'    => Bahan::count(),
            'total_user'     => User::where('role', 'mahasiswa')->count(),
            'pending_review' => Pengajuan::where('status', 'pending_review')->count(),
            'stok_kritis'    => Bahan::whereColumn('stok', '<=', 'minimal_stok')->count(),
        ];

        $stokKritis = Bahan::with('satuan')
            ->whereColumn('stok', '<=', 'minimal_stok')
            ->orderBy('stok')
            ->take(8)
            ->get();

        $recentPengajuan = Pengajuan::with(['user', 'items'])
            ->where('status', 'pending_review')
            ->latest()
            ->take(5)
            ->get();

        $chartData = $this->buildChartData();

        return Inertia::render('Dashboard', compact('stats', 'stokKritis', 'recentPengajuan', 'chartData'));
    }

    private function mahasiswaDashboard(User $user): Response
    {
        $stats = [
            'total_pengajuan' => Pengajuan::where('user_id', $user->id)->count(),
            'pending_review'  => Pengajuan::where('user_id', $user->id)->where('status', 'pending_review')->count(),
            'approved'        => Pengajuan::where('user_id', $user->id)->where('status', 'approved')->count(),
            'completed'       => Pengajuan::where('user_id', $user->id)->where('status', 'completed')->count(),
        ];

        $recentPengajuan = Pengajuan::with(['items', 'modul'])
            ->where('user_id', $user->id)
            ->latest()
            ->take(5)
            ->get();

        $chartData = $this->buildChartData();

        return Inertia::render('Dashboard', compact('stats', 'recentPengajuan', 'chartData'));
    }

    private function kjurDashboard(User $user): Response
    {
        $stats = [
            'total_transaksi' => Pengajuan::whereIn('status', ['approved', 'completed'])->count(),
            'total_bahan'     => Bahan::count(),
            'stok_kritis'     => Bahan::whereColumn('stok', '<=', 'minimal_stok')->count(),
            'pending_belanja' => BahanMasuk::where('status_kjur', 'pending')->count(),
        ];

        $chartData = $this->buildChartData();

        $topBahan = PengajuanItem::with('bahan')
            ->whereHas('pengajuan', fn ($q) => $q->where('status', 'completed')
                ->where('completed_at', '>=', now()->subMonths(6)))
            ->selectRaw('bahan_id, SUM(jumlah) as total_pakai')
            ->groupBy('bahan_id')
            ->orderByDesc('total_pakai')
            ->take(5)
            ->get();

        return Inertia::render('Dashboard', compact('stats', 'chartData', 'topBahan'));
    }

    // ──────────────────────────────────────────────────────────────────────────

    private function buildChartData(): array
    {
        $labels = collect();
        $values = collect();

        for ($i = 5; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $labels->push($date->format('M'));
            $values->push(
                Pengajuan::where('status', 'completed')
                    ->whereMonth('completed_at', $date->month)
                    ->whereYear('completed_at', $date->year)
                    ->count()
            );
        }

        return ['labels' => $labels, 'values' => $values];
    }
}
