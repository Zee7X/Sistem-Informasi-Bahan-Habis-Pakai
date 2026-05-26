import { Head, useForm, Link, router } from '@inertiajs/react';
import AppLayout from '@/Layouts/AppLayout';
import Pagination from '@/Components/Pagination';
import { Plus, X, Trash2, Search } from 'lucide-react';
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

    return (
        <AppLayout title="Bahan Masuk">
            <Head title="Bahan Masuk" />
            <div className="p-5 space-y-4">
                <div className="flex gap-2 items-center">
                    <form onSubmit={handleSearch} className="relative flex-1 max-w-72">
                        <Search size={13} className="absolute left-2.5 top-1/2 -translate-y-1/2 text-text-secondary" />
                        <input
                            value={search}
                            onChange={e => setSearch(e.target.value)}
                            placeholder="Cari bahan, pemasok, faktur..."
                            className="input pl-8"
                        />
                    </form>
                    <div className="flex-1" />
                    <button onClick={() => setShowModal(true)} className="btn-primary">
                        <Plus size={14} /> Tambah Bahan Masuk
                    </button>
                </div>
                <div className="card overflow-hidden">
                    <div className="overflow-x-auto">
                        <table className="w-full">
                            <thead>
                                <tr className="border-b border-border">
                                    <th className="section-header text-left py-2.5">Tanggal</th>
                                    <th className="section-header text-left py-2.5">Bahan</th>
                                    <th className="section-header text-left py-2.5 hidden md:table-cell">Pemasok</th>
                                    <th className="section-header text-left py-2.5 hidden lg:table-cell">No. Faktur</th>
                                    <th className="section-header text-right py-2.5">Jumlah</th>
                                </tr>
                            </thead>
                            <tbody className="divide-y divide-border/40">
                                {bahanMasuk?.data?.map(m => (
                                    <tr key={m.id} className="hover:bg-dark-surface/50 transition-colors">
                                        <td className="px-4 py-2.5 text-sm text-text-secondary">{m.tanggal_masuk}</td>
                                        <td className="px-4 py-2.5 text-sm text-text-primary">{m.bahan?.nama_bahan}</td>
                                        <td className="px-4 py-2.5 text-sm text-text-secondary hidden md:table-cell">{m.pemasok ?? '-'}</td>
                                        <td className="px-4 py-2.5 hidden lg:table-cell"><span className="identifier">{m.no_faktur ?? '-'}</span></td>
                                        <td className="px-4 py-2.5 text-right font-semibold text-success text-sm">+{m.jumlah}</td>
                                    </tr>
                                ))}
                                {!bahanMasuk?.data?.length && (
                                    <tr><td colSpan={5} className="px-4 py-12 text-center text-sm text-text-secondary">
                                        {filters?.search ? `Tidak ada hasil untuk "${filters.search}".` : 'Belum ada data bahan masuk.'}
                                    </td></tr>
                                )}
                            </tbody>
                        </table>
                    </div>

                    <Pagination pagination={bahanMasuk} />
                </div>
            </div>

            {showModal && (
                <div className="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm">
                    <div className="card-surface w-full max-w-lg shadow-modal rounded-lg overflow-hidden">
                        <div className="flex items-center justify-between px-5 py-3.5 border-b border-border">
                            <h3 className="text-sm font-semibold text-text-primary">Tambah Bahan Masuk</h3>
                            <button onClick={() => setShowModal(false)} className="btn-ghost btn-sm px-1.5"><X size={14} /></button>
                        </div>
                        <form onSubmit={submit} className="p-5 space-y-3">
                            <div className="grid grid-cols-2 gap-3">
                                <div className="col-span-2">
                                    <label className="block text-xs text-text-secondary mb-1">Bahan *</label>
                                    <select value={data.bahan_id} onChange={e => setData('bahan_id', e.target.value)} required className="input">
                                        <option value="">Pilih bahan...</option>
                                        {bahan?.map(b => <option key={b.id} value={b.id}>{b.nama_bahan}</option>)}
                                    </select>
                                    {errors.bahan_id && <p className="mt-1 text-xs text-error">{errors.bahan_id}</p>}
                                </div>
                                <div>
                                    <label className="block text-xs text-text-secondary mb-1">Jumlah *</label>
                                    <input type="number" min="1" value={data.jumlah} onChange={e => setData('jumlah', e.target.value)} required className="input" />
                                    {errors.jumlah && <p className="mt-1 text-xs text-error">{errors.jumlah}</p>}
                                </div>
                                <div>
                                    <label className="block text-xs text-text-secondary mb-1">Tanggal *</label>
                                    <input type="date" value={data.tanggal_masuk} onChange={e => setData('tanggal_masuk', e.target.value)} required className="input" />
                                </div>
                                <div>
                                    <label className="block text-xs text-text-secondary mb-1">Pemasok</label>
                                    <input type="text" value={data.pemasok} onChange={e => setData('pemasok', e.target.value)} className="input" placeholder="PT. Kimia Farma" />
                                </div>
                                <div>
                                    <label className="block text-xs text-text-secondary mb-1">No. Faktur</label>
                                    <input type="text" value={data.no_faktur} onChange={e => setData('no_faktur', e.target.value)} className="input" placeholder="INV-2026-001" />
                                </div>
                                <div className="col-span-2">
                                    <label className="block text-xs text-text-secondary mb-1">Harga Satuan (Rp)</label>
                                    <input type="number" min="0" value={data.harga_satuan} onChange={e => setData('harga_satuan', e.target.value)} className="input" placeholder="0" />
                                </div>
                                <div className="col-span-2">
                                    <label className="block text-xs text-text-secondary mb-1">Keterangan</label>
                                    <textarea rows={2} value={data.keterangan} onChange={e => setData('keterangan', e.target.value)} className="input h-auto py-2 resize-none" />
                                </div>
                            </div>
                            <div className="flex gap-2 pt-1">
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
