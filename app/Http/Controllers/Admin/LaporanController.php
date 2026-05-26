<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Bahan;
use App\Models\LogStok;
use App\Models\Pengajuan;
use App\Models\PengajuanItem;
use Illuminate\Http\Request;
use Inertia\Inertia;

class LaporanController extends Controller
{
    public function index(Request $request)
    {
        $tahun = $request->integer('tahun', now()->year);
        $bulan = $request->get('bulan', ''); // Empty string means "All Months"

        // 1. Calculate Paginated Rekap Pemakaian per Bahan
        $bahans = Bahan::with('satuan')->paginate(10)->withQueryString();

        $bahans->through(function ($b) use ($tahun, $bulan) {
            // Filter logs for this bahan in the selected period
            $logsQuery = LogStok::where('bahan_id', $b->id)
                ->whereYear('tanggal', $tahun);

            if ($bulan !== '') {
                $logsQuery->whereMonth('tanggal', $bulan);
            }

            // Sum masuk
            $totalMasuk = (int) (clone $logsQuery)->where('jenis', 'masuk')->sum('jumlah');
            
            // Sum keluar
            $totalKeluar = (int) (clone $logsQuery)->where('jenis', 'keluar')->sum('jumlah');
            
            // Sum adjust (opname / adjust can increase or decrease)
            $adjusts = (clone $logsQuery)->whereIn('jenis', ['adjust', 'opname'])->get();
            foreach ($adjusts as $adj) {
                $diff = $adj->stok_sesudah - $adj->stok_sebelum;
                if ($diff > 0) {
                    $totalMasuk += $diff;
                } else {
                    $totalKeluar += abs($diff);
                }
            }

            $stokAkhir = $b->stok;
            $stokAwal = $stokAkhir - $totalMasuk + $totalKeluar;

            return [
                'bahan_id' => $b->id,
                'nama_bahan' => $b->nama_bahan,
                'kode_bahan' => $b->kode_bahan,
                'stok_awal' => $stokAwal,
                'total_masuk' => $totalMasuk,
                'total_keluar' => $totalKeluar,
                'stok_akhir' => $stokAkhir,
            ];
        });

        // 2. Summary stats for the period (independent of pagination page)
        $pengajuanQuery = Pengajuan::whereYear('tanggal_pakai', $tahun);
        if ($bulan !== '') {
            $pengajuanQuery->whereMonth('tanggal_pakai', $bulan);
        }

        $totalPengajuan = (clone $pengajuanQuery)->count();
        $completed = (clone $pengajuanQuery)->where('status', 'completed')->count();
        
        $totalItems = (int) PengajuanItem::whereHas('pengajuan', function ($q) use ($tahun, $bulan) {
            $q->whereYear('tanggal_pakai', $tahun);
            if ($bulan !== '') {
                $q->whereMonth('tanggal_pakai', $bulan);
            }
            $q->where('status', 'completed');
        })->sum('jumlah');

        // Sum total mutasi keluar secara menyeluruh (untuk periode tersebut)
        $totalKeluarAll = (int) LogStok::where('jenis', 'keluar')
            ->whereYear('tanggal', $tahun)
            ->when($bulan !== '', fn($q) => $q->whereMonth('tanggal', $bulan))
            ->sum('jumlah');

        $summary = [
            'total_pengajuan' => $totalPengajuan,
            'completed' => $completed,
            'total_items' => $totalItems,
            'total_keluar' => $totalKeluarAll,
        ];

        $laporan = [
            'summary' => $summary,
            'items' => $bahans, // ini berupa paginated object sekarang
        ];

        // 3. Constants
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

        $filters = $request->only(['tahun', 'bulan']);

        return Inertia::render('Admin/Laporan/Index', compact('laporan', 'bulanList', 'tahun', 'bulan', 'filters'));
    }

    public function export(Request $request)
    {
        $tahun = $request->integer('tahun', now()->year);
        $bulan = $request->get('bulan', '');

        // Calculate Rekap Pemakaian per Bahan (seluruhnya tanpa paginasi untuk ekspor file)
        $bahans = Bahan::with('satuan')->get();
        $items = [];

        foreach ($bahans as $b) {
            $logsQuery = LogStok::where('bahan_id', $b->id)
                ->whereYear('tanggal', $tahun);

            if ($bulan !== '') {
                $logsQuery->whereMonth('tanggal', $bulan);
            }

            $totalMasuk = (int) (clone $logsQuery)->where('jenis', 'masuk')->sum('jumlah');
            $totalKeluar = (int) (clone $logsQuery)->where('jenis', 'keluar')->sum('jumlah');
            
            $adjusts = (clone $logsQuery)->whereIn('jenis', ['adjust', 'opname'])->get();
            foreach ($adjusts as $adj) {
                $diff = $adj->stok_sesudah - $adj->stok_sebelum;
                if ($diff > 0) {
                    $totalMasuk += $diff;
                } else {
                    $totalKeluar += abs($diff);
                }
            }

            $stokAkhir = $b->stok;
            $stokAwal = $stokAkhir - $totalMasuk + $totalKeluar;

            $items[] = [
                'kode_bahan' => $b->kode_bahan,
                'nama_bahan' => $b->nama_bahan,
                'stok_awal' => $stokAwal,
                'total_masuk' => $totalMasuk,
                'total_keluar' => $totalKeluar,
                'stok_akhir' => $stokAkhir,
                'satuan' => $b->satuan?->nama ?? '-',
            ];
        }

        $namaBulan = 'Semua Bulan';
        if ($bulan !== '') {
            $months = [
                '01' => 'Januari', '02' => 'Februari', '03' => 'Maret', '04' => 'April',
                '05' => 'Mei', '06' => 'Juni', '07' => 'Juli', '08' => 'Agustus',
                '09' => 'September', '10' => 'Oktober', '11' => 'November', '12' => 'Desember'
            ];
            $namaBulan = $months[$bulan] ?? $bulan;
        }

        $headers = [
            'Content-Type'        => 'text/csv',
            'Content-Disposition' => 'attachment; filename="laporan-rekap-bhp-' . $tahun . '-' . strtolower($namaBulan) . '.csv"',
            'Pragma'              => 'no-cache',
            'Cache-Control'       => 'must-revalidate, post-check=0, pre-check=0',
            'Expires'             => '0',
        ];

        $callback = function () use ($items, $tahun, $namaBulan) {
            $file = fopen('php://output', 'w');
            // Write UTF-8 BOM
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
            
            fputcsv($file, ['LAPORAN REKAPITULASI PENGGUNAAN BAHAN HABIS PAKAI (BHP)']);
            fputcsv($file, ["Periode: {$namaBulan} {$tahun}"]);
            fputcsv($file, []);
            fputcsv($file, ['No', 'Kode Bahan', 'Nama Bahan', 'Stok Awal', 'Total Masuk', 'Total Keluar', 'Stok Akhir', 'Satuan']);

            foreach ($items as $idx => $row) {
                fputcsv($file, [
                    $idx + 1,
                    $row['kode_bahan'],
                    $row['nama_bahan'],
                    $row['stok_awal'],
                    $row['total_masuk'],
                    $row['total_keluar'],
                    $row['stok_akhir'],
                    $row['satuan']
                ]);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
