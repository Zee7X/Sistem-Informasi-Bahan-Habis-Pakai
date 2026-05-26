import { Head, Link, router } from '@inertiajs/react';
import AppLayout from '@/Layouts/AppLayout';
import Pagination from '@/Components/Pagination';
import { CheckCircle, AlertTriangle, Eye, HelpCircle, Search } from 'lucide-react';
import { useState } from 'react';

function StatusChip({ status }) {
    const map = {
        pending:  { label: 'Pending Approval', cls: 'chip-warning' },
        approved: { label: 'Approved',         cls: 'chip-success' },
    };
    const s = map[status] ?? { label: status, cls: 'chip-neutral' };
    return <span className={`chip ${s.cls}`}>{s.label}</span>;
}

export default function Index({ masuk, filters }) {
    const [approveTarget, setApproveTarget] = useState(null);
    const [search, setSearch] = useState(filters?.search ?? '');

    const handleApprove = () => {
        if (!approveTarget) return;
        router.post(`/kjur/bahan-masuk/${approveTarget.id}/approve`, {}, {
            onSuccess: () => setApproveTarget(null)
        });
    };

    const handleSearch = (e) => {
        e.preventDefault();
        router.get('/kjur/bahan-masuk', { search, status: filters?.status }, { preserveState: true, replace: true });
    };

    const filterStatus = (status) => {
        router.get('/kjur/bahan-masuk', { status, search }, { preserveState: true, replace: true });
    };

    const formatRupiah = (number) => {
        if (!number) return '-';
        return new Intl.NumberFormat('id-ID', {
            style: 'currency',
            currency: 'IDR',
            minimumFractionDigits: 0
        }).format(number);
    };

    const statuses = [
        { value: '', label: 'Semua' },
        { value: 'pending', label: 'Pending' },
        { value: 'approved', label: 'Approved' },
    ];

    return (
        <AppLayout title="Approval Belanja Bahan">
            <Head title="Approval Belanja Bahan" />
            
            <div className="p-5 space-y-4">
                {/* Search & Filters */}
                <div className="flex flex-wrap gap-2 items-center">
                    <form onSubmit={handleSearch} className="relative flex-1 min-w-48 max-w-72">
                        <Search size={13} className="absolute left-2.5 top-1/2 -translate-y-1/2 text-text-secondary" />
                        <input
                            value={search}
                            onChange={e => setSearch(e.target.value)}
                            placeholder="Cari bahan, pemasok, faktur..."
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

                {/* Table */}
                <div className="card overflow-hidden">
                    <div className="overflow-x-auto">
                        <table className="w-full">
                            <thead>
                                <tr className="border-b border-border">
                                    <th className="section-header text-left py-2.5">Tanggal</th>
                                    <th className="section-header text-left py-2.5">Bahan</th>
                                    <th className="section-header text-left py-2.5 hidden md:table-cell">Pemasok / Faktur</th>
                                    <th className="section-header text-right py-2.5">Harga Satuan</th>
                                    <th className="section-header text-right py-2.5">Jumlah</th>
                                    <th className="section-header text-left py-2.5 pl-6">Status</th>
                                    <th className="section-header text-right py-2.5">Aksi</th>
                                </tr>
                            </thead>
                            <tbody className="divide-y divide-border/40">
                                {masuk?.data?.length > 0 ? masuk.data.map(m => (
                                    <tr key={m.id} className="hover:bg-dark-surface/50 transition-colors">
                                        <td className="px-4 py-2.5 text-sm text-text-secondary">
                                            {m.tanggal_masuk ? new Date(m.tanggal_masuk).toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' }) : '-'}
                                        </td>
                                        <td className="px-4 py-2.5">
                                            <p className="text-sm text-text-primary font-medium">{m.bahan?.nama_bahan}</p>
                                            <p className="text-2xs text-text-secondary font-mono">{m.bahan?.kode_bahan}</p>
                                        </td>
                                        <td className="px-4 py-2.5 hidden md:table-cell">
                                            <p className="text-sm text-text-primary">{m.pemasok ?? '-'}</p>
                                            {m.no_faktur && <span className="identifier">{m.no_faktur}</span>}
                                        </td>
                                        <td className="px-4 py-2.5 text-right text-sm text-text-primary font-mono">
                                            {formatRupiah(m.harga_satuan)}
                                        </td>
                                        <td className="px-4 py-2.5 text-right">
                                            <span className="text-sm font-semibold text-success font-mono">
                                                +{m.jumlah} {m.bahan?.satuan?.nama}
                                            </span>
                                        </td>
                                        <td className="px-4 py-2.5 pl-6"><StatusChip status={m.status_kjur} /></td>
                                        <td className="px-4 py-2.5 text-right">
                                            {m.status_kjur === 'pending' ? (
                                                <button
                                                    onClick={() => setApproveTarget(m)}
                                                    className="btn-primary btn-sm inline-flex items-center gap-1"
                                                >
                                                    <CheckCircle size={12} /> Setujui
                                                </button>
                                            ) : (
                                                <span className="text-xs text-text-secondary italic">Selesai</span>
                                            )}
                                        </td>
                                    </tr>
                                )) : (
                                    <tr>
                                        <td colSpan={7} className="px-4 py-12 text-center text-sm text-text-secondary">
                                            {filters?.search ? `Tidak ada hasil untuk "${filters.search}".` : 'Tidak ada data belanja bahan masuk.'}
                                        </td>
                                    </tr>
                                )}
                            </tbody>
                        </table>
                    </div>

                    <Pagination pagination={masuk} />
                </div>
            </div>

            {/* Approval Modal */}
            {approveTarget && (
                <div className="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm">
                    <div className="card-surface w-full max-w-sm shadow-modal rounded-lg p-5 space-y-4">
                        <div className="flex items-center gap-3">
                            <div className="w-9 h-9 bg-success/10 border border-success/20 rounded-md flex items-center justify-center">
                                <CheckCircle size={16} className="text-success" />
                            </div>
                            <div>
                                <p className="text-sm font-semibold text-text-primary">Persetujuan Belanja</p>
                                <p className="text-xs text-text-secondary">{approveTarget.bahan?.nama_bahan}</p>
                            </div>
                        </div>
                        <p className="text-sm text-text-secondary">
                            Apakah Anda yakin ingin menyetujui penambahan stok sebanyak <strong className="text-text-primary">{approveTarget.jumlah} {approveTarget.bahan?.satuan?.nama}</strong> dengan total harga <strong className="text-text-primary">{formatRupiah(approveTarget.jumlah * approveTarget.harga_satuan)}</strong>?
                        </p>
                        <div className="flex gap-2">
                            <button onClick={() => setApproveTarget(null)} className="btn-secondary flex-1">Batal</button>
                            <button onClick={handleApprove} className="btn-primary flex-1">Setujui</button>
                        </div>
                    </div>
                </div>
            )}
        </AppLayout>
    );
}
