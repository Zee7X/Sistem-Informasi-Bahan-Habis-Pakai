import { Head, useForm, Link, router } from '@inertiajs/react';
import AppLayout from '@/Layouts/AppLayout';
import Pagination from '@/Components/Pagination';
import { Plus, X, Search } from 'lucide-react';
import { useState } from 'react';

const jenisMap = {
    rusak:        'Rusak',
    kadaluarsa:   'Kadaluarsa',
    hilang:       'Hilang',
    koreksi_lain: 'Koreksi Lain',
};

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

    const selectedBahan = bahan?.find(b => String(b.id) === String(data.bahan_id));

    return (
        <AppLayout title="Stock Opname">
            <Head title="Stock Opname" />
            <div className="p-5 space-y-4">
                <div className="flex gap-2 items-center">
                    <form onSubmit={handleSearch} className="relative flex-1 max-w-72">
                        <Search size={13} className="absolute left-2.5 top-1/2 -translate-y-1/2 text-text-secondary" />
                        <input
                            value={search}
                            onChange={e => setSearch(e.target.value)}
                            placeholder="Cari nama bahan..."
                            className="input pl-8"
                        />
                    </form>
                    <div className="flex-1" />
                    <button onClick={() => setShowModal(true)} className="btn-primary">
                        <Plus size={14} /> Catat Koreksi Stok
                    </button>
                </div>
                <div className="card overflow-hidden">
                    <div className="overflow-x-auto">
                        <table className="w-full">
                            <thead>
                                <tr className="border-b border-border">
                                    <th className="section-header text-left py-2.5">Tanggal</th>
                                    <th className="section-header text-left py-2.5">Bahan</th>
                                    <th className="section-header text-right py-2.5 hidden md:table-cell">Stok Sebelum</th>
                                    <th className="section-header text-right py-2.5 hidden md:table-cell">Stok Sesuai</th>
                                    <th className="section-header text-right py-2.5">Selisih</th>
                                    <th className="section-header text-left py-2.5 hidden lg:table-cell">Jenis</th>
                                </tr>
                            </thead>
                            <tbody className="divide-y divide-border/40">
                                {stockOpname?.data?.map(op => (
                                    <tr key={op.id} className="hover:bg-dark-surface/50 transition-colors">
                                        <td className="px-4 py-2.5 text-sm text-text-secondary">
                                            {new Date(op.created_at).toLocaleDateString('id-ID')}
                                        </td>
                                        <td className="px-4 py-2.5 text-sm text-text-primary">{op.bahan?.nama_bahan}</td>
                                        <td className="px-4 py-2.5 text-right text-sm text-text-secondary hidden md:table-cell">{op.stok_sebelum}</td>
                                        <td className="px-4 py-2.5 text-right text-sm text-text-secondary hidden md:table-cell">{op.stok_sesuai}</td>
                                        <td className="px-4 py-2.5 text-right text-sm font-semibold">
                                            <span className={op.selisih < 0 ? 'text-error' : 'text-success'}>
                                                {op.selisih > 0 ? '+' : ''}{op.selisih}
                                            </span>
                                        </td>
                                        <td className="px-4 py-2.5 hidden lg:table-cell">
                                            <span className="chip chip-neutral capitalize">{jenisMap[op.jenis_penyesuaian] ?? op.jenis_penyesuaian}</span>
                                        </td>
                                    </tr>
                                ))}
                                {!stockOpname?.data?.length && (
                                    <tr><td colSpan={6} className="px-4 py-12 text-center text-sm text-text-secondary">
                                        {filters?.search ? `Tidak ada hasil untuk "${filters.search}".` : 'Belum ada riwayat stock opname.'}
                                    </td></tr>
                                )}
                            </tbody>
                        </table>
                    </div>

                    <Pagination pagination={stockOpname} />
                </div>
            </div>

            {showModal && (
                <div className="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm">
                    <div className="card-surface w-full max-w-md shadow-modal rounded-lg overflow-hidden">
                        <div className="flex items-center justify-between px-5 py-3.5 border-b border-border">
                            <h3 className="text-sm font-semibold text-text-primary">Koreksi Stok (Stock Opname)</h3>
                            <button onClick={() => setShowModal(false)} className="btn-ghost btn-sm px-1.5"><X size={14} /></button>
                        </div>
                        <form onSubmit={submit} className="p-5 space-y-3">
                            <div>
                                <label className="block text-xs text-text-secondary mb-1">Bahan *</label>
                                <select value={data.bahan_id} onChange={e => setData('bahan_id', e.target.value)} required className="input">
                                    <option value="">Pilih bahan...</option>
                                    {bahan?.map(b => (
                                        <option key={b.id} value={b.id}>{b.nama_bahan} — Stok: {b.stok}</option>
                                    ))}
                                </select>
                            </div>
                            {selectedBahan && (
                                <div className="bg-dark-bg/50 border border-border rounded px-3 py-2 text-xs text-text-secondary">
                                    Stok saat ini: <span className="text-text-primary font-semibold">{selectedBahan.stok}</span>
                                </div>
                            )}
                            <div>
                                <label className="block text-xs text-text-secondary mb-1">Stok Fisik Aktual *</label>
                                <input type="number" min="0" value={data.stok_sesuai} onChange={e => setData('stok_sesuai', e.target.value)} required className="input" placeholder="Jumlah stok setelah penghitungan fisik" />
                                {errors.stok_sesuai && <p className="mt-1 text-xs text-error">{errors.stok_sesuai}</p>}
                            </div>
                            <div>
                                <label className="block text-xs text-text-secondary mb-1">Jenis Penyesuaian *</label>
                                <select value={data.jenis_penyesuaian} onChange={e => setData('jenis_penyesuaian', e.target.value)} required className="input">
                                    <option value="rusak">Rusak</option>
                                    <option value="kadaluarsa">Kadaluarsa</option>
                                    <option value="hilang">Hilang</option>
                                    <option value="koreksi_lain">Koreksi Lain</option>
                                </select>
                            </div>
                            <div>
                                <label className="block text-xs text-text-secondary mb-1">Alasan *</label>
                                <textarea rows={3} value={data.alasan} onChange={e => setData('alasan', e.target.value)} required minLength={10}
                                    className="input h-auto py-2 resize-none" placeholder="Jelaskan alasan penyesuaian stok..." />
                                {errors.alasan && <p className="mt-1 text-xs text-error">{errors.alasan}</p>}
                            </div>
                            <div className="flex gap-2 pt-1">
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
