import { Head, router } from '@inertiajs/react';
import AppLayout from '@/Layouts/AppLayout';
import Pagination from '@/Components/Pagination';
import { History, Search, ArrowRight, User, Calendar } from 'lucide-react';
import { useState } from 'react';

export default function Index({ logs, bulanList, tahun, bulan, filters }) {
    const [searchLog, setSearchLog] = useState(filters?.search || '');

    const handleFilterChange = (e, filterName) => {
        const value = e.target.value;
        const currentParams = {
            tahun,
            bulan,
            search: searchLog,
            [filterName]: value
        };
        router.get('/admin/log-stok', currentParams, { preserveState: true });
    };

    const handleSearchSubmit = (e) => {
        e.preventDefault();
        router.get('/admin/log-stok', {
            tahun,
            bulan,
            search: searchLog
        }, { preserveState: true });
    };

    const getJenisBadge = (jenis) => {
        switch (jenis) {
            case 'masuk':
                return <span className="px-2.5 py-0.5 text-[11px] font-semibold rounded-full bg-success/20 text-success border border-success/30">Masuk</span>;
            case 'keluar':
                return <span className="px-2.5 py-0.5 text-[11px] font-semibold rounded-full bg-error/20 text-error border border-error/30">Keluar</span>;
            case 'opname':
                return <span className="px-2.5 py-0.5 text-[11px] font-semibold rounded-full bg-violet/20 text-violet border border-violet/30">Opname</span>;
            case 'adjust':
                return <span className="px-2.5 py-0.5 text-[11px] font-semibold rounded-full bg-amber/20 text-amber border border-amber/30">Koreksi</span>;
            default:
                return <span className="px-2.5 py-0.5 text-[11px] font-semibold rounded-full bg-text-secondary/20 text-text-secondary">Lainnya</span>;
        }
    };

    const getStokInfo = (log) => {
        let isIncrease = false;
        let isDecrease = false;
        let sign = '';

        if (log.jenis === 'masuk') {
            isIncrease = true;
            sign = '+';
        } else if (log.jenis === 'keluar') {
            isDecrease = true;
            sign = '-';
        } else {
            // opname atau adjust
            const diff = log.stok_sesudah - log.stok_sebelum;
            if (diff > 0) {
                isIncrease = true;
                sign = '+';
            } else if (diff < 0) {
                isDecrease = true;
                sign = '-';
            }
        }

        return { isIncrease, isDecrease, sign };
    };

    return (
        <AppLayout title="Log Mutasi Stok">
            <Head title="Log Mutasi Stok" />
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

                    <div className="flex flex-col gap-1 w-full max-w-md">
                        <span className="text-2xs text-text-secondary uppercase tracking-widest font-semibold">Cari Log</span>
                        <form onSubmit={handleSearchSubmit} className="relative w-full">
                            <Search size={16} className="absolute left-3 top-1/2 -translate-y-1/2 text-text-secondary" />
                            <input
                                type="text"
                                placeholder="Cari berdasarkan bahan, deskripsi, atau operator..."
                                value={searchLog}
                                onChange={(e) => setSearchLog(e.target.value)}
                                className="input pl-9 pr-24 w-full"
                            />
                            <button type="submit" className="btn-primary py-1 px-3 absolute right-1.5 top-1/2 -translate-y-1/2 text-xs">
                                Cari
                            </button>
                        </form>
                    </div>
                </div>

                {/* Table card */}
                <div className="card overflow-hidden">
                    <div className="flex items-center gap-2 px-4 py-3 border-b border-border bg-dark-surface/30 justify-between">
                        <div className="flex items-center gap-2">
                            <History size={16} className="text-violet" />
                            <h2 className="text-sm font-semibold text-text-primary">Kronologis Pergerakan Stok Laboratorium</h2>
                        </div>
                        <span className="text-xs text-text-secondary">
                            Menampilkan {logs?.data?.length || 0} riwayat mutasi
                        </span>
                    </div>

                    <div className="overflow-x-auto">
                        <table className="w-full">
                            <thead>
                                <tr className="border-b border-border bg-dark-surface/50">
                                    <th className="section-header text-left py-3 px-4">Tanggal & Jam</th>
                                    <th className="section-header text-left py-3 px-4">Item Bahan</th>
                                    <th className="section-header text-center py-3 px-4">Tipe Mutasi</th>
                                    <th className="section-header text-right py-3 px-4">Jumlah</th>
                                    <th className="section-header text-center py-3 px-4">Perubahan Stok</th>
                                    <th className="section-header text-left py-3 px-4">Keterangan / Referensi</th>
                                    <th className="section-header text-left py-3 px-4">Pemohon / Penerima</th>
                                    <th className="section-header text-left py-3 px-4">Operator</th>
                                </tr>
                            </thead>
                            <tbody className="divide-y divide-border/40">
                                {logs?.data?.length > 0 ? logs.data.map(log => {
                                    const { isIncrease, isDecrease, sign } = getStokInfo(log);
                                    return (
                                        <tr key={log.id} className="hover:bg-dark-surface/50 transition-colors">
                                            <td className="px-4 py-3 whitespace-nowrap text-xs text-text-secondary font-mono">
                                                <div className="flex items-center gap-1.5">
                                                    <Calendar size={12} className="text-violet" />
                                                    {new Date(log.tanggal).toLocaleString('id-ID', {
                                                        day: 'numeric',
                                                        month: 'short',
                                                        year: 'numeric',
                                                        hour: '2-digit',
                                                        minute: '2-digit'
                                                    })}
                                                </div>
                                            </td>
                                            <td className="px-4 py-3">
                                                <p className="text-sm font-semibold text-text-primary">
                                                    {log.bahan?.nama_bahan || 'Bahan Dihapus'}
                                                </p>
                                                <span className="identifier text-xs">{log.bahan?.kode_bahan || '-'}</span>
                                            </td>
                                            <td className="px-4 py-3 text-center">
                                                {getJenisBadge(log.jenis)}
                                            </td>
                                            <td className={`px-4 py-3 text-right font-bold text-sm font-mono ${isIncrease ? 'text-success' : isDecrease ? 'text-error' : 'text-text-primary'}`}>
                                                {sign}{Math.abs(log.jumlah)} {log.bahan?.satuan?.nama || ''}
                                            </td>
                                            <td className="px-4 py-3">
                                                <div className="flex items-center justify-center gap-1.5 text-xs font-mono">
                                                    <span className="text-text-secondary">{log.stok_sebelum}</span>
                                                    <ArrowRight size={12} className="text-text-secondary/40" />
                                                    <span className={`font-bold ${isIncrease ? 'text-success' : isDecrease ? 'text-error' : 'text-text-primary'}`}>
                                                        {log.stok_sesudah}
                                                    </span>
                                                </div>
                                            </td>
                                        <td className="px-4 py-3 text-sm text-text-primary max-w-xs truncate" title={log.keterangan}>
                                            {log.keterangan || '-'}
                                        </td>
                                        <td className="px-4 py-3 text-xs text-text-secondary whitespace-nowrap">
                                            {log.reference_table === 'pengajuan' && log.pengajuan ? (
                                                <div className="flex flex-col">
                                                    <span className="font-semibold text-text-primary">
                                                        {log.pengajuan.user?.name || 'Mahasiswa'}
                                                    </span>
                                                    {log.pengajuan.kelompok && (
                                                        <span className="text-[10px] text-text-secondary/80">
                                                            {log.pengajuan.kelompok}
                                                        </span>
                                                    )}
                                                </div>
                                            ) : log.reference_table === 'bahan_masuk' && log.bahan_masuk ? (
                                                <span className="font-medium text-text-secondary">
                                                    {log.bahan_masuk.pemasok || 'Supplier'}
                                                </span>
                                            ) : (
                                                <span className="text-text-secondary/60">-</span>
                                            )}
                                        </td>
                                        <td className="px-4 py-3 text-xs text-text-secondary whitespace-nowrap">
                                            <div className="flex items-center gap-1">
                                                <User size={12} className="text-violet/70" />
                                                <span>
                                                    {log.created_by_user?.name || 
                                                     log.created_by?.name || 
                                                     log.createdBy?.name || 
                                                     (log.created_by && typeof log.created_by !== 'object' ? String(log.created_by) : 'System')}
                                                </span>
                                            </div>
                                        </td>
                                    </tr>
                                    );
                                }) : (
                                    <tr><td colSpan={8} className="px-4 py-12 text-center text-sm text-text-secondary">Tidak ada data log mutasi stok untuk kriteria pencarian ini.</td></tr>
                                )}
                            </tbody>
                        </table>
                    </div>

                    {/* Pagination */}
                    {logs && <Pagination pagination={logs} />}
                </div>

            </div>
        </AppLayout>
    );
}
