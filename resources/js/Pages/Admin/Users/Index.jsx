import { Head, useForm, router } from '@inertiajs/react';
import AppLayout from '@/Layouts/AppLayout';
import Pagination from '@/Components/Pagination';
import { Plus, X, Pencil, Trash2, Search } from 'lucide-react';
import { useState } from 'react';

const roleMap = {
    admin: { label: 'Admin', cls: 'chip-violet' },
    mahasiswa: { label: 'Mahasiswa', cls: 'chip-neutral' },
    ketua_jurusan: { label: 'Ketua Jurusan', cls: 'chip-success' },
};

export default function Index({ users, filters }) {
    const [showCreate, setShowCreate] = useState(false);
    const [editData, setEditData] = useState(null);
    const [search, setSearch] = useState(filters?.search ?? '');

    const createForm = useForm({
        name: '', email: '', password: '', password_confirmation: '',
        role: 'mahasiswa', nim: '', kelas: '', program_studi: '', angkatan: '', no_telp: '',
    });
    const editForm = useForm({
        name: '', email: '', password: '', role: '', nim: '', kelas: '', program_studi: '', angkatan: '',
    });

    const openEdit = (u) => {
        setEditData(u);
        editForm.setData({ name: u.name, email: u.email, password: '', role: u.role, nim: u.nim ?? '', kelas: u.kelas ?? '', program_studi: u.program_studi ?? '', angkatan: u.angkatan ?? '' });
    };

    const submitCreate = (e) => {
        e.preventDefault();
        createForm.post('/admin/users', { onSuccess: () => { createForm.reset(); setShowCreate(false); } });
    };
    const submitEdit = (e) => {
        e.preventDefault();
        editForm.put(`/admin/users/${editData.id}`, { onSuccess: () => setEditData(null) });
    };
    const handleDelete = (u) => {
        if (confirm(`Hapus user "${u.name}"?`)) router.delete(`/admin/users/${u.id}`);
    };
    const handleSearch = (e) => {
        e.preventDefault();
        router.get('/admin/users', { search }, { preserveState: true, replace: true });
    };

    const UserForm = ({ form, onSubmit, onCancel, isCreate }) => (
        <form onSubmit={onSubmit} className="p-5 space-y-3">
            <div className="grid grid-cols-2 gap-3">
                <div className="col-span-2">
                    <label className="block text-xs text-text-secondary mb-1">Nama Lengkap *</label>
                    <input type="text" value={form.data.name} onChange={e => form.setData('name', e.target.value)} required className="input" />
                    {form.errors.name && <p className="mt-1 text-xs text-error">{form.errors.name}</p>}
                </div>
                <div>
                    <label className="block text-xs text-text-secondary mb-1">Email *</label>
                    <input type="email" value={form.data.email} onChange={e => form.setData('email', e.target.value)} required className="input" />
                    {form.errors.email && <p className="mt-1 text-xs text-error">{form.errors.email}</p>}
                </div>
                <div>
                    <label className="block text-xs text-text-secondary mb-1">Role *</label>
                    <select value={form.data.role} onChange={e => form.setData('role', e.target.value)} className="input">
                        <option value="mahasiswa">Mahasiswa</option>
                        <option value="admin">Admin</option>
                        <option value="ketua_jurusan">Ketua Jurusan</option>
                    </select>
                </div>
                <div>
                    <label className="block text-xs text-text-secondary mb-1">{isCreate ? 'Password *' : 'Password Baru'}</label>
                    <input type="password" value={form.data.password} onChange={e => form.setData('password', e.target.value)} required={isCreate} className="input" placeholder={isCreate ? '' : 'Kosongkan jika tidak diubah'} />
                    {form.errors.password && <p className="mt-1 text-xs text-error">{form.errors.password}</p>}
                </div>
                <div>
                    <label className="block text-xs text-text-secondary mb-1">NIM</label>
                    <input type="text" value={form.data.nim} onChange={e => form.setData('nim', e.target.value)} className="input" />
                </div>
                <div>
                    <label className="block text-xs text-text-secondary mb-1">Kelas</label>
                    <input type="text" value={form.data.kelas} onChange={e => form.setData('kelas', e.target.value)} className="input" placeholder="TI-22-A" />
                </div>
                <div>
                    <label className="block text-xs text-text-secondary mb-1">Program Studi</label>
                    <input type="text" value={form.data.program_studi} onChange={e => form.setData('program_studi', e.target.value)} className="input" />
                </div>
                <div>
                    <label className="block text-xs text-text-secondary mb-1">Angkatan</label>
                    <input type="text" value={form.data.angkatan} onChange={e => form.setData('angkatan', e.target.value)} className="input" placeholder="2022" />
                </div>
            </div>
            <div className="flex gap-2 pt-1">
                <button type="button" onClick={onCancel} className="btn-secondary flex-1">Batal</button>
                <button type="submit" disabled={form.processing} className="btn-primary flex-1">Simpan</button>
            </div>
        </form>
    );

    return (
        <AppLayout title="Kelola Users">
            <Head title="Kelola Users" />
            <div className="p-5 space-y-4">
                <div className="flex gap-2 items-center">
                    <form onSubmit={handleSearch} className="relative flex-1 max-w-72">
                        <Search size={13} className="absolute left-2.5 top-1/2 -translate-y-1/2 text-text-secondary" />
                        <input value={search} onChange={e => setSearch(e.target.value)} placeholder="Cari nama, email, NIM..." className="input pl-8" />
                    </form>
                    <div className="flex-1" />
                    <button onClick={() => setShowCreate(true)} className="btn-primary"><Plus size={14} /> Tambah User</button>
                </div>
                <div className="card overflow-hidden">
                    <div className="overflow-x-auto">
                        <table className="w-full">
                            <thead>
                                <tr className="border-b border-border">
                                    <th className="section-header text-left py-2.5">Nama</th>
                                    <th className="section-header text-left py-2.5 hidden md:table-cell">Email</th>
                                    <th className="section-header text-left py-2.5 hidden lg:table-cell">NIM / Kelas</th>
                                    <th className="section-header text-left py-2.5">Role</th>
                                    <th className="section-header text-right py-2.5">Aksi</th>
                                </tr>
                            </thead>
                            <tbody className="divide-y divide-border/40">
                                {users?.data?.map(u => (
                                    <tr key={u.id} className="hover:bg-dark-surface/50 transition-colors">
                                        <td className="px-4 py-2.5 text-sm text-text-primary font-medium">{u.name}</td>
                                        <td className="px-4 py-2.5 text-sm text-text-secondary hidden md:table-cell">{u.email}</td>
                                        <td className="px-4 py-2.5 hidden lg:table-cell">
                                            <p className="identifier">{u.nim ?? '-'}</p>
                                            <p className="text-xs text-text-secondary">{u.kelas ?? '-'}</p>
                                        </td>
                                        <td className="px-4 py-2.5">
                                            <span className={`chip ${roleMap[u.role]?.cls ?? 'chip-neutral'}`}>{roleMap[u.role]?.label ?? u.role}</span>
                                        </td>
                                        <td className="px-4 py-2.5 text-right">
                                            <div className="flex gap-1 justify-end">
                                                <button onClick={() => openEdit(u)} className="btn-ghost btn-sm px-1.5"><Pencil size={13} /></button>
                                                <button onClick={() => handleDelete(u)} className="btn-danger btn-sm px-1.5"><Trash2 size={13} /></button>
                                            </div>
                                        </td>
                                    </tr>
                                ))}
                                {!users?.data?.length && (
                                    <tr><td colSpan={5} className="px-4 py-12 text-center text-sm text-text-secondary">Tidak ada user ditemukan.</td></tr>
                                )}
                            </tbody>
                        </table>
                    </div>
                    <Pagination pagination={users} />
                </div>
            </div>

            {showCreate && (
                <div className="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm">
                    <div className="card-surface w-full max-w-lg shadow-modal rounded-lg overflow-hidden max-h-[90vh] overflow-y-auto">
                        <div className="flex items-center justify-between px-5 py-3.5 border-b border-border sticky top-0 bg-dark-surface z-10">
                            <h3 className="text-sm font-semibold text-text-primary">Tambah User</h3>
                            <button onClick={() => setShowCreate(false)} className="btn-ghost btn-sm px-1.5"><X size={14} /></button>
                        </div>
                        <UserForm form={createForm} onSubmit={submitCreate} onCancel={() => setShowCreate(false)} isCreate />
                    </div>
                </div>
            )}
            {editData && (
                <div className="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm">
                    <div className="card-surface w-full max-w-lg shadow-modal rounded-lg overflow-hidden max-h-[90vh] overflow-y-auto">
                        <div className="flex items-center justify-between px-5 py-3.5 border-b border-border sticky top-0 bg-dark-surface z-10">
                            <h3 className="text-sm font-semibold text-text-primary">Edit User</h3>
                            <button onClick={() => setEditData(null)} className="btn-ghost btn-sm px-1.5"><X size={14} /></button>
                        </div>
                        <UserForm form={editForm} onSubmit={submitEdit} onCancel={() => setEditData(null)} />
                    </div>
                </div>
            )}
        </AppLayout>
    );
}
