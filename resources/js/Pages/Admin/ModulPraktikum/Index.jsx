import { Head, Link, useForm, router } from '@inertiajs/react';
import AppLayout from '@/Layouts/AppLayout';
import Pagination from '@/Components/Pagination';
import { Plus, X, Pencil, Trash2, BookOpen, Search } from 'lucide-react';
import { useState } from 'react';

export default function Index({ moduls, filters }) {
    const [search, setSearch] = useState(filters?.search ?? '');

    const handleDelete = (m) => {
        if (confirm(`Hapus modul "${m.nama_modul}"?`)) router.delete(`/admin/modul-praktikum/${m.id}`);
    };
    const handleSearch = (e) => {
        e.preventDefault();
        router.get('/admin/modul-praktikum', { search }, { preserveState: true, replace: true });
    };

    return (
        <AppLayout title="Modul Praktikum">
            <Head title="Modul Praktikum" />
            <div className="p-5 space-y-4">
                <div className="flex gap-2 items-center">
                    <form onSubmit={handleSearch} className="relative flex-1 max-w-64">
                        <Search size={13} className="absolute left-2.5 top-1/2 -translate-y-1/2 text-text-secondary" />
                        <input value={search} onChange={e => setSearch(e.target.value)} placeholder="Cari modul..." className="input pl-8" />
                    </form>
                    <div className="flex-1" />
                    <Link href="/admin/modul-praktikum/create" className="btn-primary flex items-center gap-1"><Plus size={14} /> Tambah Modul</Link>
                </div>
                <div className="card overflow-hidden">
                    <div className="overflow-x-auto">
                        <table className="w-full">
                            <thead>
                                <tr className="border-b border-border">
                                    <th className="section-header text-left py-2.5">Kode</th>
                                    <th className="section-header text-left py-2.5">Nama Modul</th>
                                    <th className="section-header text-center py-2.5 hidden md:table-cell">Bahan</th>
                                    <th className="section-header text-center py-2.5">Status</th>
                                    <th className="section-header text-right py-2.5">Aksi</th>
                                </tr>
                            </thead>
                            <tbody className="divide-y divide-border/40">
                                {moduls?.data?.map(m => (
                                    <tr key={m.id} className="hover:bg-dark-surface/50 transition-colors">
                                        <td className="px-4 py-2.5"><span className="identifier">{m.kode_modul}</span></td>
                                        <td className="px-4 py-2.5">
                                            <p className="text-sm text-text-primary">{m.nama_modul}</p>
                                            {m.deskripsi && <p className="text-xs text-text-secondary truncate max-w-xs">{m.deskripsi}</p>}
                                        </td>
                                        <td className="px-4 py-2.5 text-center text-sm text-text-secondary hidden md:table-cell">
                                            {m.items?.length ?? 0} item
                                        </td>
                                        <td className="px-4 py-2.5 text-center">
                                            <span className={`chip ${m.is_active ? 'chip-success' : 'chip-neutral'}`}>
                                                {m.is_active ? 'Aktif' : 'Nonaktif'}
                                            </span>
                                        </td>
                                        <td className="px-4 py-2.5 text-right">
                                            <div className="flex gap-1 justify-end">
                                                <Link href={`/admin/modul-praktikum/${m.id}/edit`} className="btn-ghost btn-sm px-1.5 flex items-center justify-center"><Pencil size={13} /></Link>
                                                <button onClick={() => handleDelete(m)} className="btn-danger btn-sm px-1.5"><Trash2 size={13} /></button>
                                            </div>
                                        </td>
                                    </tr>
                                ))}
                                {!moduls?.data?.length && (
                                    <tr><td colSpan={5} className="px-4 py-12 text-center text-sm text-text-secondary">Belum ada modul praktikum.</td></tr>
                                )}
                            </tbody>
                        </table>
                    </div>

                    <Pagination pagination={moduls} />
                </div>
            </div>
        </AppLayout>
    );
}
