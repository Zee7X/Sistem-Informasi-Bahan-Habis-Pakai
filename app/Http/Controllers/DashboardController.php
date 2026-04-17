<?php

namespace App\Http\Controllers;

use App\Models\Bahan;
use App\Models\User;
use App\Models\PenggunaanBahan;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $role = $user->role;

        // Ambil Statistik Berdasarkan Role
        if ($role === 'mahasiswa') {
            $stats = [
                'total_pengajuan' => PenggunaanBahan::where('requester_user_id', $user->id)->count(),
                'approved_request' => PenggunaanBahan::where('requester_user_id', $user->id)->where('status', 'approved')->count(),
                'pending_request' => PenggunaanBahan::where('requester_user_id', $user->id)->where('status', 'pending')->count(),
                'total_bahan' => Bahan::count(),
            ];

            $recent_activity = PenggunaanBahan::where('requester_user_id', $user->id)
                ->with('bahan')
                ->latest()
                ->take(5)
                ->get();
        } else {
            $stats = [
                'total_bahan' => Bahan::count(),
                'total_user' => User::count(),
                'pending_request' => PenggunaanBahan::where('status', 'pending')->count(),
                'stok_kritis' => Bahan::whereColumn('stok', '<=', 'minimal_stok')->count(),
            ];

            $recent_activity = PenggunaanBahan::with(['bahan', 'requester'])
                ->latest()
                ->take(5)
                ->get();
        }

        // Data Grafik: Tren Penggunaan 6 Bulan Terakhir
        $chartLabels = collect();
        $chartValues = collect();

        for ($i = 5; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $chartLabels->push($date->format('M Y'));

            $query = PenggunaanBahan::where('status', 'approved')
                ->whereMonth('tanggal_pemakaian', $date->month)
                ->whereYear('tanggal_pemakaian', $date->year);

            if ($role === 'mahasiswa') {
                $query->where('requester_user_id', $user->id);
            }

            $chartValues->push($query->count());
        }

        $chartData = [
            'labels' => $chartLabels,
            'values' => $chartValues,
        ];

        return view('dashboard', compact('role', 'stats', 'recent_activity', 'chartData'));
    }
}
