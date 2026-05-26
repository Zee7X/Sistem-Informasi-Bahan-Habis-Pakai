<?php

namespace App\Http\Controllers\KetuaJurusan;

use App\Http\Controllers\Controller;
use App\Models\Pengajuan;
use App\Models\PengajuanItem;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class LaporanController extends Controller
{
    public function rekap(Request $request): Response
    {
        $tahun    = $request->integer('tahun', now()->year);
        $semester = $request->get('semester', '1'); // '1' = Jan-Jun, '2' = Jul-Des

        [$bulanAwal, $bulanAkhir] = $semester === '1'
            ? [1, 6]
            : [7, 12];

        // Rekapitulasi per bahan dalam semester dipilih
        $rekap = PengajuanItem::with('bahan.satuan')
            ->whereHas('pengajuan', fn ($q) =>
                $q->where('status', 'completed')
                  ->whereYear('completed_at', $tahun)
                  ->whereMonth('completed_at', '>=', $bulanAwal)
                  ->whereMonth('completed_at', '<=', $bulanAkhir)
            )
            ->selectRaw('bahan_id, nama_bahan_snapshot, satuan_snapshot, SUM(jumlah) as total_pakai, COUNT(*) as frekuensi')
            ->groupBy('bahan_id', 'nama_bahan_snapshot', 'satuan_snapshot')
            ->orderByDesc('total_pakai')
            ->get();

        // Tren per bulan dalam semester tersebut
        $trenBulan = collect();
        for ($bulan = $bulanAwal; $bulan <= $bulanAkhir; $bulan++) {
            $jumlah = Pengajuan::where('status', 'completed')
                ->whereYear('completed_at', $tahun)
                ->whereMonth('completed_at', $bulan)
                ->count();
            $trenBulan->push(['bulan' => \Carbon\Carbon::create($tahun, $bulan)->translatedFormat('F'), 'jumlah' => $jumlah]);
        }

        return Inertia::render('KetuaJurusan/Laporan/Rekap', compact('rekap', 'trenBulan', 'tahun', 'semester'));
    }

    /** Export rekapitulasi pemakaian ke format CSV */
    public function exportRekap(Request $request)
    {
        $tahun    = $request->integer('tahun', now()->year);
        $semester = $request->get('semester', '1');

        [$bulanAwal, $bulanAkhir] = $semester === '1' ? [1, 6] : [7, 12];

        $rekap = PengajuanItem::whereHas('pengajuan', fn ($q) =>
            $q->where('status', 'completed')
              ->whereYear('completed_at', $tahun)
              ->whereMonth('completed_at', '>=', $bulanAwal)
              ->whereMonth('completed_at', '<=', $bulanAkhir)
        )
        ->selectRaw('nama_bahan_snapshot, satuan_snapshot, SUM(jumlah) as total_pakai, COUNT(*) as frekuensi')
        ->groupBy('nama_bahan_snapshot', 'satuan_snapshot')
        ->orderByDesc('total_pakai')
        ->get();

        $headers = [
            'Content-Type'        => 'text/csv',
            'Content-Disposition' => 'attachment; filename="rekap-pemakaian-' . $tahun . '-semester-' . $semester . '.csv"',
            'Pragma'              => 'no-cache',
            'Cache-Control'       => 'must-revalidate, post-check=0, pre-check=0',
            'Expires'             => '0',
        ];

        $callback = function () use ($rekap) {
            $file = fopen('php://output', 'w');
            // Write UTF-8 BOM to support Indonesian Excel formatting
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
            
            fputcsv($file, ['No', 'Nama Bahan', 'Total Pemakaian', 'Satuan', 'Frekuensi Pengambilan']);

            foreach ($rekap as $idx => $row) {
                fputcsv($file, [
                    $idx + 1,
                    $row->nama_bahan_snapshot,
                    $row->total_pakai,
                    $row->satuan_snapshot,
                    $row->frekuensi
                ]);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}

