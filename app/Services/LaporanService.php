<?php

namespace App\Services;

use App\Models\Bahan;
use App\Models\LogStok;
use App\Models\Pengajuan;
use App\Models\PengajuanItem;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class LaporanService
{
    /**
     * Get rekapitulasi stok untuk admin.
     */
    public function getAdminRekap(int $tahun, string $bulan = ''): array
    {
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

        return $items;
    }

    /**
     * Get rekapitulasi pemakaian untuk Ketua Jurusan (per semester).
     */
    public function getKjurRekap(int $tahun, string $semester): Collection
    {
        [$bulanAwal, $bulanAkhir] = $semester === '1' ? [1, 6] : [7, 12];

        return PengajuanItem::whereHas('pengajuan', fn ($q) =>
            $q->where('status', 'completed')
              ->whereYear('completed_at', $tahun)
              ->whereMonth('completed_at', '>=', $bulanAwal)
              ->whereMonth('completed_at', '<=', $bulanAkhir)
        )
        ->selectRaw('nama_bahan_snapshot, satuan_snapshot, SUM(jumlah) as total_pakai, COUNT(*) as frekuensi')
        ->groupBy('nama_bahan_snapshot', 'satuan_snapshot')
        ->orderByDesc('total_pakai')
        ->get();
    }

    /**
     * Generate PDF untuk Berita Acara atau Laporan Bulanan.
     */
    public function generatePdfRekap(int $tahun, string $bulan = '')
    {
        $items = $this->getAdminRekap($tahun, $bulan);
        $namaBulan = $this->getNamaBulan($bulan);

        $data = [
            'items' => $items,
            'tahun' => $tahun,
            'bulan' => $namaBulan,
            'tanggal' => now()->translatedFormat('d F Y'),
        ];

        return Pdf::loadView('exports.laporan-rekap-pdf', $data);
    }

    private function getNamaBulan(string $bulan): string
    {
        if ($bulan === '') return 'Semua Bulan';

        $months = [
            '01' => 'Januari', '02' => 'Februari', '03' => 'Maret', '04' => 'April',
            '05' => 'Mei', '06' => 'Juni', '07' => 'Juli', '08' => 'Agustus',
            '09' => 'September', '10' => 'Oktober', '11' => 'November', '12' => 'Desember'
        ];

        return $months[$bulan] ?? $bulan;
    }
}
