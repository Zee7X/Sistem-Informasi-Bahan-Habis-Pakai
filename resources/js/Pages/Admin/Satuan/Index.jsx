import { Head, useForm, router, Link } from '@inertiajs/react';
import AppLayout from '@/Layouts/AppLayout';
import Pagination from '@/Components/Pagination';
import { Plus, X, Pencil, Trash2, Search } from 'lucide-react';
import { useState } from 'react';

export default function Index({ satuan, filters }) {
    const [showCreate, setShowCreate] = useState(false);
    const [editData, setEditData] = useState(null);
    const [search, setSearch] = useState(filters?.search ?? '');
    const createForm = useForm({ nama: '', keterangan: '' });
    const editForm = useForm({ nama: '', keterangan: '' });

    const submitCreate = (e) => {
        e.preventDefault();
        createForm.post('/admin/satuan', { onSuccess: () => { createForm.reset(); setShowCreate(false); } });
    };
    const openEdit = (s) => {
        setEditData(s);
        editForm.setData({ nama: s.nama, keterangan: s.keterangan ?? '' });
    };
    const submitEdit = (e) => {
        e.preventDefault();
        editForm.put(`/admin/satuan/${editData.id}`, { onSuccess: () => setEditData(null) });
    };
    const handleDelete = (s) => {
        if (confirm(`Hapus satuan "${s.nama}"?`)) {
            router.delete(`/admin/satuan/${s.id}`);
        }
    };
    const handleSearch = (e) => {
        e.preventDefault();
        router.get('/admin/satuan', { search }, { preserveState: true, replace: true });
    };

    const ModalForm = ({ form, onSubmit, onCancel, title }) => (
        <div className="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm">
            <div className="card-surface w-full max-w-sm shadow-modal rounded-lg overflow-hidden">
                <div className="flex items-center justify-between px-5 py-3.5 border-b border-border">
                    <h3 className="text-sm font-semibold text-text-primary">{title}</h3>
                    <button onClick={onCancel} className="btn-ghost btn-sm px-1.5"><X size={14} /></button>
                </div>
                <form onSubmit={onSubmit} className="p-5 space-y-3">
                    <div>
                        <label className="block text-xs text-text-secondary mb-1">Nama Satuan *</label>
                        <input type="text" value={form.data.nama} onChange={e => form.setData('nama', e.target.value)} required className="input" placeholder="ml, gram, pcs..." />
                        {form.errors.nama && <p className="mt-1 text-xs text-error">{form.errors.nama}</p>}
                    </div>
                    <div>
                        <label className="block text-xs text-text-secondary mb-1">Keterangan</label>
                        <input type="text" value={form.data.keterangan} onChange={e => form.setData('keterangan', e.target.value)} className="input" />
                    </div>
                    <div className="flex gap-2 pt-1">
                        <button type="button" onClick={onCancel} className="btn-secondary flex-1">Batal</button>
                        <button type="submit" disabled={form.processing} className="btn-primary flex-1">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    );

    return (
        <AppLayout title="Satuan">
            <Head title="Satuan" />
            <div className="p-5 space-y-4">
                <div className="flex gap-2 items-center">
                    <form onSubmit={handleSearch} className="relative flex-1 max-w-72">
                        <Search size={13} className="absolute left-2.5 top-1/2 -translate-y-1/2 text-text-secondary" />
                        <input
                            value={search}
                            onChange={e => setSearch(e.target.value)}
                            placeholder="Cari nama satuan..."
                            className="input pl-8"
                        />
                    </form>
                    <div className="flex-1" />
                    <button onClick={() => setShowCreate(true)} className="btn-primary">
                        <Plus size={14} /> Tambah Satuan
                    </button>
                </div>
                <div className="card overflow-hidden">
                    <table className="w-full">
                        <thead>
                            <tr className="border-b border-border">
                                <th className="section-header text-left py-2.5">Nama Satuan</th>
                                <th className="section-header text-left py-2.5 hidden md:table-cell">Keterangan</th>
                                <th className="section-header text-right py-2.5">Aksi</th>
                            </tr>
                        </thead>
                        <tbody className="divide-y divide-border/40">
                            {satuan?.data?.map(s => (
                                <tr key={s.id} className="hover:bg-dark-surface/50 transition-colors">
                                    <td className="px-4 py-2.5 text-sm font-medium text-text-primary">{s.nama}</td>
                                    <td className="px-4 py-2.5 text-sm text-text-secondary hidden md:table-cell">{s.keterangan ?? '-'}</td>
                                    <td className="px-4 py-2.5 text-right">
                                        <div className="flex gap-1 justify-end">
                                            <button onClick={() => openEdit(s)} className="btn-ghost btn-sm px-1.5"><Pencil size={13} /></button>
                                            <button onClick={() => handleDelete(s)} className="btn-danger btn-sm px-1.5"><Trash2 size={13} /></button>
                                        </div>
                                    </td>
                                </tr>
                            ))}
                            {!satuan?.data?.length && (
                                <tr><td colSpan={3} className="px-4 py-12 text-center text-sm text-text-secondary">
                                    {filters?.search ? `Tidak ada hasil untuk "${filters.search}".` : 'Belum ada satuan.'}
                                </td></tr>
                            )}
                        </tbody>
                    </table>

                    <Pagination pagination={satuan} />
                </div>
            </div>
            {showCreate && <ModalForm form={createForm} onSubmit={submitCreate} onCancel={() => setShowCreate(false)} title="Tambah Satuan" />}
            {editData && <ModalForm form={editForm} onSubmit={submitEdit} onCancel={() => setEditData(null)} title="Edit Satuan" />}
        </AppLayout>
    );
}
