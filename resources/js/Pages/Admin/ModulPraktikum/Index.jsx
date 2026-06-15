import { Head, Link, useForm, router } from '@inertiajs/react';
import AppLayout from '@/Layouts/AppLayout';
import Pagination from '@/Components/Pagination';
import { Plus, X, Pencil, Trash2, BookOpen, Search } from 'lucide-react';
import { useState } from 'react';
import Swal from 'sweetalert2';
import 'sweetalert2/dist/sweetalert2.min.css';

export default function Index({ moduls, filters }) {
    const [search, setSearch] = useState(filters?.search ?? '');

    const handleDelete = (m) => {
        Swal.fire({
            title: 'Hapus Modul Praktikum?',
            html: `Apakah Anda yakin ingin menghapus modul <strong>"${m.nama_modul}"</strong>?<br><small class="text-gray-500">Semua item bahan dalam modul ini akan ikut terhapus.</small>`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            cancelButtonColor: '#6b7280',
            confirmButtonText: 'Ya, Hapus!',
            cancelButtonText: 'Batal',
            background: '#ffffff',
            color: '#1f2937',
            customClass: {
                popup: 'rounded-xl shadow-2xl border border-gray-200'
            }
        }).then((result) => {
            if (result.isConfirmed) {
                router.delete(`/admin/modul-praktikum/${m.id}`, {
                    onSuccess: () => {
                        Swal.fire({
                            title: 'Dihapus!',
                            text: 'Modul praktikum telah berhasil dihapus.',
                            icon: 'success',
                            background: '#ffffff',
                            color: '#1f2937',
                            confirmButtonColor: '#7c3aed',
                            timer: 2000
                        });
                    }
                });
            }
        });
    };
    const handleSearch = (e) => {
        e.preventDefault();
        router.get('/admin/modul-praktikum', { search }, { preserveState: true, replace: true });
    };

    return (
        <AppLayout title="Modul Praktikum">
            <Head title="Modul Praktikum" />
            <div className="p-5 space-y-5 max-w-7xl mx-auto">
                {/* Search & Action Header */}
                <div className="card p-4 flex flex-wrap gap-4 items-center justify-between">
                    <form onSubmit={handleSearch} className="relative flex-1 min-w-[280px] max-w-sm">
                        <Search size={16} className="absolute left-3 top-1/2 -translate-y-1/2 text-text-secondary" />
                        <input
                            value={search}
                            onChange={e => setSearch(e.target.value)}
                            placeholder="Cari modul..."
                            className="input pl-10 py-2 w-full text-sm"
                        />
                    </form>
                    <Link href="/admin/modul-praktikum/create" className="btn-primary inline-flex items-center gap-1.5 py-2 px-4">
                        <Plus size={16} /> Tambah Modul
                    </Link>
                </div>
                <div className="card overflow-hidden">
                    <div className="flex items-center gap-2 px-5 py-3.5 border-b border-border bg-dark-surface/30">
                        <BookOpen size={16} className="text-violet" />
                        <h2 className="text-sm font-semibold text-text-primary">Daftar Modul Praktikum</h2>
                    </div>

                    <div className="overflow-x-auto">
                        <table className="w-full">
                            <thead>
                                <tr className="border-b border-border bg-dark-surface/50">
                                    <th className="section-header text-center py-3 px-5 w-12">No.</th>
                                    <th className="section-header text-left py-3 px-5">Kode</th>
                                    <th className="section-header text-left py-3 px-5">Nama Modul</th>
                                    <th className="section-header text-center py-3 px-5 hidden md:table-cell">Bahan</th>
                                    <th className="section-header text-center py-3 px-5">Status</th>
                                    <th className="section-header text-right py-3 px-5">Aksi</th>
                                </tr>
                            </thead>
                            <tbody className="divide-y divide-border/40">
                                {moduls?.data?.map((m, idx) => (
                                    <tr key={m.id} className="hover:bg-dark-surface/30 transition-colors">
                                        <td className="px-5 py-3.5 text-center text-sm text-text-secondary">
                                            {(moduls.meta?.from ?? moduls.from ?? 1) + idx}
                                        </td>
                                        <td className="px-5 py-3.5"><span className="identifier">{m.kode_modul}</span></td>
                                        <td className="px-5 py-3.5">
                                            <p className="text-sm text-text-primary">{m.nama_modul}</p>
                                            {m.deskripsi && <p className="text-xs text-text-secondary truncate max-w-xs">{m.deskripsi}</p>}
                                        </td>
                                        <td className="px-5 py-3.5 text-center text-sm text-text-secondary hidden md:table-cell">
                                            {m.items?.length ?? 0} item
                                        </td>
                                        <td className="px-5 py-3.5 text-center">
                                            <span className={`chip ${m.is_active ? 'chip-success' : 'chip-neutral'}`}>
                                                {m.is_active ? 'Aktif' : 'Nonaktif'}
                                            </span>
                                        </td>
                                        <td className="px-5 py-3.5 text-right">
                                            <div className="flex gap-1 justify-end">
                                                <Link href={`/admin/modul-praktikum/${m.id}/edit`} className="btn-ghost btn-sm px-1.5 flex items-center justify-center"><Pencil size={13} /></Link>
                                                <button onClick={() => handleDelete(m)} className="btn-danger btn-sm px-1.5"><Trash2 size={13} /></button>
                                            </div>
                                        </td>
                                    </tr>
                                ))}
                                {!moduls?.data?.length && (
                                    <tr><td colSpan={6} className="px-5 py-12 text-center text-sm text-text-secondary">Belum ada modul praktikum.</td></tr>
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
