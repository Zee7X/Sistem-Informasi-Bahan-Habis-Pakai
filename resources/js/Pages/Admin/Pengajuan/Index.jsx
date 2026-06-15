import { Head, Link, router, usePage } from '@inertiajs/react';
import AppLayout from '@/Layouts/AppLayout';
import Pagination from '@/Components/Pagination';
import { Search, Eye, Calendar, ClipboardList } from 'lucide-react';
import { useState } from 'react';

function StatusBadge({ status }) {
    const map = {
        pending_review: { label: 'Pending Review', cls: 'bg-warning/10 text-warning border-warning/20' },
        approved:       { label: 'Approved',        cls: 'bg-violet/10 text-violet border-violet/20' },
        completed:      { label: 'Completed',       cls: 'bg-success/10 text-success border-success/20' },
        rejected:       { label: 'Ditolak',         cls: 'bg-error/10 text-error border-error/20' },
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
    const { auth } = usePage().props;
    const role = auth?.user?.role;
    const prefix = role === 'ketua_jurusan' ? '/kjur/transaksi' : '/admin/pengajuan';
    const [search, setSearch] = useState(filters?.search ?? '');

    const handleSearch = (e) => {
        e.preventDefault();
        router.get(prefix, { search, status: filters?.status }, { preserveState: true, replace: true });
    };

    const filterStatus = (status) => {
        router.get(prefix, { status, search }, { preserveState: true, replace: true });
    };

    // Format tanggal
    const formatTanggal = (dateString) => {
        if (!dateString) return '-';
        try {
            const date = new Date(dateString);
            return date.toLocaleDateString('id-ID', { day: '2-digit', month: 'long', year: 'numeric' });
        } catch (e) {
            return dateString;
        }
    };

    const statuses = [
        { value: '', label: 'Semua' },
        { value: 'pending_review', label: 'Pending' },
        { value: 'approved', label: 'Approved' },
        { value: 'completed', label: 'Selesai' },
        { value: 'rejected', label: 'Ditolak' },
    ];

    return (
        <AppLayout title="Pengajuan BHP">
            <Head title="Pengajuan BHP" />
            <div className="p-5 space-y-5 max-w-7xl mx-auto">

                {/* Search & Filter Header */}
                <div className="card p-4 flex flex-wrap gap-4 items-center justify-between">
                    <form onSubmit={handleSearch} className="relative flex-1 min-w-[280px] max-w-sm">
                        <Search size={16} className="absolute left-3 top-1/2 -translate-y-1/2 text-text-secondary" />
                        <input
                            value={search}
                            onChange={e => setSearch(e.target.value)}
                            placeholder="Cari kode pengajuan atau nama..."
                            className="input pl-10 py-2 w-full text-sm"
                        />
                    </form>

                    <div className="flex items-center gap-1.5 flex-wrap">
                        <span className="text-2xs text-text-secondary font-semibold uppercase tracking-wider mr-1 select-none">Filter:</span>
                        {statuses.map(s => (
                            <button
                                key={s.value}
                                onClick={() => filterStatus(s.value)}
                                className={`px-3 py-1.5 rounded-lg text-xs font-semibold transition-all duration-150 ${
                                    (filters?.status === s.value || (!filters?.status && !s.value))
                                        ? 'bg-violet text-white shadow-sm shadow-violet/10'
                                        : 'bg-dark-surface/50 text-text-secondary border border-border/80 hover:bg-dark-surface hover:text-text-primary'
                                }`}
                            >
                                {s.label}
                            </button>
                        ))}
                    </div>
                </div>

                {/* Table Card */}
                <div className="card overflow-hidden">
                    <div className="flex items-center gap-2 px-5 py-3.5 border-b border-border bg-dark-surface/30">
                        <ClipboardList size={16} className="text-violet" />
                        <h2 className="text-sm font-semibold text-text-primary">Daftar Pengajuan Bahan Habis Pakai</h2>
                    </div>

                    <div className="overflow-x-auto">
                        <table className="w-full">
                            <thead>
                                <tr className="border-b border-border bg-dark-surface/50">
                                    <th className="section-header text-center py-3 px-5 w-12">No.</th>
                                    <th className="section-header text-left py-3 px-5">Kode Pengajuan</th>
                                    <th className="section-header text-left py-3 px-5">Mahasiswa</th>
                                    <th className="section-header text-left py-3 px-5 hidden md:table-cell">Mata Kuliah</th>
                                    <th className="section-header text-left py-3 px-5 hidden lg:table-cell">Tanggal Pemakaian</th>
                                    <th className="section-header text-center py-3 px-5">Status</th>
                                    <th className="section-header text-right py-3 px-5">Aksi</th>
                                </tr>
                            </thead>
                            <tbody className="divide-y divide-border/40">
                                {pengajuan?.data?.length > 0 ? pengajuan.data.map((p, idx) => (
                                    <tr key={p.id} className="hover:bg-dark-surface/30 transition-colors">
                                        <td className="px-5 py-3.5 text-center text-sm text-text-secondary">
                                            {(pengajuan.meta?.from ?? pengajuan.from ?? 1) + idx}
                                        </td>
                                        <td className="px-5 py-3.5"><span className="identifier font-mono">{p.kode_pengajuan}</span></td>
                                        <td className="px-5 py-3.5 text-sm font-medium text-text-primary">{p.user?.name}</td>
                                        <td className="px-5 py-3.5 text-sm text-text-secondary hidden md:table-cell">{p.mata_kuliah || 'Mandiri'}</td>
                                        <td className="px-5 py-3.5 text-sm text-text-secondary hidden lg:table-cell">
                                            <div className="flex items-center gap-1.5">
                                                <Calendar size={13} className="text-text-secondary/60" />
                                                <span>{formatTanggal(p.tanggal_pakai)}</span>
                                            </div>
                                        </td>
                                        <td className="px-5 py-3.5 text-center"><StatusBadge status={p.status} /></td>
                                        <td className="px-5 py-3.5 text-right">
                                            <Link
                                                href={`${prefix}/${p.id}`}
                                                className="btn-secondary inline-flex items-center gap-1 py-1.5 px-3 text-xs"
                                            >
                                                <Eye size={12} /> Detail
                                            </Link>
                                        </td>
                                    </tr>
                                )) : (
                                    <tr>
                                        <td colSpan={7} className="px-5 py-12 text-center text-sm text-text-secondary">
                                            Tidak ada pengajuan ditemukan.
                                        </td>
                                    </tr>
                                )}
                            </tbody>
                        </table>
                    </div>

                    {pengajuan && <Pagination pagination={pengajuan} />}
                </div>
            </div>
        </AppLayout>
    );
}
