import { Head, Link, router } from '@inertiajs/react';
import AppLayout from '@/Layouts/AppLayout';
import Pagination from '@/Components/Pagination';
import { Plus, Eye, Search } from 'lucide-react';
import { useState } from 'react';

function StatusChip({ status }) {
    const map = {
        pending_review: { label: 'Pending Review', cls: 'chip-warning' },
        approved:       { label: 'Disetujui',       cls: 'chip-violet' },
        completed:      { label: 'Selesai',         cls: 'chip-success' },
        rejected:       { label: 'Ditolak',         cls: 'chip-error' },
    };
    const s = map[status] ?? { label: status, cls: 'chip-neutral' };
    return <span className={`chip ${s.cls}`}>{s.label}</span>;
}

export default function Index({ pengajuan, filters }) {
    const [search, setSearch] = useState(filters?.search ?? '');

    const handleSearch = (e) => {
        e.preventDefault();
        router.get('/mahasiswa/pengajuan', { search }, { preserveState: true, replace: true });
    };

    return (
        <AppLayout title="Pengajuan Saya">
            <Head title="Pengajuan Saya" />
            <div className="p-5 space-y-4">
                <div className="flex gap-2 items-center">
                    <form onSubmit={handleSearch} className="relative flex-1 max-w-72">
                        <Search size={13} className="absolute left-2.5 top-1/2 -translate-y-1/2 text-text-secondary" />
                        <input
                            value={search}
                            onChange={e => setSearch(e.target.value)}
                            placeholder="Cari kode atau mata kuliah..."
                            className="input pl-8"
                        />
                    </form>
                    <div className="flex-1" />
                    <Link href="/mahasiswa/pengajuan/create" className="btn-primary">
                        <Plus size={14} /> Pengajuan Baru
                    </Link>
                </div>
                <div className="card overflow-hidden">
                    <div className="overflow-x-auto">
                        <table className="w-full">
                            <thead>
                                <tr className="border-b border-border">
                                    <th className="section-header text-left py-2.5">Kode</th>
                                    <th className="section-header text-left py-2.5 hidden md:table-cell">Mata Kuliah</th>
                                    <th className="section-header text-left py-2.5 hidden lg:table-cell">Tgl Pakai</th>
                                    <th className="section-header text-left py-2.5">Status</th>
                                    <th className="section-header text-right py-2.5">Aksi</th>
                                </tr>
                            </thead>
                            <tbody className="divide-y divide-border/40">
                                {pengajuan?.data?.length > 0 ? pengajuan.data.map(p => (
                                    <tr key={p.id} className="hover:bg-dark-surface/50 transition-colors">
                                        <td className="px-4 py-2.5"><span className="identifier">{p.kode_pengajuan}</span></td>
                                        <td className="px-4 py-2.5 text-sm text-text-secondary hidden md:table-cell">{p.mata_kuliah ?? '-'}</td>
                                        <td className="px-4 py-2.5 text-sm text-text-secondary hidden lg:table-cell">{p.tanggal_pakai}</td>
                                        <td className="px-4 py-2.5"><StatusChip status={p.status} /></td>
                                        <td className="px-4 py-2.5 text-right">
                                            <Link href={`/mahasiswa/pengajuan/${p.id}`} className="btn-ghost btn-sm">
                                                <Eye size={13} /> Detail
                                            </Link>
                                        </td>
                                    </tr>
                                )) : (
                                    <tr>
                                        <td colSpan={5} className="px-4 py-16 text-center">
                                            {filters?.search ? (
                                                <p className="text-sm text-text-secondary">Tidak ada hasil untuk "{filters.search}".</p>
                                            ) : (
                                                <>
                                                    <p className="text-sm text-text-secondary mb-3">Belum ada pengajuan BHP.</p>
                                                    <Link href="/mahasiswa/pengajuan/create" className="btn-primary">
                                                        <Plus size={14} /> Buat Pengajuan Pertama
                                                    </Link>
                                                </>
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
