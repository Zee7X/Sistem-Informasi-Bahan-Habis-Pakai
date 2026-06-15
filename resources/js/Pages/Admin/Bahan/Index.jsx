import { Head, useForm, router, usePage, Link } from '@inertiajs/react';
import AppLayout from '@/Layouts/AppLayout';
import Pagination from '@/Components/Pagination';
import { Plus, X, Pencil, Trash2, FlaskConical, AlertTriangle, Check, Search } from 'lucide-react';

function StatCard({ label, value, icon: Icon, color = 'violet' }) {
    const colors = {
        violet:  { bg: 'bg-violet/10',  text: 'text-violet',  border: 'border-violet/20' },
        success: { bg: 'bg-success/10', text: 'text-success', border: 'border-success/20' },
        error:   { bg: 'bg-error/10',   text: 'text-error',   border: 'border-error/20' },
    };
    const c = colors[color] || colors.violet;

    return (
        <div className="card p-5 flex flex-col justify-between min-h-[115px] hover:shadow-sm transition-all duration-200">
            <div className="flex justify-between items-start gap-4">
                <div className="min-w-0 flex-1">
                    <p className="text-2xs font-semibold text-text-secondary uppercase tracking-widest leading-none mb-2">{label}</p>
                    <div className="flex items-center gap-2 mt-1">
                        <span className="text-2xl font-bold text-text-primary leading-none tracking-tight">{value ?? 0}</span>
                    </div>
                </div>
                {/* Large Icon Badge */}
                <div className={`w-12 h-12 rounded-xl flex items-center justify-center flex-shrink-0 ${c.bg} ${c.text} border ${c.border} shadow-sm transition-transform hover:scale-105 duration-200`}>
                    <Icon size={22} strokeWidth={2} />
                </div>
            </div>
        </div>
    );
}
import { useState } from 'react';

export default function Index({ bahan, satuan, filters, stats }) {
    const { auth } = usePage().props;
    const role = auth?.user?.role;
    const [showCreate, setShowCreate] = useState(false);
    const [editData, setEditData] = useState(null);
    const [deleteTarget, setDeleteTarget] = useState(null);
    const [search, setSearch] = useState(filters?.search ?? '');

    const createForm = useForm({
        kode_bahan: '', nama_bahan: '', spesifikasi: '', stok: '', satuan_id: '',
        minimal_stok: '', lokasi: '', keterangan: '',
    });
    const editForm = useForm({
        kode_bahan: '', nama_bahan: '', spesifikasi: '', stok: '', satuan_id: '',
        minimal_stok: '', lokasi: '', keterangan: '',
    });

    const openEdit = (b) => {
        setEditData(b);
        editForm.setData({
            kode_bahan: b.kode_bahan, nama_bahan: b.nama_bahan, spesifikasi: b.spesifikasi ?? '',
            stok: b.stok, satuan_id: b.satuan_id, minimal_stok: b.minimal_stok,
            lokasi: b.lokasi ?? '', keterangan: b.keterangan ?? '',
        });
    };

    const submitCreate = (e) => {
        e.preventDefault();
        createForm.post('/admin/bahan', { onSuccess: () => { createForm.reset(); setShowCreate(false); } });
    };

    const submitEdit = (e) => {
        e.preventDefault();
        editForm.put(`/admin/bahan/${editData.id}`, { onSuccess: () => setEditData(null) });
    };

    const handleDelete = () => {
        router.delete(`/admin/bahan/${deleteTarget.id}`, { onSuccess: () => setDeleteTarget(null) });
    };

    const handleSearch = (e) => {
        e.preventDefault();
        router.get('/admin/bahan', { search }, { preserveState: true, replace: true });
    };

    const BahanForm = ({ form, onSubmit, onCancel }) => (
        <form onSubmit={onSubmit} className="p-5 space-y-3">
            <div className="grid grid-cols-2 gap-3">
                <div>
                    <label className="block text-xs text-text-secondary mb-1">Kode Bahan *</label>
                    <input type="text" value={form.data.kode_bahan} onChange={e => form.setData('kode_bahan', e.target.value)} required className="input" placeholder="BHN-001" />
                    {form.errors.kode_bahan && <p className="mt-1 text-xs text-error">{form.errors.kode_bahan}</p>}
                </div>
                <div>
                    <label className="block text-xs text-text-secondary mb-1">Nama Bahan *</label>
                    <input type="text" value={form.data.nama_bahan} onChange={e => form.setData('nama_bahan', e.target.value)} required className="input" />
                    {form.errors.nama_bahan && <p className="mt-1 text-xs text-error">{form.errors.nama_bahan}</p>}
                </div>
                <div className="col-span-2">
                    <label className="block text-xs text-text-secondary mb-1">Spesifikasi</label>
                    <input type="text" value={form.data.spesifikasi} onChange={e => form.setData('spesifikasi', e.target.value)} className="input" />
                </div>
                <div>
                    <label className="block text-xs text-text-secondary mb-1">Satuan *</label>
                    <select value={form.data.satuan_id} onChange={e => form.setData('satuan_id', e.target.value)} required className="input">
                        <option value="">Pilih satuan...</option>
                        {satuan?.map(s => <option key={s.id} value={s.id}>{s.nama}</option>)}
                    </select>
                    {form.errors.satuan_id && <p className="mt-1 text-xs text-error">{form.errors.satuan_id}</p>}
                </div>
                <div>
                    <label className="block text-xs text-text-secondary mb-1">Stok Awal</label>
                    <input type="number" min="0" value={form.data.stok} onChange={e => form.setData('stok', e.target.value)} className="input" />
                </div>
                <div>
                    <label className="block text-xs text-text-secondary mb-1">Minimal Stok</label>
                    <input type="number" min="0" value={form.data.minimal_stok} onChange={e => form.setData('minimal_stok', e.target.value)} className="input" />
                </div>
                <div>
                    <label className="block text-xs text-text-secondary mb-1">Lokasi</label>
                    <input type="text" value={form.data.lokasi} onChange={e => form.setData('lokasi', e.target.value)} className="input" placeholder="Rak A-1" />
                </div>
            </div>
            <div className="flex gap-2 pt-1">
                <button type="button" onClick={onCancel} className="btn-secondary flex-1">Batal</button>
                <button type="submit" disabled={form.processing} className="btn-primary flex-1">Simpan</button>
            </div>
        </form>
    );

    return (
        <AppLayout title="Master Bahan">
            <Head title="Master Bahan" />
            <div className="p-5 space-y-5 max-w-7xl mx-auto">
                {/* Stats */}
                <div className="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <StatCard label="Total Bahan" value={stats?.total} icon={FlaskConical} color="violet" />
                    <StatCard label="Stok Aman" value={stats?.stok_aman} icon={Check} color="success" />
                    <StatCard label="Perlu Restock" value={stats?.perlu_restock} icon={AlertTriangle} color="error" />
                </div>

                {/* Search & Action Header */}
                <div className="card p-4 flex flex-wrap gap-4 items-center justify-between">
                    <form onSubmit={handleSearch} className="relative flex-1 min-w-[280px] max-w-sm">
                        <Search size={16} className="absolute left-3 top-1/2 -translate-y-1/2 text-text-secondary" />
                        <input
                            value={search}
                            onChange={e => setSearch(e.target.value)}
                            placeholder="Cari nama atau kode bahan..."
                            className="input pl-10 py-2 w-full text-sm"
                        />
                    </form>
                    {role === 'admin' && (
                        <button onClick={() => setShowCreate(true)} className="btn-primary inline-flex items-center gap-1.5 py-2 px-4">
                            <Plus size={16} /> Tambah Bahan
                        </button>
                    )}
                </div>

                <div className="card overflow-hidden">
                    <div className="flex items-center gap-2 px-5 py-3.5 border-b border-border bg-dark-surface/30">
                        <FlaskConical size={16} className="text-violet" />
                        <h2 className="text-sm font-semibold text-text-primary">Daftar Master Bahan</h2>
                    </div>

                    <div className="overflow-x-auto">
                        <table className="w-full">
                            <thead>
                                <tr className="border-b border-border bg-dark-surface/50">
                                    <th className="section-header text-center py-3 px-5 w-12">No.</th>
                                    <th className="section-header text-left py-3 px-5">Kode</th>
                                    <th className="section-header text-left py-3 px-5">Nama Bahan</th>
                                    <th className="section-header text-left py-3 px-5 hidden md:table-cell">Satuan</th>
                                    <th className="section-header text-right py-3 px-5">Stok</th>
                                    <th className="section-header text-right py-3 px-5 hidden lg:table-cell">Min. Stok</th>
                                    {role === 'admin' && <th className="section-header text-right py-3 px-5">Aksi</th>}
                                </tr>
                            </thead>
                            <tbody className="divide-y divide-border/40">
                                {bahan?.data?.map((b, idx) => (
                                    <tr key={b.id} className="hover:bg-dark-surface/30 transition-colors">
                                        <td className="px-5 py-3.5 text-center text-sm text-text-secondary">
                                            {(bahan.meta?.from ?? bahan.from ?? 1) + idx}
                                        </td>
                                        <td className="px-5 py-3.5"><span className="identifier">{b.kode_bahan}</span></td>
                                        <td className="px-5 py-3.5">
                                            <p className="text-sm text-text-primary">{b.nama_bahan}</p>
                                            {b.spesifikasi && <p className="text-xs text-text-secondary">{b.spesifikasi}</p>}
                                        </td>
                                        <td className="px-5 py-3.5 text-sm text-text-secondary hidden md:table-cell">{b.satuan?.nama}</td>
                                        <td className="px-5 py-3.5 text-right">
                                            <span className={`text-sm font-semibold ${b.stok <= b.minimal_stok ? 'text-error' : 'text-success'}`}>
                                                {b.stok}
                                            </span>
                                        </td>
                                        <td className="px-5 py-3.5 text-right text-sm text-text-secondary hidden lg:table-cell">{b.minimal_stok}</td>
                                        {role === 'admin' && (
                                            <td className="px-5 py-3.5 text-right">
                                                <div className="flex gap-1 justify-end">
                                                    <button onClick={() => openEdit(b)} className="btn-ghost btn-sm px-1.5"><Pencil size={13} /></button>
                                                    <button onClick={() => setDeleteTarget(b)} className="btn-danger btn-sm px-1.5"><Trash2 size={13} /></button>
                                                </div>
                                            </td>
                                        )}
                                    </tr>
                                ))}
                                {!bahan?.data?.length && (
                                    <tr><td colSpan={role === 'admin' ? 7 : 6} className="px-5 py-12 text-center text-sm text-text-secondary">Belum ada data bahan.</td></tr>
                                )}
                            </tbody>
                        </table>
                    </div>

                    <Pagination pagination={bahan} />
                </div>
            </div>

            {/* Create Modal */}
            {showCreate && (
                <div className="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm">
                    <div className="card-surface w-full max-w-lg shadow-modal rounded-lg overflow-hidden">
                        <div className="flex items-center justify-between px-5 py-3.5 border-b border-border">
                            <h3 className="text-sm font-semibold text-text-primary">Tambah Bahan Baru</h3>
                            <button onClick={() => setShowCreate(false)} className="btn-ghost btn-sm px-1.5"><X size={14} /></button>
                        </div>
                        <BahanForm form={createForm} onSubmit={submitCreate} onCancel={() => setShowCreate(false)} />
                    </div>
                </div>
            )}

            {/* Edit Modal */}
            {editData && (
                <div className="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm">
                    <div className="card-surface w-full max-w-lg shadow-modal rounded-lg overflow-hidden">
                        <div className="flex items-center justify-between px-5 py-3.5 border-b border-border">
                            <h3 className="text-sm font-semibold text-text-primary">Edit Bahan</h3>
                            <button onClick={() => setEditData(null)} className="btn-ghost btn-sm px-1.5"><X size={14} /></button>
                        </div>
                        <BahanForm form={editForm} onSubmit={submitEdit} onCancel={() => setEditData(null)} />
                    </div>
                </div>
            )}

            {/* Delete Confirm */}
            {deleteTarget && (
                <div className="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm">
                    <div className="card-surface w-full max-w-sm shadow-modal rounded-lg p-5 space-y-4">
                        <div className="flex items-center gap-3">
                            <div className="w-9 h-9 bg-error/10 border border-error/20 rounded-md flex items-center justify-center">
                                <AlertTriangle size={16} className="text-error" />
                            </div>
                            <div>
                                <p className="text-sm font-semibold text-text-primary">Hapus Bahan</p>
                                <p className="text-xs text-text-secondary">{deleteTarget.nama_bahan}</p>
                            </div>
                        </div>
                        <p className="text-sm text-text-secondary">Aksi ini tidak dapat dibatalkan. Data bahan akan dihapus permanen.</p>
                        <div className="flex gap-2">
                            <button onClick={() => setDeleteTarget(null)} className="btn-secondary flex-1">Batal</button>
                            <button onClick={handleDelete} className="btn-danger flex-1">Hapus</button>
                        </div>
                    </div>
                </div>
            )}
        </AppLayout>
    );
}
