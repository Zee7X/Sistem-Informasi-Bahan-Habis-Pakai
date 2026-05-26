import { Head, Link, router } from '@inertiajs/react';
import AppLayout from '@/Layouts/AppLayout';
import { BarChart3, Calendar, FileDown, TrendingUp } from 'lucide-react';
import { useState } from 'react';

export default function Rekap({ rekap, trenBulan, tahun, semester }) {
    const [selectedTahun, setSelectedTahun] = useState(tahun);
    const [selectedSemester, setSelectedSemester] = useState(semester);

    const handleFilter = (e) => {
        e.preventDefault();
        router.get('/kjur/laporan/rekap', {
            tahun: selectedTahun,
            semester: selectedSemester
        }, { preserveState: true });
    };

    // Calculate maximum for chart scaling
    const maxJumlah = Math.max(...(trenBulan?.map(t => t.jumlah) ?? [0]), 1);

    // List of years for dropdown
    const currentYear = new Date().getFullYear();
    const years = Array.from({ length: 5 }, (_, i) => currentYear - i);

    return (
        <AppLayout title="Rekapitulasi Pemakaian Bahan">
            <Head title="Rekapitulasi Pemakaian" />

            <div className="p-5 space-y-6">
                {/* Filter Panel */}
                <form onSubmit={handleFilter} className="card p-4 flex flex-wrap items-end gap-3 max-w-2xl bg-dark-card">
                    <div className="flex-1 min-w-40">
                        <label className="block text-2xs uppercase tracking-wider text-text-secondary mb-1">Tahun Akademik</label>
                        <select
                            value={selectedTahun}
                            onChange={e => setSelectedTahun(e.target.value)}
                            className="input"
                        >
                            {years.map(y => (
                                <option key={y} value={y}>{y}</option>
                            ))}
                        </select>
                    </div>
                    <div className="flex-1 min-w-48">
                        <label className="block text-2xs uppercase tracking-wider text-text-secondary mb-1">Semester</label>
                        <select
                            value={selectedSemester}
                            onChange={e => setSelectedSemester(e.target.value)}
                            className="input"
                        >
                            <option value="1">Semester 1 (Ganjil - Jan s/d Jun)</option>
                            <option value="2">Semester 2 (Genap - Jul s/d Des)</option>
                        </select>
                    </div>
                    <button type="submit" className="btn-primary h-8 px-4">
                        Terapkan Filter
                    </button>
                </form>

                {/* Dashboard Visualization (Sleek CSS Chart) */}
                <div className="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    {/* Chart Card */}
                    <div className="card p-5 lg:col-span-2 space-y-4">
                        <div className="flex items-center gap-2">
                            <TrendingUp size={15} className="text-violet" />
                            <h3 className="text-sm font-semibold text-text-primary">Tren Penggunaan BHP (Jumlah Pengajuan Selesai)</h3>
                        </div>

                        <div className="h-56 flex items-end justify-between gap-2 pt-6 px-2">
                            {trenBulan?.map((t, idx) => {
                                const percentage = (t.jumlah / maxJumlah) * 100;
                                return (
                                    <div key={idx} className="flex-1 flex flex-col items-center group h-full justify-end">
                                        {/* Value Indicator */}
                                        <span className="text-2xs font-mono font-semibold text-text-secondary group-hover:text-violet transition-colors mb-2">
                                            {t.jumlah}
                                        </span>
                                        {/* Visual Bar */}
                                        <div className="w-full max-w-[40px] bg-dark-surface border border-border rounded-t-sm relative h-full flex items-end">
                                            <div
                                                style={{ height: `${percentage}%` }}
                                                className="w-full bg-violet/40 hover:bg-violet border-t border-violet/85 transition-all duration-150 rounded-t-sm"
                                            />
                                        </div>
                                        {/* Month Label */}
                                        <span className="text-2xs text-text-secondary mt-2.5 truncate max-w-[60px]" title={t.bulan}>
                                            {t.bulan}
                                        </span>
                                    </div>
                                );
                            })}
                        </div>
                    </div>

                    {/* Stats Summary */}
                    <div className="card p-5 space-y-4 flex flex-col justify-between">
                        <div className="space-y-4">
                            <div className="flex items-center gap-2">
                                <BarChart3 size={15} className="text-violet" />
                                <h3 className="text-sm font-semibold text-text-primary">Statistik Semester</h3>
                            </div>
                            <div className="space-y-3">
                                <div>
                                    <p className="text-2xs text-text-secondary uppercase tracking-widest">Total Jenis Bahan Terpakai</p>
                                    <p className="text-2xl font-bold text-text-primary mt-0.5">{rekap?.length ?? 0}</p>
                                </div>
                                <div className="divider" />
                                <div>
                                    <p className="text-2xs text-text-secondary uppercase tracking-widest">Total Volume Pemakaian</p>
                                    <p className="text-2xl font-bold text-success mt-0.5">
                                        {rekap?.reduce((acc, curr) => acc + parseFloat(curr.total_pakai), 0).toFixed(1) ?? 0}
                                    </p>
                                </div>
                            </div>
                        </div>

                        <div>
                            <a
                                href={`/kjur/laporan/rekap/export?tahun=${tahun}&semester=${semester}`}
                                className="btn-secondary w-full justify-center text-xs h-8 flex items-center gap-1.5"
                            >
                                <FileDown size={13} /> Export Laporan CSV
                            </a>
                        </div>
                    </div>
                </div>

                {/* Detailed Table */}
                <div className="card overflow-hidden">
                    <div className="px-5 py-3.5 border-b border-border bg-dark-surface/30">
                        <h3 className="text-xs font-semibold text-text-secondary uppercase tracking-wider">Tabel Rekapitulasi Pemakaian</h3>
                    </div>
                    <div className="overflow-x-auto">
                        <table className="w-full">
                            <thead>
                                <tr className="border-b border-border">
                                    <th className="section-header text-left py-2.5 w-12">No</th>
                                    <th className="section-header text-left py-2.5">Nama Bahan</th>
                                    <th className="section-header text-right py-2.5">Total Pemakaian</th>
                                    <th className="section-header text-right py-2.5">Frekuensi Pengambilan</th>
                                </tr>
                            </thead>
                            <tbody className="divide-y divide-border/40">
                                {rekap?.length > 0 ? rekap.map((item, idx) => (
                                    <tr key={idx} className="hover:bg-dark-surface/50 transition-colors">
                                        <td className="px-4 py-2.5 text-sm text-text-secondary font-mono">{idx + 1}</td>
                                        <td className="px-4 py-2.5">
                                            <p className="text-sm text-text-primary font-medium">{item.nama_bahan_snapshot}</p>
                                        </td>
                                        <td className="px-4 py-2.5 text-right font-semibold text-violet text-sm font-mono">
                                            {parseFloat(item.total_pakai)} {item.satuan_snapshot}
                                        </td>
                                        <td className="px-4 py-2.5 text-right text-sm text-text-primary font-mono">
                                            {item.frekuensi} kali
                                        </td>
                                    </tr>
                                )) : (
                                    <tr>
                                        <td colSpan={4} className="px-4 py-12 text-center text-sm text-text-secondary">
                                            Tidak ada pemakaian bahan terekam untuk semester ini.
                                        </td>
                                    </tr>
                                )}
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </AppLayout>
    );
}
