import { Head, Link, useForm } from '@inertiajs/react';
import AppLayout from '@/Layouts/AppLayout';
import { ArrowLeft, Plus, Trash2 } from 'lucide-react';
import { useState, useEffect } from 'react';

export default function Create({ moduls, bahan }) {
    const { data, setData, post, processing, errors } = useForm({
        jenis: 'modul',
        modul_id: '',
        mata_kuliah: '',
        kelas: '',
        kelompok: '',
        jumlah_anggota: '',
        tanggal_pakai: '',
        keterangan: '',
        items: [{ bahan_id: '', jumlah: '' }],
    });

    const selectedModul = moduls?.find(m => String(m.id) === String(data.modul_id));

    const addItem = () => setData('items', [...data.items, { bahan_id: '', jumlah: '' }]);
    const removeItem = (i) => setData('items', data.items.filter((_, idx) => idx !== i));
    const updateItem = (i, field, value) => {
        const updated = data.items.map((item, idx) => idx === i ? { ...item, [field]: value } : item);
        setData('items', updated);
    };

    // Auto-populate items when modul is selected
    useEffect(() => {
        if (data.jenis === 'modul' && selectedModul) {
            setData('items', selectedModul.items?.map(item => ({
                bahan_id: item.bahan_id,
                jumlah: item.jumlah,
            })) ?? []);
        }
    }, [data.modul_id, data.jenis]);

    const submit = (e) => {
        e.preventDefault();
        post('/mahasiswa/pengajuan');
    };

    const today = new Date().toISOString().split('T')[0];

    return (
        <AppLayout title="Pengajuan BHP Baru">
            <Head title="Pengajuan BHP Baru" />
            <div className="p-5 max-w-2xl">
                <div className="flex items-center gap-3 mb-5">
                    <Link href="/mahasiswa/pengajuan" className="btn-ghost btn-sm">
                        <ArrowLeft size={13} /> Kembali
                    </Link>
                </div>

                <form onSubmit={submit} className="space-y-5">
                    {/* Jenis Pengajuan */}
                    <div className="card p-5 space-y-4">
                        <h2 className="text-sm font-semibold text-text-primary">Jenis Pengajuan</h2>
                        <div className="flex gap-3">
                            {[{ value: 'modul', label: 'Berdasarkan Modul' }, { value: 'mandiri', label: 'Mandiri (Pilih Sendiri)' }].map(opt => (
                                <label key={opt.value} className={`flex-1 flex items-center gap-2.5 p-3 rounded-md border cursor-pointer transition-colors ${data.jenis === opt.value ? 'border-violet bg-violet/10' : 'border-border hover:border-text-secondary'}`}>
                                    <input type="radio" name="jenis" value={opt.value} checked={data.jenis === opt.value} onChange={e => setData('jenis', e.target.value)} className="accent-violet" />
                                    <span className="text-sm text-text-primary">{opt.label}</span>
                                </label>
                            ))}
                        </div>

                        {/* Modul Selector */}
                        {data.jenis === 'modul' && (
                            <div>
                                <label className="block text-xs text-text-secondary mb-1">Pilih Modul *</label>
                                <select value={data.modul_id} onChange={e => setData('modul_id', e.target.value)} required className="input">
                                    <option value="">Pilih modul praktikum...</option>
                                    {moduls?.map(m => <option key={m.id} value={m.id}>{m.kode_modul} — {m.nama_modul}</option>)}
                                </select>
                                {errors.modul_id && <p className="mt-1 text-xs text-error">{errors.modul_id}</p>}
                            </div>
                        )}
                    </div>

                    {/* Info Kegiatan */}
                    <div className="card p-5 space-y-3">
                        <h2 className="text-sm font-semibold text-text-primary">Informasi Kegiatan</h2>
                        <div className="grid grid-cols-2 gap-3">
                            <div className="col-span-2">
                                <label className="block text-xs text-text-secondary mb-1">Mata Kuliah</label>
                                <input type="text" value={data.mata_kuliah} onChange={e => setData('mata_kuliah', e.target.value)} className="input" placeholder="Kimia Organik" />
                            </div>
                            <div>
                                <label className="block text-xs text-text-secondary mb-1">Kelas</label>
                                <input type="text" value={data.kelas} onChange={e => setData('kelas', e.target.value)} className="input" placeholder="TI-22-A" />
                            </div>
                            <div>
                                <label className="block text-xs text-text-secondary mb-1">Kelompok / Nama Tim</label>
                                <input type="text" value={data.kelompok} onChange={e => setData('kelompok', e.target.value)} className="input" placeholder="Kelompok 3" />
                            </div>
                            <div>
                                <label className="block text-xs text-text-secondary mb-1">
                                    Jumlah Anggota
                                    <span className="ml-1 text-text-secondary/60">(opsional)</span>
                                </label>
                                <input
                                    type="number"
                                    min="1"
                                    max="20"
                                    value={data.jumlah_anggota}
                                    onChange={e => setData('jumlah_anggota', e.target.value)}
                                    className="input"
                                    placeholder="mis. 4"
                                />
                                {errors.jumlah_anggota && <p className="mt-1 text-xs text-error">{errors.jumlah_anggota}</p>}
                            </div>
                            <div className="col-span-2">
                                <label className="block text-xs text-text-secondary mb-1">Tanggal Pakai *</label>
                                <input type="date" value={data.tanggal_pakai} min={today} onChange={e => setData('tanggal_pakai', e.target.value)} required className="input" />
                                {errors.tanggal_pakai && <p className="mt-1 text-xs text-error">{errors.tanggal_pakai}</p>}
                            </div>
                            <div className="col-span-2">
                                <label className="block text-xs text-text-secondary mb-1">Keterangan</label>
                                <textarea rows={2} value={data.keterangan} onChange={e => setData('keterangan', e.target.value)} className="input h-auto py-2 resize-none" placeholder="Keterangan tambahan (opsional)" />
                            </div>
                        </div>
                    </div>

                    {/* Daftar Bahan */}
                    <div className="card overflow-hidden">
                        <div className="flex items-center justify-between px-5 py-3 border-b border-border">
                            <h2 className="text-sm font-semibold text-text-primary">Daftar Bahan</h2>
                            {data.jenis === 'mandiri' && (
                                <button type="button" onClick={addItem} className="btn-ghost btn-sm">
                                    <Plus size={13} /> Tambah Bahan
                                </button>
                            )}
                        </div>
                        <div className="p-3 space-y-2">
                            {data.jenis === 'modul' && selectedModul ? (
                                /* Read-only modul items */
                                selectedModul.items?.length > 0 ? selectedModul.items.map((item, i) => (
                                    <div key={i} className="flex items-center gap-2 p-2 bg-dark-bg/40 rounded">
                                        <span className="flex-1 text-sm text-text-primary">{item.bahan?.nama_bahan}</span>
                                        <span className="font-mono text-sm text-text-secondary">{item.jumlah}</span>
                                        <span className="text-xs text-text-secondary">{item.bahan?.satuan?.nama}</span>
                                    </div>
                                )) : (
                                    <p className="text-sm text-text-secondary text-center py-4">Pilih modul untuk melihat daftar bahan.</p>
                                )
                            ) : data.jenis === 'mandiri' ? (
    /* Editable mandiri items */
    data.items.map((item, i) => {
        const selectedBahan = bahan?.find(b => String(b.id) === String(item.bahan_id));
        const isOverStock = selectedBahan && parseFloat(item.jumlah) > parseFloat(selectedBahan.stok);

        return (
            <div key={i} className="flex flex-col gap-1">
                <div className="flex gap-2 items-start">
                    <div className="flex-1">
                        <select value={item.bahan_id} onChange={e => updateItem(i, 'bahan_id', e.target.value)} required className="input">
                            <option value="">Pilih bahan...</option>
                            {bahan?.map(b => <option key={b.id} value={b.id}>{b.nama_bahan} (Stok: {b.stok} {b.satuan?.nama})</option>)}
                        </select>
                    </div>
                    <div className="w-24">
                        <input
                            type="number"
                            min="0.01"
                            step="0.01"
                            value={item.jumlah}
                            onChange={e => updateItem(i, 'jumlah', e.target.value)}
                            required
                            placeholder="Jml"
                            className={`input ${isOverStock ? 'border-error bg-error/10' : ''}`}
                        />
                    </div>
                    {data.items.length > 1 && (
                        <button type="button" onClick={() => removeItem(i)} className="btn-ghost btn-sm px-1.5 mt-0.5">
                            <Trash2 size={13} className="text-error" />
                        </button>
                    )}
                </div>
                {isOverStock && (
                    <p className="text-[10px] text-error font-semibold">
                        Jumlah melebihi stok tersedia ({selectedBahan.stok} {selectedBahan.satuan?.nama})
                    </p>
                )}
            </div>
        );
    })
                            ) : (
                                <p className="text-sm text-text-secondary text-center py-4">Pilih jenis pengajuan di atas.</p>
                            )}
                        </div>
                        {errors.items && <p className="px-5 pb-3 text-xs text-error">{errors.items}</p>}
                    </div>

                    <div className="flex gap-2">
                        <Link href="/mahasiswa/pengajuan" className="btn-secondary flex-1 justify-center">Batal</Link>
                        <button type="submit" disabled={processing} className="btn-primary flex-1 justify-center">
                            {processing ? 'Mengirim...' : 'Kirim Pengajuan'}
                        </button>
                    </div>
                </form>
            </div>
        </AppLayout>
    );
}
