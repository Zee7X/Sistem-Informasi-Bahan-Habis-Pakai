import { Head, Link, router } from '@inertiajs/react';
import AppLayout from '@/Layouts/AppLayout';
import Pagination from '@/Components/Pagination';
import { Eye, Search } from 'lucide-react';
import { useState } from 'react';

function StatusChip({ status }) {
    const map = {
        pending_review: { label: 'Pending Review', cls: 'chip-warning' },
        approved:       { label: 'Approved',        cls: 'chip-violet' },
        completed:      { label: 'Completed',       cls: 'chip-success' },
        rejected:       { label: 'Ditolak',         cls: 'chip-error' },
    };
    const s = map[status] ?? { label: status, cls: 'chip-neutral' };
    return <span className={`chip ${s.cls}`}>{s.label}</span>;
}

export default function Index({ pengajuan, filters }) {
    const [search, setSearch] = useState(filters?.search ?? '');

    const handleSearch = (e) => {
        e.preventDefault();
        router.get('/kjur/transaksi', { search, status: filters?.status }, { preserveState: true, replace: true });
    };

    const filterStatus = (status) => {
        router.get('/kjur/transaksi', { status, search }, { preserveState: true, replace: true });
    };

    const statuses = [
        { value: '', label: 'Semua' },
        { value: 'pending_review', label: 'Pending' },
        { value: 'approved', label: 'Approved' },
        { value: 'completed', label: 'Selesai' },
        { value: 'rejected', label: 'Ditolak' },
    ];

    return (
        <AppLayout title="Transaksi BHP">
            <Head title="Transaksi BHP" />
            <div className="p-5 space-y-4">
                <div className="flex flex-wrap items-center gap-2">
                    <form onSubmit={handleSearch} className="relative flex-1 min-w-48 max-w-72">
                        <Search size={13} className="absolute left-2.5 top-1/2 -translate-y-1/2 text-text-secondary" />
                        <input
                            value={search}
                            onChange={e => setSearch(e.target.value)}
                            placeholder="Cari kode atau mahasiswa..."
                            className="input pl-8"
                        />
                    </form>
                    <div className="flex gap-1 flex-wrap">
                        {statuses.map(s => (
                            <button
                                key={s.value}
                                onClick={() => filterStatus(s.value)}
                                className={(filters?.status === s.value || (!filters?.status && !s.value)) ? 'btn-primary btn-sm' : 'btn-secondary btn-sm'}
                            >
                                {s.label}
                            </button>
                        ))}
                    </div>
                </div>

                <div className="card overflow-hidden">
                    <div className="overflow-x-auto">
                        <table className="w-full">
                            <thead>
                                <tr className="border-b border-border">
                                    <th className="section-header text-left py-2.5">Kode</th>
                                    <th className="section-header text-left py-2.5">Mahasiswa</th>
                                    <th className="section-header text-left py-2.5 hidden md:table-cell">Mata Kuliah</th>
                                    <th className="section-header text-left py-2.5 hidden lg:table-cell">Tgl Pakai</th>
                                    <th className="section-header text-left py-2.5">Status</th>
                                    <th className="section-header text-right py-2.5">Aksi</th>
                                </tr>
                            </thead>
                            <tbody className="divide-y divide-border/40">
                                {pengajuan?.data?.map(p => (
                                    <tr key={p.id} className="hover:bg-dark-surface/50 transition-colors">
                                        <td className="px-4 py-2.5"><span className="identifier">{p.kode_pengajuan}</span></td>
                                        <td className="px-4 py-2.5 text-sm text-text-primary">{p.user?.name}</td>
                                        <td className="px-4 py-2.5 text-sm text-text-secondary hidden md:table-cell">{p.mata_kuliah ?? '-'}</td>
                                        <td className="px-4 py-2.5 text-sm text-text-secondary hidden lg:table-cell">{p.tanggal_pakai}</td>
                                        <td className="px-4 py-2.5"><StatusChip status={p.status} /></td>
                                        <td className="px-4 py-2.5 text-right">
                                            <Link href={`/kjur/transaksi/${p.id}`} className="btn-ghost btn-sm">
                                                <Eye size={13} /> Detail
                                            </Link>
                                        </td>
                                    </tr>
                                ))}
                                {!pengajuan?.data?.length && (
                                    <tr><td colSpan={6} className="px-4 py-12 text-center text-sm text-text-secondary">
                                        {filters?.search ? `Tidak ada hasil untuk "${filters.search}".` : 'Tidak ada transaksi.'}
                                    </td></tr>
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
