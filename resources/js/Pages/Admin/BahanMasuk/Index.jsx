import { Head, useForm, Link, router } from '@inertiajs/react';
import AppLayout from '@/Layouts/AppLayout';
import Pagination from '@/Components/Pagination';
import { Plus, X, Trash2, Search, Calendar, Landmark, ClipboardList, Info } from 'lucide-react';
import { useState } from 'react';

export default function Index({ bahanMasuk, bahan, filters }) {
    const [showModal, setShowModal] = useState(false);
    const [search, setSearch] = useState(filters?.search ?? '');
    const { data, setData, post, processing, errors, reset } = useForm({
        bahan_id: '', jumlah: '', tanggal_masuk: new Date().toISOString().split('T')[0],
        pemasok: '', no_faktur: '', harga_satuan: '', keterangan: '',
    });

    const submit = (e) => {
        e.preventDefault();
        post('/admin/bahan-masuk', { onSuccess: () => { reset(); setShowModal(false); } });
    };

    const handleSearch = (e) => {
        e.preventDefault();
        router.get('/admin/bahan-masuk', { search }, { preserveState: true, replace: true });
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

    return (
        <AppLayout title="Bahan Masuk">
            <Head title="Bahan Masuk" />
            <div className="p-5 space-y-5 max-w-7xl mx-auto">

                {/* Header Actions */}
                <div className="card p-4 flex flex-wrap gap-4 items-center justify-between">
                    <form onSubmit={handleSearch} className="relative flex-1 min-w-[280px] max-w-sm">
                        <Search size={16} className="absolute left-3 top-1/2 -translate-y-1/2 text-text-secondary" />
                        <input
                            value={search}
                            onChange={e => setSearch(e.target.value)}
                            placeholder="Cari bahan, pemasok, faktur..."
                            className="input pl-10 py-2 w-full text-sm"
                        />
                    </form>
                    <button onClick={() => setShowModal(true)} className="btn-primary inline-flex items-center gap-1.5 py-2 px-4">
                        <Plus size={16} /> Tambah Bahan Masuk
                    </button>
                </div>

                {/* Table list */}
                <div className="card overflow-hidden">
                    <div className="flex items-center gap-2 px-5 py-3.5 border-b border-border bg-dark-surface/30">
                        <ClipboardList size={16} className="text-violet" />
                        <h2 className="text-sm font-semibold text-text-primary">Daftar Bahan Masuk & Restock</h2>
                    </div>

                    <div className="overflow-x-auto">
                        <table className="w-full">
                            <thead>
                                <tr className="border-b border-border bg-dark-surface/50">
                                    <th className="section-header text-center py-3 px-5 w-12">No.</th>
                                    <th className="section-header text-left py-3 px-5">Tanggal Masuk</th>
                                    <th className="section-header text-left py-3 px-5">Nama Bahan</th>
                                    <th className="section-header text-left py-3 px-5 hidden md:table-cell">Pemasok</th>
                                    <th className="section-header text-left py-3 px-5 hidden lg:table-cell">No. Faktur</th>
                                    <th className="section-header text-right py-3 px-5">Jumlah</th>
                                </tr>
                            </thead>
                            <tbody className="divide-y divide-border/40">
                                {bahanMasuk?.data?.length > 0 ? bahanMasuk.data.map((m, idx) => (
                                    <tr key={m.id} className="hover:bg-dark-surface/30 transition-colors">
                                        <td className="px-5 py-3.5 text-center text-sm text-text-secondary">
                                            {(bahanMasuk.meta?.from ?? bahanMasuk.from ?? 1) + idx}
                                        </td>
                                        <td className="px-5 py-3.5 text-sm text-text-secondary">
                                            <div className="flex items-center gap-1.5">
                                                <Calendar size={13} className="text-text-secondary/60" />
                                                <span>{formatTanggal(m.tanggal_masuk)}</span>
                                            </div>
                                        </td>
                                        <td className="px-5 py-3.5 text-sm font-semibold text-text-primary">{m.bahan?.nama_bahan}</td>
                                        <td className="px-5 py-3.5 text-sm text-text-secondary hidden md:table-cell">
                                            <div className="flex items-center gap-1.5">
                                                <Landmark size={13} className="text-text-secondary/60" />
                                                <span>{m.pemasok ?? '-'}</span>
                                            </div>
                                        </td>
                                        <td className="px-5 py-3.5 hidden lg:table-cell"><span className="identifier font-mono">{m.no_faktur ?? '-'}</span></td>
                                        <td className="px-5 py-3.5 text-right font-mono font-bold text-success text-sm">+{parseFloat(m.jumlah)}</td>
                                    </tr>
                                )) : (
                                    <tr>
                                        <td colSpan={6} className="px-5 py-12 text-center text-sm text-text-secondary">
                                            {filters?.search ? `Tidak ada hasil untuk "${filters.search}".` : 'Belum ada data bahan masuk.'}
                                        </td>
                                    </tr>
                                )}
                            </tbody>
                        </table>
                    </div>

                    <Pagination pagination={bahanMasuk} />
                </div>
            </div>

            {/* Create Modal */}
            {showModal && (
                <div className="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm">
                    <div className="card-surface w-full max-w-lg shadow-modal rounded-lg overflow-hidden border border-border">
                        <div className="flex items-center justify-between px-5 py-3.5 border-b border-border bg-dark-surface/30">
                            <h3 className="text-sm font-semibold text-text-primary">Tambah Bahan Masuk</h3>
                            <button onClick={() => setShowModal(false)} className="btn-ghost btn-sm px-1.5"><X size={14} /></button>
                        </div>
                        <form onSubmit={submit} className="p-5 space-y-3">
                            <div className="grid grid-cols-2 gap-3">
                                <div className="col-span-2">
                                    <label className="block text-xs text-text-secondary mb-1 font-semibold">Bahan *</label>
                                    <select value={data.bahan_id} onChange={e => setData('bahan_id', e.target.value)} required className="input">
                                        <option value="">Pilih bahan...</option>
                                        {bahan?.map(b => <option key={b.id} value={b.id}>{b.nama_bahan}</option>)}
                                    </select>
                                    {errors.bahan_id && <p className="mt-1 text-xs text-error">{errors.bahan_id}</p>}
                                </div>
                                <div>
                                    <label className="block text-xs text-text-secondary mb-1 font-semibold">Jumlah *</label>
                                    <input type="number" min="1" value={data.jumlah} onChange={e => setData('jumlah', e.target.value)} required className="input" />
                                    {errors.jumlah && <p className="mt-1 text-xs text-error">{errors.jumlah}</p>}
                                </div>
                                <div>
                                    <label className="block text-xs text-text-secondary mb-1 font-semibold">Tanggal *</label>
                                    <input type="date" value={data.tanggal_masuk} onChange={e => setData('tanggal_masuk', e.target.value)} required className="input" />
                                </div>
                                <div>
                                    <label className="block text-xs text-text-secondary mb-1 font-semibold">Pemasok</label>
                                    <input type="text" value={data.pemasok} onChange={e => setData('pemasok', e.target.value)} className="input" placeholder="PT. Kimia Farma" />
                                </div>
                                <div>
                                    <label className="block text-xs text-text-secondary mb-1 font-semibold">No. Faktur</label>
                                    <input type="text" value={data.no_faktur} onChange={e => setData('no_faktur', e.target.value)} className="input" placeholder="INV-2026-001" />
                                </div>
                                <div className="col-span-2">
                                    <label className="block text-xs text-text-secondary mb-1 font-semibold">Harga Satuan (Rp)</label>
                                    <input type="number" min="0" value={data.harga_satuan} onChange={e => setData('harga_satuan', e.target.value)} className="input" placeholder="0" />
                                </div>
                                <div className="col-span-2">
                                    <label className="block text-xs text-text-secondary mb-1 font-semibold">Keterangan</label>
                                    <textarea rows={2} value={data.keterangan} onChange={e => setData('keterangan', e.target.value)} className="input h-auto py-2 resize-none" />
                                </div>
                            </div>
                            <div className="flex gap-2 pt-2 border-t border-border mt-4">
                                <button type="button" onClick={() => setShowModal(false)} className="btn-secondary flex-1">Batal</button>
                                <button type="submit" disabled={processing} className="btn-primary flex-1">Simpan</button>
                            </div>
                        </form>
                    </div>
                </div>
            )}
        </AppLayout>
    );
}
