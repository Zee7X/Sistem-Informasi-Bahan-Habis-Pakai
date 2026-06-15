<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Bahan;
use App\Models\LogStok;
use App\Models\Pengajuan;
use App\Models\PengajuanItem;
use Illuminate\Http\Request;
use App\Services\LaporanService;
use Inertia\Inertia;

class LaporanController extends Controller
{
    public function __construct(
        private readonly LaporanService $laporanService
    ) {}

    public function index(Request $request)
    {
        $tahun = $request->integer('tahun', now()->year);
        $bulan = $request->get('bulan', ''); // Empty string means "All Months"

        // 1. Calculate Paginated Rekap Pemakaian per Bahan
        $bahans = Bahan::with('satuan')->paginate(10)->withQueryString();

        $bahans->through(function ($b) use ($tahun, $bulan) {
            $data = $this->calculateBahanStats($b, $tahun, $bulan);
            return array_merge(['bahan_id' => $b->id, 'nama_bahan' => $b->nama_bahan, 'kode_bahan' => $b->kode_bahan], $data);
        });

        // 2. Summary stats
        $summary = $this->calculateSummary($tahun, $bulan);

        $laporan = [
            'summary' => $summary,
            'items' => $bahans,
        ];

        $bulanList = $this->getBulanList();
        $filters = $request->only(['tahun', 'bulan']);

        return Inertia::render('Admin/Laporan/Index', compact('laporan', 'bulanList', 'tahun', 'bulan', 'filters'));
    }

    public function export(Request $request)
    {
        $tahun = $request->integer('tahun', now()->year);
        $bulan = $request->get('bulan', '');
        $format = $request->get('format', 'csv');

        if ($format === 'pdf') {
            return $this->laporanService->generatePdfRekap($tahun, $bulan)->download("laporan-bhp-{$tahun}.pdf");
        }

        $items = $this->laporanService->getAdminRekap($tahun, $bulan);
        $namaBulan = $this->getNamaBulanLabel($bulan);

        $headers = [
            'Content-Type'        => 'text/csv',
            'Content-Disposition' => 'attachment; filename="laporan-rekap-bhp-' . $tahun . '-' . strtolower($namaBulan) . '.csv"',
            'Pragma'              => 'no-cache',
            'Cache-Control'       => 'must-revalidate, post-check=0, pre-check=0',
            'Expires'             => '0',
        ];

        $callback = function () use ($items, $tahun, $namaBulan) {
            $file = fopen('php://output', 'w');
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
            fputcsv($file, ['LAPORAN REKAPITULASI PENGGUNAAN BAHAN HABIS PAKAI (BHP)']);
            fputcsv($file, ["Periode: {$namaBulan} {$tahun}"]);
            fputcsv($file, []);
            fputcsv($file, ['No', 'Kode Bahan', 'Nama Bahan', 'Stok Awal', 'Total Masuk', 'Total Keluar', 'Stok Akhir', 'Satuan']);

            foreach ($items as $idx => $row) {
                fputcsv($file, [$idx + 1, $row['kode_bahan'], $row['nama_bahan'], $row['stok_awal'], $row['total_masuk'], $row['total_keluar'], $row['stok_akhir'], $row['satuan']]);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    private function calculateBahanStats($b, $tahun, $bulan)
    {
        $logsQuery = LogStok::where('bahan_id', $b->id)->whereYear('tanggal', $tahun);
        if ($bulan !== '') $logsQuery->whereMonth('tanggal', $bulan);

        $totalMasuk = (int) (clone $logsQuery)->where('jenis', 'masuk')->sum('jumlah');
        $totalKeluar = (int) (clone $logsQuery)->where('jenis', 'keluar')->sum('jumlah');

        $adjusts = (clone $logsQuery)->whereIn('jenis', ['adjust', 'opname'])->get();
        foreach ($adjusts as $adj) {
            $diff = $adj->stok_sesudah - $adj->stok_sebelum;
            if ($diff > 0) $totalMasuk += $diff; else $totalKeluar += abs($diff);
        }

        return [
            'stok_awal' => $b->stok - $totalMasuk + $totalKeluar,
            'total_masuk' => $totalMasuk,
            'total_keluar' => $totalKeluar,
            'stok_akhir' => $b->stok,
        ];
    }

    private function calculateSummary($tahun, $bulan)
    {
        $pq = Pengajuan::whereYear('tanggal_pakai', $tahun);
        if ($bulan !== '') $pq->whereMonth('tanggal_pakai', $bulan);

        return [
            'total_pengajuan' => (clone $pq)->count(),
            'completed' => (clone $pq)->where('status', 'completed')->count(),
            'total_items' => (int) PengajuanItem::whereHas('pengajuan', fn($q) =>
                $q->whereYear('tanggal_pakai', $tahun)->when($bulan !== '', fn($q) => $q->whereMonth('tanggal_pakai', $bulan))->where('status', 'completed')
            )->sum('jumlah'),
            'total_keluar' => (int) LogStok::where('jenis', 'keluar')->whereYear('tanggal', $tahun)->when($bulan !== '', fn($q) => $q->whereMonth('tanggal', $bulan))->sum('jumlah'),
        ];
    }

    private function getNamaBulanLabel($bulan) {
        if ($bulan === '') return 'Semua Bulan';
        $months = ['01' => 'Januari', '02' => 'Februari', '03' => 'Maret', '04' => 'April', '05' => 'Mei', '06' => 'Juni', '07' => 'Juli', '08' => 'Agustus', '09' => 'September', '10' => 'Oktober', '11' => 'November', '12' => 'Desember'];
        return $months[$bulan] ?? $bulan;
    }

    private function getBulanList() {
        return [
            ['value' => '01', 'label' => 'Januari'], ['value' => '02', 'label' => 'Februari'], ['value' => '03', 'label' => 'Maret'],
            ['value' => '04', 'label' => 'April'], ['value' => '05', 'label' => 'Mei'], ['value' => '06', 'label' => 'Juni'],
            ['value' => '07', 'label' => 'Juli'], ['value' => '08', 'label' => 'Agustus'], ['value' => '09', 'label' => 'September'],
            ['value' => '10', 'label' => 'Oktober'], ['value' => '11', 'label' => 'November'], ['value' => '12', 'label' => 'Desember'],
        ];
    }
}
