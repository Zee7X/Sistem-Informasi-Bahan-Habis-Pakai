<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LogStok;
use Illuminate\Http\Request;
use Inertia\Inertia;

class LogStokController extends Controller
{
    public function index(Request $request)
    {
        $tahun = $request->integer('tahun', now()->year);
        $bulan = $request->get('bulan', ''); // Empty means "All Months"

        $logQuery = LogStok::with(['bahan.satuan', 'createdBy', 'pengajuan.user', 'bahanMasuk']);

        // Filter by search query
        if ($request->filled('search')) {
            $s = $request->search;
            $logQuery->where(function ($q) use ($s) {
                $q->where('keterangan', 'like', "%{$s}%")
                  ->orWhere('jenis', 'like', "%{$s}%")
                  ->orWhereHas('bahan', fn ($b) => $b->where('nama_bahan', 'like', "%{$s}%")->orWhere('kode_bahan', 'like', "%{$s}%"))
                  ->orWhereHas('createdBy', fn ($u) => $u->where('name', 'like', "%{$s}%"));
            });
        }

        // Filter by Year & Month
        $logQuery->whereYear('tanggal', $tahun);
        if ($bulan !== '') {
            $logQuery->whereMonth('tanggal', $bulan);
        }

        $logs = $logQuery->latest('tanggal')->paginate(10)->withQueryString();

        $bulanList = [
            ['value' => '01', 'label' => 'Januari'],
            ['value' => '02', 'label' => 'Februari'],
            ['value' => '03', 'label' => 'Maret'],
            ['value' => '04', 'label' => 'April'],
            ['value' => '05', 'label' => 'Mei'],
            ['value' => '06', 'label' => 'Juni'],
            ['value' => '07', 'label' => 'Juli'],
            ['value' => '08', 'label' => 'Agustus'],
            ['value' => '09', 'label' => 'September'],
            ['value' => '10', 'label' => 'Oktober'],
            ['value' => '11', 'label' => 'November'],
            ['value' => '12', 'label' => 'Desember'],
        ];

        $filters = $request->only(['tahun', 'bulan', 'search']);

        return Inertia::render('Admin/LogStok/Index', compact('logs', 'bulanList', 'tahun', 'bulan', 'filters'));
    }
}
