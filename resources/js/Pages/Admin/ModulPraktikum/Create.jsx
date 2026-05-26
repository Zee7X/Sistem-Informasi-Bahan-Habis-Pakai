import { Head, Link, useForm } from '@inertiajs/react';
import AppLayout from '@/Layouts/AppLayout';
import { ArrowLeft, Plus, Trash2, Save, BookOpen } from 'lucide-react';
import { useState } from 'react';

export default function Create({ bahan }) {
    const { data, setData, post, processing, errors } = useForm({
        kode_modul: '',
        nama_modul: '',
        deskripsi: '',
        is_active: true,
        items: [{ bahan_id: '', jumlah: '' }]
    });

    const addItem = () => {
        setData('items', [...data.items, { bahan_id: '', jumlah: '' }]);
    };

    const removeItem = (index) => {
        if (data.items.length > 1) {
            const newItems = data.items.filter((_, i) => i !== index);
            setData('items', newItems);
        }
    };

    const handleItemChange = (index, field, value) => {
        const newItems = data.items.map((item, i) => {
            if (i === index) {
                return { ...item, [field]: value };
            }
            return item;
        });
        setData('items', newItems);
    };

    const handleSubmit = (e) => {
        e.preventDefault();
        post('/admin/modul-praktikum');
    };

    // Filter out materials already selected, to prevent duplication in other rows
    const getAvailableBahan = (currentIndex) => {
        const selectedIds = data.items
            .map((item, index) => index !== currentIndex ? item.bahan_id : null)
            .filter(Boolean);
        return bahan.filter(b => !selectedIds.includes(b.id.toString()) && !selectedIds.includes(b.id));
    };

    const getSatuan = (bahanId) => {
        const found = bahan.find(b => b.id.toString() === bahanId.toString());
        return found?.satuan?.nama ?? '';
    };

    return (
        <AppLayout title="Buat Modul Praktikum Baru">
            <Head title="Buat Modul Praktikum Baru" />
            <div className="p-5 max-w-4xl space-y-4 mx-auto">
                <div className="flex items-center gap-3">
                    <Link href="/admin/modul-praktikum" className="btn-secondary btn-sm flex items-center gap-1">
                        <ArrowLeft size={13} /> Kembali
                    </Link>
                </div>

                <form onSubmit={handleSubmit} className="space-y-5">
                    {/* Informasi Modul */}
                    <div className="card p-5 space-y-4">
                        <h2 className="text-sm font-semibold text-text-primary flex items-center gap-2 border-b border-border pb-2.5">
                            <BookOpen size={15} className="text-violet" /> Informasi Modul Praktikum
                        </h2>
                        <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label className="block text-xs text-text-secondary mb-1">Kode Modul *</label>
                                <input
                                    type="text"
                                    value={data.kode_modul}
                                    onChange={e => setData('kode_modul', e.target.value)}
                                    required
                                    className="input uppercase"
                                    placeholder="KIMIA-01"
                                />
                                {errors.kode_modul && <p className="mt-1 text-xs text-error">{errors.kode_modul}</p>}
                            </div>
                            <div>
                                <label className="block text-xs text-text-secondary mb-1">Nama Modul *</label>
                                <input
                                    type="text"
                                    value={data.nama_modul}
                                    onChange={e => setData('nama_modul', e.target.value)}
                                    required
                                    className="input"
                                    placeholder="Praktikum Kimia Dasar 1"
                                />
                                {errors.nama_modul && <p className="mt-1 text-xs text-error">{errors.nama_modul}</p>}
                            </div>
                            <div className="md:col-span-2">
                                <label className="block text-xs text-text-secondary mb-1">Deskripsi</label>
                                <textarea
                                    rows={2}
                                    value={data.deskripsi}
                                    onChange={e => setData('deskripsi', e.target.value)}
                                    className="input h-auto py-2 resize-none"
                                    placeholder="Keterangan singkat mengenai modul praktikum ini..."
                                />
                                {errors.deskripsi && <p className="mt-1 text-xs text-error">{errors.deskripsi}</p>}
                            </div>
                            <div className="md:col-span-2">
                                <label className="flex items-center gap-2 cursor-pointer">
                                    <input
                                        type="checkbox"
                                        checked={data.is_active}
                                        onChange={e => setData('is_active', e.target.checked)}
                                        className="accent-violet w-3.5 h-3.5"
                                    />
                                    <span className="text-xs text-text-secondary font-medium">Aktif (Dapat dipilih oleh mahasiswa saat mengajukan BHP)</span>
                                </label>
                            </div>
                        </div>
                    </div>

                    {/* Komposisi Bahan */}
                    <div className="card p-5 space-y-4">
                        <div className="flex items-center justify-between border-b border-border pb-2.5">
                            <h2 className="text-sm font-semibold text-text-primary">Komposisi Bahan Habis Pakai</h2>
                            <button
                                type="button"
                                onClick={addItem}
                                className="btn-secondary btn-sm flex items-center gap-1 text-violet border-violet/20 hover:border-violet/40 bg-violet/5"
                            >
                                <Plus size={13} /> Tambah Bahan
                            </button>
                        </div>

                        <div className="space-y-3">
                            {data.items.map((item, index) => (
                                <div key={index} className="flex flex-wrap items-center gap-3 p-3 bg-dark-surface/40 border border-border/60 rounded-md">
                                    <div className="flex-1 min-w-[200px]">
                                        <label className="block text-[10px] text-text-secondary mb-0.5 uppercase tracking-wider">Pilih Bahan *</label>
                                        <select
                                            value={item.bahan_id}
                                            onChange={e => handleItemChange(index, 'bahan_id', e.target.value)}
                                            required
                                            className="input bg-dark-bg"
                                        >
                                            <option value="">Pilih Bahan...</option>
                                            {getAvailableBahan(index).map(b => (
                                                <option key={b.id} value={b.id}>
                                                    {b.nama_bahan} (Stok: {b.stok} {b.satuan?.nama})
                                                </option>
                                            ))}
                                        </select>
                                    </div>
                                    <div className="w-28">
                                        <label className="block text-[10px] text-text-secondary mb-0.5 uppercase tracking-wider">Jumlah *</label>
                                        <input
                                            type="number"
                                            step="0.01"
                                            min="0.01"
                                            value={item.jumlah}
                                            onChange={e => handleItemChange(index, 'jumlah', e.target.value)}
                                            required
                                            placeholder="0.00"
                                            className="input text-right font-mono"
                                        />
                                    </div>
                                    <div className="w-16 pt-3 text-xs font-semibold text-text-secondary">
                                        {getSatuan(item.bahan_id)}
                                    </div>
                                    <div className="pt-3">
                                        <button
                                            type="button"
                                            onClick={() => removeItem(index)}
                                            disabled={data.items.length === 1}
                                            className="btn-ghost btn-sm text-error/85 hover:text-error hover:bg-error/10 disabled:opacity-30 disabled:hover:bg-transparent"
                                        >
                                            <Trash2 size={14} />
                                        </button>
                                    </div>
                                </div>
                            ))}
                            {errors.items && <p className="text-xs text-error">{errors.items}</p>}
                            {errors && Object.keys(errors).some(k => k.startsWith('items.')) && (
                                <p className="text-xs text-error">Mohon lengkapi semua bahan dan jumlah kebutuhan dengan benar.</p>
                            )}
                        </div>
                    </div>

                    {/* Submit Actions */}
                    <div className="flex gap-3">
                        <Link
                            href="/admin/modul-praktikum"
                            className="btn-secondary flex-1 py-2 text-center text-sm font-semibold rounded-md"
                        >
                            Batal
                        </Link>
                        <button
                            type="submit"
                            disabled={processing}
                            className="btn-primary flex-1 py-2 text-center text-sm font-semibold rounded-md flex items-center justify-center gap-1.5"
                        >
                            <Save size={15} /> Simpan Modul
                        </button>
                    </div>
                </form>
            </div>
        </AppLayout>
    );
}
