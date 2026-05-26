import { Head, router } from '@inertiajs/react';
import AppLayout from '@/Layouts/AppLayout';
import Pagination from '@/Components/Pagination';
import { Download, BarChart3 } from 'lucide-react';

export default function Index({ laporan, bulanList, tahun, bulan, filters }) {
    
    const handleFilterChange = (e, filterName) => {
        const value = e.target.value;
        const currentParams = {
            tahun,
            bulan,
            [filterName]: value
        };
        router.get('/admin/laporan', currentParams, { preserveState: true });
    };

    return (
        <AppLayout title="Laporan BHP">
            <Head title="Laporan BHP" />
            <div className="p-5 space-y-5">
                
                {/* Header Filter Panel */}
                <div className="card p-4 flex flex-wrap gap-4 items-center justify-between">
                    <div className="flex gap-3 items-center flex-wrap">
                        <div className="flex flex-col gap-1">
                            <span className="text-2xs text-text-secondary uppercase tracking-widest font-semibold">Tahun</span>
                            <select 
                                value={tahun} 
                                onChange={(e) => handleFilterChange(e, 'tahun')}
                                className="input w-28"
                            >
                                {[2024, 2025, 2026, 2027].map(y => <option key={y} value={y}>{y}</option>)}
                            </select>
                        </div>
                        <div className="flex flex-col gap-1">
                            <span className="text-2xs text-text-secondary uppercase tracking-widest font-semibold">Bulan</span>
                            <select 
                                value={bulan || ''} 
                                onChange={(e) => handleFilterChange(e, 'bulan')}
                                className="input w-40"
                            >
                                <option value="">Semua Bulan</option>
                                {(bulanList ?? []).map(b => <option key={b.value} value={b.value}>{b.label}</option>)}
                            </select>
                        </div>
                    </div>

                    <div className="flex gap-3 items-center self-end">
                        {/* Export Button */}
                        <a 
                            href={`/admin/laporan/export?tahun=${tahun}&bulan=${bulan}`} 
                            className="btn-secondary flex items-center gap-1.5 px-4 py-2 text-xs font-semibold"
                        >
                            <Download size={14} /> 
                            Export Excel / CSV
                        </a>
                    </div>
                </div>

                {/* Summary stats */}
                {laporan?.summary && (
                    <div className="grid grid-cols-2 xl:grid-cols-4 gap-4">
                        {[
                            { label: 'Total Pengajuan', value: laporan.summary.total_pengajuan, border: 'border-l-4 border-violet' },
                            { label: 'Selesai (Completed)', value: laporan.summary.completed, border: 'border-l-4 border-success' },
                            { label: 'Total Item Serah Terima', value: laporan.summary.total_items, border: 'border-l-4 border-amber' },
                            { label: 'Jumlah Mutasi Keluar', value: laporan.summary.total_keluar, border: 'border-l-4 border-error' },
                        ].map(s => (
                            <div key={s.label} className={`card p-4 transition-all hover:scale-[1.02] ${s.border}`}>
                                <p className="text-2xs text-text-secondary uppercase tracking-widest font-semibold mb-1">{s.label}</p>
                                <p className="text-2xl font-bold text-text-primary">{s.value ?? 0}</p>
                            </div>
                        ))}
                    </div>
                )}

                {/* Table card */}
                <div className="card overflow-hidden">
                    <div className="flex items-center gap-2 px-4 py-3 border-b border-border bg-dark-surface/30">
                        <BarChart3 size={16} className="text-violet" />
                        <h2 className="text-sm font-semibold text-text-primary">Tabel Rekapitulasi Pemakaian Bahan</h2>
                    </div>
                    <div className="overflow-x-auto">
                        <table className="w-full">
                            <thead>
                                <tr className="border-b border-border bg-dark-surface/50">
                                    <th className="section-header text-left py-3 px-4">Nama & Kode Bahan</th>
                                    <th className="section-header text-right py-3 px-4">Stok Awal</th>
                                    <th className="section-header text-right py-3 px-4">Total Masuk</th>
                                    <th className="section-header text-right py-3 px-4">Total Keluar</th>
                                    <th className="section-header text-right py-3 px-4">Stok Akhir</th>
                                </tr>
                            </thead>
                            <tbody className="divide-y divide-border/40">
                                {laporan?.items?.data?.length > 0 ? laporan.items.data.map(item => (
                                    <tr key={item.bahan_id} className="hover:bg-dark-surface/50 transition-colors">
                                        <td className="px-4 py-3">
                                            <p className="text-sm font-medium text-text-primary">{item.nama_bahan}</p>
                                            <span className="identifier text-xs">{item.kode_bahan}</span>
                                        </td>
                                        <td className="px-4 py-3 text-right text-sm text-text-secondary font-mono">{item.stok_awal}</td>
                                        <td className="px-4 py-3 text-right text-sm text-success font-semibold font-mono">+{item.total_masuk}</td>
                                        <td className="px-4 py-3 text-right text-sm text-error font-semibold font-mono">-{item.total_keluar}</td>
                                        <td className="px-4 py-3 text-right text-sm font-bold text-text-primary font-mono">{item.stok_akhir}</td>
                                    </tr>
                                )) : (
                                    <tr><td colSpan={5} className="px-4 py-12 text-center text-sm text-text-secondary">Tidak ada data laporan pemakaian bahan untuk periode ini.</td></tr>
                                )}
                            </tbody>
                        </table>
                    </div>

                    {/* Pagination Component */}
                    {laporan?.items && <Pagination pagination={laporan.items} />}
                </div>

            </div>
        </AppLayout>
    );
}
