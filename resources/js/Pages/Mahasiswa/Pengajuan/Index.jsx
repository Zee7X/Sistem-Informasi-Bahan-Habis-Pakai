import { Head, Link, router } from '@inertiajs/react';
import AppLayout from '@/Layouts/AppLayout';
import Pagination from '@/Components/Pagination';
import { Plus, Eye, Search, Calendar, ClipboardList } from 'lucide-react';
import { useState } from 'react';

function StatusBadge({ status }) {
    const map = {
        pending_review: { label: 'Menunggu Review', cls: 'bg-warning/10 text-warning border-warning/20' },
        approved:       { label: 'Disetujui',        cls: 'bg-violet/10 text-violet border-violet/20' },
        completed:      { label: 'Selesai',           cls: 'bg-success/10 text-success border-success/20' },
        rejected:       { label: 'Ditolak',           cls: 'bg-error/10 text-error border-error/20' },
    };
    const s = map[status] ?? { label: status, cls: 'bg-text-secondary/10 text-text-secondary border-border' };
    return (
        <span className={`inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-semibold border ${s.cls}`}>
            <span className="w-1 h-1 rounded-full bg-current" />
            {s.label}
        </span>
    );
}

export default function Index({ pengajuan, filters }) {
    const [search, setSearch] = useState(filters?.search ?? '');

    const handleSearch = (e) => {
        e.preventDefault();
        router.get('/mahasiswa/pengajuan', { search }, { preserveState: true, replace: true });
    };

    const formatTanggal = (dateString) => {
        if (!dateString) return '-';
        try {
            const date = new Date(dateString);
            return date.toLocaleDateString('id-ID', { day: '2-digit', month: 'long', year: 'numeric' });
        } catch (e) {
            return dateString;
        }
    };

    return (
        <AppLayout title="Pengajuan Saya">
            <Head title="Pengajuan Saya" />
            <div className="p-5 space-y-5 max-w-7xl mx-auto">

                {/* Header Actions */}
                <div className="card p-4 flex flex-wrap gap-4 items-center justify-between">
                    <form onSubmit={handleSearch} className="relative flex-1 min-w-[280px] max-w-sm">
                        <Search size={16} className="absolute left-3 top-1/2 -translate-y-1/2 text-text-secondary" />
                        <input
                            value={search}
                            onChange={e => setSearch(e.target.value)}
                            placeholder="Cari kode atau mata kuliah..."
                            className="input pl-10 py-2 w-full text-sm"
                        />
                    </form>
                    <Link href="/mahasiswa/pengajuan/create" className="btn-primary inline-flex items-center gap-1.5 py-2 px-4">
                        <Plus size={16} /> Buat Pengajuan Baru
                    </Link>
                </div>

                {/* Table list */}
                <div className="card overflow-hidden">
                    <div className="flex items-center gap-2 px-5 py-3.5 border-b border-border bg-dark-surface/30">
                        <ClipboardList size={16} className="text-violet" />
                        <h2 className="text-sm font-semibold text-text-primary">Riwayat Pengajuan Bahan Saya</h2>
                    </div>

                    <div className="overflow-x-auto">
                        <table className="w-full">
                            <thead>
                                <tr className="border-b border-border bg-dark-surface/50">
                                    <th className="section-header text-left py-3 px-5">Kode Pengajuan</th>
                                    <th className="section-header text-left py-3 px-5 hidden md:table-cell">Mata Kuliah</th>
                                    <th className="section-header text-left py-3 px-5 hidden lg:table-cell">Tanggal Pemakaian</th>
                                    <th className="section-header text-center py-3 px-5">Status</th>
                                    <th className="section-header text-right py-3 px-5">Aksi</th>
                                </tr>
                            </thead>
                            <tbody className="divide-y divide-border/40">
                                {pengajuan?.data?.length > 0 ? pengajuan.data.map(p => (
                                    <tr key={p.id} className="hover:bg-dark-surface/30 transition-colors">
                                        <td className="px-5 py-3.5"><span className="identifier font-mono">{p.kode_pengajuan}</span></td>
                                        <td className="px-5 py-3.5 text-sm font-medium text-text-primary hidden md:table-cell">{p.mata_kuliah ?? 'Mandiri'}</td>
                                        <td className="px-5 py-3.5 text-sm text-text-secondary hidden lg:table-cell">
                                            <div className="flex items-center gap-1.5">
                                                <Calendar size={13} className="text-text-secondary/60" />
                                                <span>{formatTanggal(p.tanggal_pakai)}</span>
                                            </div>
                                        </td>
                                        <td className="px-5 py-3.5 text-center"><StatusBadge status={p.status} /></td>
                                        <td className="px-5 py-3.5 text-right">
                                            <Link
                                                href={`/mahasiswa/pengajuan/${p.id}`}
                                                className="btn-secondary inline-flex items-center gap-1 py-1.5 px-3 text-xs"
                                            >
                                                <Eye size={12} /> Detail
                                            </Link>
                                        </td>
                                    </tr>
                                )) : (
                                    <tr>
                                        <td colSpan={5} className="px-5 py-12 text-center">
                                            {filters?.search ? (
                                                <p className="text-sm text-text-secondary">Tidak ada hasil untuk "{filters.search}".</p>
                                            ) : (
                                                <div className="space-y-3">
                                                    <p className="text-sm text-text-secondary">Anda belum memiliki riwayat pengajuan bahan.</p>
                                                    <Link href="/mahasiswa/pengajuan/create" className="btn-primary inline-flex items-center gap-1 py-1.5 px-4">
                                                        <Plus size={14} /> Buat Pengajuan Pertama
                                                    </Link>
                                                </div>
                                            )}
                                        </td>
                                    </tr>
                                )}
                            </tbody>
                        </table>
                    </div>

                    <Pagination pagination={pengajuan} />
                </div>

            </div>
        </AppLayout>
    );
}
