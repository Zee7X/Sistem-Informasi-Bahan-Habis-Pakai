import { Head, useForm, Link, router } from '@inertiajs/react';
import AppLayout from '@/Layouts/AppLayout';
import Pagination from '@/Components/Pagination';
import SearchableSelect from '@/Components/SearchableSelect';
import { Plus, X, Search, Calendar, Database, Sparkles, ClipboardList } from 'lucide-react';
import { useState } from 'react';

const jenisMap = {
    rusak:        'Rusak',
    kadaluarsa:   'Kadaluarsa',
    hilang:       'Hilang',
    koreksi_lain: 'Koreksi Lain',
};

const chipColorMap = {
    rusak:        'bg-error/10 text-error border-error/20',
    kadaluarsa:   'bg-warning/10 text-warning border-warning/20',
    hilang:       'bg-text-secondary/10 text-text-secondary border-border',
    koreksi_lain: 'bg-violet/10 text-violet border-violet/20',
};

function StatusBadge({ type }) {
    const label = jenisMap[type] ?? type;
    const cls = chipColorMap[type] ?? 'bg-text-secondary/10 text-text-secondary border-border';
    return (
        <span className={`inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-semibold border ${cls}`}>
            <span className="w-1 h-1 rounded-full bg-current" />
            {label}
        </span>
    );
}

export default function Index({ stockOpname, bahan, filters }) {
    const [showModal, setShowModal] = useState(false);
    const [search, setSearch] = useState(filters?.search ?? '');
    const { data, setData, post, processing, errors, reset } = useForm({
        bahan_id: '', stok_sesuai: '', alasan: '', jenis_penyesuaian: 'rusak',
    });

    const submit = (e) => {
        e.preventDefault();
        post('/admin/stock-opname', { onSuccess: () => { reset(); setShowModal(false); } });
    };

    const handleSearch = (e) => {
        e.preventDefault();
        router.get('/admin/stock-opname', { search }, { preserveState: true, replace: true });
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

    const selectedBahan = bahan?.find(b => String(b.id) === String(data.bahan_id));

    return (
        <AppLayout title="Stock Opname">
            <Head title="Stock Opname" />
            <div className="p-5 space-y-5 max-w-7xl mx-auto">

                {/* Header Actions */}
                <div className="card p-4 flex flex-wrap gap-4 items-center justify-between">
                    <form onSubmit={handleSearch} className="relative flex-1 min-w-[280px] max-w-sm">
                        <Search size={16} className="absolute left-3 top-1/2 -translate-y-1/2 text-text-secondary" />
                        <input
                            value={search}
                            onChange={e => setSearch(e.target.value)}
                            placeholder="Cari nama bahan..."
                            className="input pl-10 py-2 w-full text-sm"
                        />
                    </form>
                    <button onClick={() => setShowModal(true)} className="btn-primary inline-flex items-center gap-1.5 py-2 px-4">
                        <Plus size={16} /> Catat Koreksi Stok
                    </button>
                </div>

                {/* Table card */}
                <div className="card overflow-hidden">
                    <div className="flex items-center gap-2 px-5 py-3.5 border-b border-border bg-dark-surface/30">
                        <ClipboardList size={16} className="text-violet" />
                        <h2 className="text-sm font-semibold text-text-primary">Daftar Penyesuaian Stok (Stock Opname)</h2>
                    </div>

                    <div className="overflow-x-auto">
                        <table className="w-full">
                            <thead>
                                <tr className="border-b border-border bg-dark-surface/50">
                                    <th className="section-header text-center py-3 px-5 w-12">No.</th>
                                    <th className="section-header text-left py-3 px-5">Tanggal</th>
                                    <th className="section-header text-left py-3 px-5">Bahan</th>
                                    <th className="section-header text-right py-3 px-5 hidden md:table-cell">Stok Sebelum</th>
                                    <th className="section-header text-right py-3 px-5 hidden md:table-cell">Stok Sesuai</th>
                                    <th className="section-header text-right py-3 px-5">Selisih</th>
                                    <th className="section-header text-center py-3 px-5 hidden lg:table-cell">Jenis Masalah</th>
                                </tr>
                            </thead>
                            <tbody className="divide-y divide-border/40">
                                {stockOpname?.data?.length > 0 ? stockOpname.data.map((op, idx) => (
                                    <tr key={op.id} className="hover:bg-dark-surface/30 transition-colors">
                                        <td className="px-5 py-3.5 text-center text-sm text-text-secondary">
                                            {(stockOpname.meta?.from ?? stockOpname.from ?? 1) + idx}
                                        </td>
                                        <td className="px-5 py-3.5 text-sm text-text-secondary">
                                            <div className="flex items-center gap-1.5">
                                                <Calendar size={13} className="text-text-secondary/60" />
                                                <span>{formatTanggal(op.created_at)}</span>
                                            </div>
                                        </td>
                                        <td className="px-5 py-3.5 text-sm text-text-primary">
                                            <div className="font-semibold">{op.bahan?.nama_bahan}</div>
                                            {op.bahan?.spesifikasi && (
                                                <div className="text-2xs text-text-secondary mt-0.5">{op.bahan.spesifikasi}</div>
                                            )}
                                        </td>
                                        <td className="px-5 py-3.5 text-right text-sm text-text-secondary hidden md:table-cell font-mono">{op.stok_sebelum}</td>
                                        <td className="px-5 py-3.5 text-right text-sm text-text-secondary hidden md:table-cell font-mono">{op.stok_sesuai}</td>
                                        <td className="px-5 py-3.5 text-right text-sm font-bold font-mono">
                                            <span className={op.selisih < 0 ? 'text-error' : 'text-success'}>
                                                {op.selisih > 0 ? '+' : ''}{op.selisih}
                                            </span>
                                        </td>
                                        <td className="px-5 py-3.5 text-center hidden lg:table-cell">
                                            <StatusBadge type={op.jenis_penyesuaian} />
                                        </td>
                                    </tr>
                                )) : (
                                    <tr>
                                        <td colSpan={7} className="px-5 py-12 text-center text-sm text-text-secondary">
                                            {filters?.search ? `Tidak ada hasil untuk "${filters.search}".` : 'Belum ada riwayat stock opname.'}
                                        </td>
                                    </tr>
                                )}
                            </tbody>
                        </table>
                    </div>

                    <Pagination pagination={stockOpname} />
                </div>
            </div>

            {/* Create Modal */}
            {showModal && (
                <div className="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm">
                    <div className="card-surface w-full max-w-md shadow-modal rounded-lg overflow-hidden border border-border">
                        <div className="flex items-center justify-between px-5 py-3.5 border-b border-border bg-dark-surface/30">
                            <h3 className="text-sm font-semibold text-text-primary">Koreksi Stok (Stock Opname)</h3>
                            <button onClick={() => setShowModal(false)} className="btn-ghost btn-sm px-1.5"><X size={14} /></button>
                        </div>
                        <form onSubmit={submit} className="p-5 space-y-3">
                             <div>
                                 <label className="block text-xs text-text-secondary mb-1 font-semibold">Bahan *</label>
                                 <SearchableSelect
                                     options={bahan}
                                     value={data.bahan_id}
                                     onChange={val => setData('bahan_id', val)}
                                     placeholder="Pilih bahan..."
                                     renderExtra={b => `Stok: ${b.stok}`}
                                     required
                                 />
                             </div>
                            {selectedBahan && (
                                <div className="bg-dark-surface/50 border border-border/80 rounded px-3 py-2 text-xs text-text-secondary flex items-center gap-1.5">
                                    <Database size={13} className="text-violet" />
                                    <span>Stok tercatat sistem saat ini: <strong className="text-text-primary font-bold">{selectedBahan.stok} {selectedBahan.satuan?.nama}</strong></span>
                                </div>
                            )}
                            <div>
                                <label className="block text-xs text-text-secondary mb-1 font-semibold">Stok Fisik Aktual *</label>
                                <div className="relative">
                                    <input
                                        type="number"
                                        min="0"
                                        value={data.stok_sesuai}
                                        onChange={e => setData('stok_sesuai', e.target.value)}
                                        required
                                        className={`input ${selectedBahan?.satuan?.nama ? 'pr-12' : ''}`}
                                        placeholder="Jumlah stok setelah penghitungan fisik"
                                    />
                                    {selectedBahan?.satuan?.nama && (
                                        <span className="absolute right-2.5 top-1/2 -translate-y-1/2 text-xs font-semibold text-text-secondary bg-dark-surface px-1.5 py-0.5 rounded border border-border">
                                            {selectedBahan.satuan.nama}
                                        </span>
                                    )}
                                </div>
                                {errors.stok_sesuai && <p className="mt-1 text-xs text-error">{errors.stok_sesuai}</p>}
                            </div>
                            <div>
                                <label className="block text-xs text-text-secondary mb-1 font-semibold">Jenis Penyesuaian *</label>
                                <select value={data.jenis_penyesuaian} onChange={e => setData('jenis_penyesuaian', e.target.value)} required className="input">
                                    <option value="rusak">Rusak</option>
                                    <option value="kadaluarsa">Kadaluarsa</option>
                                    <option value="hilang">Hilang</option>
                                    <option value="koreksi_lain">Koreksi Lain</option>
                                </select>
                            </div>
                            <div>
                                <label className="block text-xs text-text-secondary mb-1 font-semibold">Alasan *</label>
                                <textarea rows={3} value={data.alasan} onChange={e => setData('alasan', e.target.value)} required minLength={10}
                                    className="input h-auto py-2 resize-none" placeholder="Jelaskan alasan penyesuaian stok..." />
                                {errors.alasan && <p className="mt-1 text-xs text-error">{errors.alasan}</p>}
                            </div>
                            <div className="flex gap-2 pt-2 border-t border-border mt-4">
                                <button type="button" onClick={() => setShowModal(false)} className="btn-secondary flex-1">Batal</button>
                                <button type="submit" disabled={processing} className="btn-primary flex-1">Simpan Koreksi</button>
                            </div>
                        </form>
                    </div>
                </div>
            )}
        </AppLayout>
    );
}
