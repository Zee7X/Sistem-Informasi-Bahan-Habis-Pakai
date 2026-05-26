import { Head, Link, useForm, router, usePage } from '@inertiajs/react';
import AppLayout from '@/Layouts/AppLayout';
import { ArrowLeft, CheckCircle, XCircle, Package } from 'lucide-react';
import { useState } from 'react';
import Swal from 'sweetalert2';
import 'sweetalert2/dist/sweetalert2.min.css';

function StatusChip({ status }) {
    const map = {
        pending_review: { label: 'Pending Review', cls: 'chip-warning' },
        approved:       { label: 'Approved',        cls: 'chip-violet' },
        completed:      { label: 'Completed',       cls: 'chip-success' },
        rejected:       { label: 'Ditolak',         cls: 'chip-error' },
    };
    const s = map[status] ?? { label: status, cls: 'chip-neutral' };
    return <span className={`chip ${s.cls}`}>{s.label}</span>;
}

export default function Show({ pengajuan }) {
    const { auth } = usePage().props;
    const role = auth?.user?.role;
    const backUrl = role === 'ketua_jurusan' ? '/kjur/transaksi' : '/admin/pengajuan';
    const [showRejectForm, setShowRejectForm] = useState(false);
    const { data, setData, post, processing } = useForm({ reject_reason: '' });

    const handleApprove = () => {
        Swal.fire({
            title: 'Setujui Pengajuan BHP?',
            text: `Apakah Anda yakin ingin menyetujui pengajuan ${pengajuan.kode_pengajuan}?`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#7c3aed',
            cancelButtonColor: '#6b7280',
            confirmButtonText: 'Ya, Setujui',
            cancelButtonText: 'Batal',
            background: '#ffffff',
            color: '#1f2937',
            customClass: {
                popup: 'rounded-xl shadow-2xl border border-gray-200'
            }
        }).then((result) => {
            if (result.isConfirmed) {
                router.post(`/admin/pengajuan/${pengajuan.id}/approve`, {}, {
                    onSuccess: () => {
                        Swal.fire({
                            title: 'Berhasil disetujui!',
                            text: 'Pengajuan BHP kini berstatus Approved.',
                            icon: 'success',
                            background: '#ffffff',
                            color: '#1f2937',
                            confirmButtonColor: '#7c3aed',
                            timer: 2000,
                            timerProgressBar: true
                        });
                    },
                    onError: () => {
                        Swal.fire({
                            title: 'Gagal!',
                            text: 'Terjadi kesalahan saat memproses persetujuan.',
                            icon: 'error',
                            background: '#ffffff',
                            color: '#1f2937',
                            confirmButtonColor: '#ef4444'
                        });
                    }
                });
            }
        });
    };

    const handleComplete = () => {
        Swal.fire({
            title: 'Tandai Selesai & Serahkan?',
            text: 'Stok fisik bahan di laboratorium akan dikurangi secara otomatis saat transaksi selesai.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#10b981',
            cancelButtonColor: '#6b7280',
            confirmButtonText: 'Ya, Selesai!',
            cancelButtonText: 'Batal',
            background: '#ffffff',
            color: '#1f2937',
            customClass: {
                popup: 'rounded-xl shadow-2xl border border-gray-200'
            }
        }).then((result) => {
            if (result.isConfirmed) {
                router.post(`/admin/pengajuan/${pengajuan.id}/complete`, {}, {
                    onSuccess: () => {
                        Swal.fire({
                            title: 'Transaksi Selesai!',
                            text: 'Bahan telah diserahkan fisik dan stok berhasil dikurangi.',
                            icon: 'success',
                            background: '#ffffff',
                            color: '#1f2937',
                            confirmButtonColor: '#10b981',
                            timer: 2500,
                            timerProgressBar: true
                        });
                    },
                    onError: (errors) => {
                        const msg = Object.values(errors).join('\n') || 'Gagal menyelesaikan pengajuan.';
                        Swal.fire({
                            title: 'Gagal Menyelesaikan!',
                            text: msg,
                            icon: 'error',
                            background: '#ffffff',
                            color: '#1f2937',
                            confirmButtonColor: '#ef4444'
                        });
                    }
                });
            }
        });
    };

    const handleReject = (e) => {
        e.preventDefault();
        Swal.fire({
            title: 'Tolak Pengajuan BHP?',
            text: 'Apakah Anda yakin ingin menolak permohonan bahan praktikum ini?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            cancelButtonColor: '#6b7280',
            confirmButtonText: 'Ya, Tolak!',
            cancelButtonText: 'Batal',
            background: '#ffffff',
            color: '#1f2937',
            customClass: {
                popup: 'rounded-xl shadow-2xl border border-gray-200'
            }
        }).then((result) => {
            if (result.isConfirmed) {
                post(`/admin/pengajuan/${pengajuan.id}/reject`, {
                    onSuccess: () => {
                        setShowRejectForm(false);
                        Swal.fire({
                            title: 'Ditolak!',
                            text: 'Pengajuan BHP telah ditolak.',
                            icon: 'info',
                            background: '#ffffff',
                            color: '#1f2937',
                            confirmButtonColor: '#ef4444',
                            timer: 2000,
                            timerProgressBar: true
                        });
                    },
                    onError: () => {
                        Swal.fire({
                            title: 'Gagal!',
                            text: 'Gagal memproses penolakan.',
                            icon: 'error',
                            background: '#ffffff',
                            color: '#1f2937',
                            confirmButtonColor: '#ef4444'
                        });
                    }
                });
            }
        });
    };

    return (
        <AppLayout title={`Pengajuan ${pengajuan.kode_pengajuan}`}>
            <Head title={`Pengajuan ${pengajuan.kode_pengajuan}`} />
            <div className="p-5 max-w-3xl space-y-4">
                <div className="flex items-center gap-3">
                    <Link href={backUrl} className="btn-ghost btn-sm">
                        <ArrowLeft size={13} /> Kembali
                    </Link>
                    <div className="flex-1" />
                    {role === 'admin' && pengajuan.status === 'pending_review' && (
                        <>
                            <button onClick={() => setShowRejectForm(s => !s)} className="btn-danger btn-sm">
                                <XCircle size={13} /> Tolak
                            </button>
                            <button onClick={handleApprove} disabled={processing} className="btn-primary btn-sm">
                                <CheckCircle size={13} /> Setujui
                            </button>
                        </>
                    )}
                    {role === 'admin' && pengajuan.status === 'approved' && (
                        <button onClick={handleComplete} disabled={processing} className="btn-primary btn-sm">
                            <Package size={13} /> Tandai Selesai
                        </button>
                    )}
                </div>

                {showRejectForm && (
                    <div className="card-surface p-4 border border-error/20 rounded-md">
                        <form onSubmit={handleReject} className="space-y-3">
                            <label className="text-xs font-medium text-text-secondary">Alasan Penolakan *</label>
                            <textarea
                                value={data.reject_reason}
                                onChange={e => setData('reject_reason', e.target.value)}
                                rows={3} required
                                className="input h-auto py-2 resize-none"
                                placeholder="Jelaskan alasan penolakan..."
                            />
                            <div className="flex gap-2">
                                <button type="button" onClick={() => setShowRejectForm(false)} className="btn-ghost btn-sm">Batal</button>
                                <button type="submit" disabled={processing} className="btn-danger btn-sm">Konfirmasi Tolak</button>
                            </div>
                        </form>
                    </div>
                )}

                <div className="card overflow-hidden">
                    <div className="flex items-center justify-between px-5 py-3.5 border-b border-border bg-dark-surface/40">
                        <div>
                            <p className="identifier">{pengajuan.kode_pengajuan}</p>
                            <p className="text-xs text-text-secondary mt-0.5">
                                {pengajuan.jenis === 'modul' ? pengajuan.modul?.nama_modul : 'Mandiri'}
                            </p>
                        </div>
                        <StatusChip status={pengajuan.status} />
                    </div>

                    <div className="grid grid-cols-2 gap-px bg-border">
                        {[
                            { label: 'Mahasiswa', value: pengajuan.user?.name },
                            { label: 'NIM', value: pengajuan.user?.nim ?? '-' },
                            { label: 'Mata Kuliah', value: pengajuan.mata_kuliah },
                            { label: 'Kelas', value: pengajuan.kelas ?? '-' },
                            { label: 'Kelompok', value: pengajuan.kelompok ?? '-' },
                            { label: 'Jumlah Anggota', value: pengajuan.jumlah_anggota ? `${pengajuan.jumlah_anggota} orang` : '-' },
                            { label: 'Tanggal Pakai', value: pengajuan.tanggal_pakai },
                            { label: 'Jenis Pengajuan', value: pengajuan.jenis },
                        ].map(({ label, value }) => (
                            <div key={label} className="bg-dark-card px-5 py-3">
                                <p className="text-2xs text-text-secondary uppercase tracking-wider mb-0.5">{label}</p>
                                <p className="text-sm text-text-primary">{value}</p>
                            </div>
                        ))}
                    </div>

                    <div className="border-t border-border">
                        <p className="section-header border-b border-border">Daftar Bahan yang Diminta</p>
                        <table className="w-full">
                            <thead>
                                <tr className="border-b border-border">
                                    <th className="section-header text-left py-2">Nama Bahan</th>
                                    <th className="section-header text-right py-2">Jumlah</th>
                                    <th className="section-header text-right py-2">Satuan</th>
                                </tr>
                            </thead>
                            <tbody className="divide-y divide-border/40">
                                {pengajuan.items?.map(item => (
                                    <tr key={item.id}>
                                        <td className="px-4 py-2.5 text-sm text-text-primary">{item.nama_bahan_snapshot}</td>
                                        <td className="px-4 py-2.5 text-sm font-mono text-right text-text-primary">{item.jumlah}</td>
                                        <td className="px-4 py-2.5 text-sm text-text-secondary text-right">{item.satuan_snapshot}</td>
                                    </tr>
                                ))}
                            </tbody>
                        </table>
                    </div>

                    {pengajuan.reject_reason && (
                        <div className="border-t border-border px-5 py-3 bg-error/5">
                            <p className="text-2xs text-error/70 uppercase tracking-wider mb-1">Alasan Penolakan</p>
                            <p className="text-sm text-text-primary">{pengajuan.reject_reason}</p>
                        </div>
                    )}
                    {pengajuan.keterangan && (
                        <div className="border-t border-border px-5 py-3">
                            <p className="text-2xs text-text-secondary uppercase tracking-wider mb-1">Keterangan</p>
                            <p className="text-sm text-text-primary">{pengajuan.keterangan}</p>
                        </div>
                    )}
                </div>
            </div>
        </AppLayout>
    );
}
