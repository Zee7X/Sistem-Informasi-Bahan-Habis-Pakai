import { Head, Link, useForm, router } from '@inertiajs/react';
import AppLayout from '@/Layouts/AppLayout';
import { ArrowLeft, Plus, Trash2, Save, BookOpen, AlertCircle } from 'lucide-react';
import { useState } from 'react';

export default function Edit({ modulPraktikum, bahan }) {
    // Form for Modul details
    const detailForm = useForm({
        kode_modul: modulPraktikum.kode_modul || '',
        nama_modul: modulPraktikum.nama_modul || '',
        deskripsi: modulPraktikum.deskripsi || '',
        is_active: modulPraktikum.is_active ?? true,
    });

    // Form for adding a new item
    const itemForm = useForm({
        bahan_id: '',
        jumlah: '',
    });

    const handleDetailSubmit = (e) => {
        e.preventDefault();
        detailForm.put(`/admin/modul-praktikum/${modulPraktikum.id}`);
    };

    const handleAddItemSubmit = (e) => {
        e.preventDefault();
        itemForm.post(`/admin/modul-praktikum/${modulPraktikum.id}/items`, {
            onSuccess: () => itemForm.reset(),
        });
    };

    const handleDeleteItem = (itemId) => {
        if (confirm('Hapus bahan ini dari modul praktikum?')) {
            router.delete(`/admin/modul-praktikum/${modulPraktikum.id}/items/${itemId}`);
        }
    };

    // Filter out materials already in the modul
    const existingBahanIds = modulPraktikum.items?.map(item => item.bahan_id.toString()) || [];
    const availableBahan = bahan.filter(b => !existingBahanIds.includes(b.id.toString()));

    return (
        <AppLayout title={`Edit Modul: ${modulPraktikum.nama_modul}`}>
            <Head title={`Edit Modul: ${modulPraktikum.nama_modul}`} />
            <div className="p-5 max-w-4xl space-y-5 mx-auto">
                <div className="flex items-center gap-3">
                    <Link href="/admin/modul-praktikum" className="btn-secondary btn-sm flex items-center gap-1">
                        <ArrowLeft size={13} /> Kembali
                    </Link>
                </div>

                <div className="grid grid-cols-1 md:grid-cols-3 gap-5">
                    {/* Left: Detail Form */}
                    <div className="md:col-span-1 space-y-4">
                        <div className="card p-4 space-y-3">
                            <h3 className="text-sm font-semibold text-text-primary border-b border-border pb-2">Detail Modul</h3>
                            <form onSubmit={handleDetailSubmit} className="space-y-3">
                                <div>
                                    <label className="block text-[10px] text-text-secondary mb-0.5 uppercase tracking-wider">Kode Modul *</label>
                                    <input
                                        type="text"
                                        value={detailForm.data.kode_modul}
                                        onChange={e => detailForm.setData('kode_modul', e.target.value)}
                                        required
                                        className="input uppercase"
                                    />
                                    {detailForm.errors.kode_modul && <p className="mt-1 text-xs text-error">{detailForm.errors.kode_modul}</p>}
                                </div>
                                <div>
                                    <label className="block text-[10px] text-text-secondary mb-0.5 uppercase tracking-wider">Nama Modul *</label>
                                    <input
                                        type="text"
                                        value={detailForm.data.nama_modul}
                                        onChange={e => detailForm.setData('nama_modul', e.target.value)}
                                        required
                                        className="input"
                                    />
                                    {detailForm.errors.nama_modul && <p className="mt-1 text-xs text-error">{detailForm.errors.nama_modul}</p>}
                                </div>
                                <div>
                                    <label className="block text-[10px] text-text-secondary mb-0.5 uppercase tracking-wider">Deskripsi</label>
                                    <textarea
                                        rows={3}
                                        value={detailForm.data.deskripsi}
                                        onChange={e => detailForm.setData('deskripsi', e.target.value)}
                                        className="input h-auto py-2 resize-none"
                                    />
                                    {detailForm.errors.deskripsi && <p className="mt-1 text-xs text-error">{detailForm.errors.deskripsi}</p>}
                                </div>
                                <div>
                                    <label className="flex items-center gap-2 cursor-pointer">
                                        <input
                                            type="checkbox"
                                            checked={detailForm.data.is_active}
                                            onChange={e => detailForm.setData('is_active', e.target.checked)}
                                            className="accent-violet w-3.5 h-3.5"
                                        />
                                        <span className="text-xs text-text-secondary font-medium">Aktif</span>
                                    </label>
                                </div>
                                <button
                                    type="submit"
                                    disabled={detailForm.processing}
                                    className="btn-primary w-full py-1.5 text-xs font-semibold rounded-md flex items-center justify-center gap-1"
                                >
                                    <Save size={13} /> Perbarui Detail
                                </button>
                            </form>
                        </div>
                    </div>

                    {/* Right: Items Management */}
                    <div className="md:col-span-2 space-y-4">
                        {/* Add Item Form */}
                        <div className="card p-4 space-y-3">
                            <h3 className="text-sm font-semibold text-text-primary border-b border-border pb-2">Tambah Komposisi Bahan</h3>
                            <form onSubmit={handleAddItemSubmit} className="flex flex-wrap items-end gap-3">
                                <div className="flex-1 min-w-[200px]">
                                    <label className="block text-[10px] text-text-secondary mb-1 uppercase tracking-wider">Pilih Bahan BHP *</label>
                                    <select
                                        value={itemForm.data.bahan_id}
                                        onChange={e => itemForm.setData('bahan_id', e.target.value)}
                                        required
                                        className="input"
                                    >
                                        <option value="">Pilih Bahan...</option>
                                        {availableBahan.map(b => (
                                            <option key={b.id} value={b.id}>
                                                {b.nama_bahan} (Stok: {b.stok} {b.satuan?.nama})
                                            </option>
                                        ))}
                                    </select>
                                    {itemForm.errors.bahan_id && <p className="mt-1 text-xs text-error">{itemForm.errors.bahan_id}</p>}
                                </div>
                                <div className="w-28">
                                    <label className="block text-[10px] text-text-secondary mb-1 uppercase tracking-wider">Jumlah *</label>
                                    <input
                                        type="number"
                                        step="0.01"
                                        min="0.01"
                                        value={itemForm.data.jumlah}
                                        onChange={e => itemForm.setData('jumlah', e.target.value)}
                                        required
                                        placeholder="0.00"
                                        className="input font-mono text-right"
                                    />
                                    {itemForm.errors.jumlah && <p className="mt-1 text-xs text-error">{itemForm.errors.jumlah}</p>}
                                </div>
                                <button
                                    type="submit"
                                    disabled={itemForm.processing}
                                    className="btn-primary py-2 px-4 text-xs font-semibold rounded-md flex items-center gap-1 h-[32px] justify-center"
                                >
                                    <Plus size={13} /> Tambahkan
                                </button>
                            </form>
                        </div>

                        {/* List of current items */}
                        <div className="card overflow-hidden">
                            <div className="px-4 py-3 border-b border-border bg-dark-surface/30">
                                <h3 className="text-sm font-semibold text-text-primary">Daftar Bahan Saat Ini</h3>
                            </div>
                            <div className="overflow-x-auto">
                                <table className="w-full">
                                    <thead>
                                        <tr className="border-b border-border bg-dark-bg/20">
                                            <th className="section-header text-left py-2">Nama Bahan</th>
                                            <th className="section-header text-right py-2">Jumlah Kebutuhan</th>
                                            <th className="section-header text-left py-2 pl-4">Satuan</th>
                                            <th className="section-header text-right py-2">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody className="divide-y divide-border/40">
                                        {modulPraktikum.items?.length > 0 ? (
                                            modulPraktikum.items.map(item => (
                                                <tr key={item.id} className="hover:bg-dark-surface/20 transition-colors">
                                                    <td className="px-4 py-2 text-sm text-text-primary font-medium">
                                                        {item.bahan?.nama_bahan ?? 'Bahan Terhapus'}
                                                    </td>
                                                    <td className="px-4 py-2 text-sm font-mono text-right text-text-primary">
                                                        {item.jumlah}
                                                    </td>
                                                    <td className="px-4 py-2 text-sm text-text-secondary pl-4">
                                                        {item.bahan?.satuan?.nama ?? ''}
                                                    </td>
                                                    <td className="px-4 py-2 text-right">
                                                        <button
                                                            onClick={() => handleDeleteItem(item.id)}
                                                            className="btn-ghost btn-sm text-error/85 hover:text-error hover:bg-error/10 px-1.5"
                                                        >
                                                            <Trash2 size={13} />
                                                        </button>
                                                    </td>
                                                </tr>
                                            ))
                                        ) : (
                                            <tr>
                                                <td colSpan={4} className="px-4 py-8 text-center text-xs text-text-secondary flex items-center justify-center gap-1.5">
                                                    <AlertCircle size={13} /> Belum ada komposisi bahan dalam modul ini.
                                                </td>
                                            </tr>
                                        )}
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </AppLayout>
    );
}
