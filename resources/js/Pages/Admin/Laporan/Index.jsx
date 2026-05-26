import { Head, Link } from '@inertiajs/react';
import AppLayout from '@/Layouts/AppLayout';
import { Download, BarChart3 } from 'lucide-react';

export default function Index({ laporan, bulanList, tahun, bulan }) {
    return (
        <AppLayout title="Laporan BHP">
            <Head title="Laporan BHP" />
            <div className="p-5 space-y-4">
                {/* Filter */}
                <div className="card p-4 flex flex-wrap gap-3 items-center">
                    <form method="get" action="/admin/laporan" className="flex gap-2 items-center flex-wrap">
                        <select name="tahun" defaultValue={tahun} className="input w-28">
                            {[2024, 2025, 2026].map(y => <option key={y} value={y}>{y}</option>)}
                        </select>
                        <select name="bulan" defaultValue={bulan} className="input w-36">
                            <option value="">Semua Bulan</option>
                            {(bulanList ?? []).map(b => <option key={b.value} value={b.value}>{b.label}</option>)}
                        </select>
                        <button type="submit" className="btn-primary">Filter</button>
                    </form>
                    <div className="flex-1" />
                    <a href={`/admin/laporan/export?tahun=${tahun}&bulan=${bulan}`} className="btn-secondary">
                        <Download size={14} /> Export PDF
                    </a>
                </div>

                {/* Summary stats */}
                {laporan?.summary && (
                    <div className="grid grid-cols-2 xl:grid-cols-4 gap-3">
                        {[
                            { label: 'Total Pengajuan', value: laporan.summary.total_pengajuan },
                            { label: 'Selesai', value: laporan.summary.completed },
                            { label: 'Total Item Terpakai', value: laporan.summary.total_items },
                            { label: 'Pengeluaran Stok', value: laporan.summary.total_keluar },
                        ].map(s => (
                            <div key={s.label} className="card p-4">
                                <p className="text-2xs text-text-secondary uppercase tracking-widest mb-1">{s.label}</p>
                                <p className="text-xl font-semibold text-text-primary">{s.value ?? 0}</p>
                            </div>
                        ))}
                    </div>
                )}

                {/* Table */}
                <div className="card overflow-hidden">
                    <div className="flex items-center gap-2 px-4 py-3 border-b border-border">
                        <BarChart3 size={14} className="text-violet" />
                        <h2 className="text-sm font-medium text-text-primary">Rekap Penggunaan per Bahan</h2>
                    </div>
                    <div className="overflow-x-auto">
                        <table className="w-full">
                            <thead>
                                <tr className="border-b border-border">
                                    <th className="section-header text-left py-2.5">Bahan</th>
                                    <th className="section-header text-right py-2.5">Stok Awal</th>
                                    <th className="section-header text-right py-2.5">Masuk</th>
                                    <th className="section-header text-right py-2.5">Keluar</th>
                                    <th className="section-header text-right py-2.5">Stok Akhir</th>
                                </tr>
                            </thead>
                            <tbody className="divide-y divide-border/40">
                                {laporan?.items?.length > 0 ? laporan.items.map(item => (
                                    <tr key={item.bahan_id} className="hover:bg-dark-surface/50 transition-colors">
                                        <td className="px-4 py-2.5">
                                            <p className="text-sm text-text-primary">{item.nama_bahan}</p>
                                            <span className="identifier text-xs">{item.kode_bahan}</span>
                                        </td>
                                        <td className="px-4 py-2.5 text-right text-sm text-text-secondary">{item.stok_awal}</td>
                                        <td className="px-4 py-2.5 text-right text-sm text-success">+{item.total_masuk}</td>
                                        <td className="px-4 py-2.5 text-right text-sm text-error">-{item.total_keluar}</td>
                                        <td className="px-4 py-2.5 text-right text-sm font-medium text-text-primary">{item.stok_akhir}</td>
                                    </tr>
                                )) : (
                                    <tr><td colSpan={5} className="px-4 py-12 text-center text-sm text-text-secondary">Tidak ada data laporan untuk periode ini.</td></tr>
                                )}
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </AppLayout>
    );
}
