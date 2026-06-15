import { Head, Link, useForm, router, usePage } from '@inertiajs/react';
import AppLayout from '@/Layouts/AppLayout';
import {
    ArrowLeft, CheckCircle, XCircle, Package, User, Hash,
    BookOpen, Calendar, Users, FileText, Check, Ban, CheckSquare, Clock
} from 'lucide-react';
import { useState } from 'react';
import Swal from 'sweetalert2';
import 'sweetalert2/dist/sweetalert2.min.css';

function StatusBadge({ status }) {
    const map = {
        pending_review: { label: 'Menunggu Review', cls: 'bg-warning/10 text-warning border-warning/20' },
        approved:       { label: 'Disetujui (Approved)', cls: 'bg-violet/10 text-violet border-violet/20' },
        completed:      { label: 'Selesai (Completed)', cls: 'bg-success/10 text-success border-success/20' },
        rejected:       { label: 'Ditolak (Rejected)', cls: 'bg-error/10 text-error border-error/20' },
    };
    const s = map[status] ?? { label: status, cls: 'bg-text-secondary/10 text-text-secondary border-border' };
    return (
        <span className={`inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold border ${s.cls}`}>
            <span className="w-1.5 h-1.5 rounded-full bg-current" />
            {s.label}
        </span>
    );
}

export default function Show({ pengajuan }) {
    const { auth } = usePage().props;
    const role = auth?.user?.role;
    const backUrl = role === 'ketua_jurusan' ? '/kjur/transaksi' : '/admin/pengajuan';
    const [showRejectForm, setShowRejectForm] = useState(false);
    const { data, setData, post, processing } = useForm({ reject_reason: '' });

    // Format tanggal
    const formatTanggal = (dateString) => {
        if (!dateString) return '-';
        try {
            const date = new Date(dateString);
            return date.toLocaleDateString('id-ID', { day: '2-digit', month: 'long', year: 'numeric' });
        } catch (e) {
            return dateString;
        }
    };

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
        }).then((result) => {
            if (result.isConfirmed) {
                router.post(`/admin/pengajuan/${pengajuan.id}/approve`, {}, {
                    onSuccess: () => {
                        Swal.fire({
                            title: 'Berhasil disetujui!',
                            text: 'Pengajuan BHP kini berstatus Approved.',
                            icon: 'success',
                            confirmButtonColor: '#7c3aed',
                            timer: 2000,
                            timerProgressBar: true
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
        }).then((result) => {
            if (result.isConfirmed) {
                router.post(`/admin/pengajuan/${pengajuan.id}/complete`, {}, {
                    onSuccess: () => {
                        Swal.fire({
                            title: 'Transaksi Selesai!',
                            text: 'Bahan telah diserahkan fisik dan stok berhasil dikurangi.',
                            icon: 'success',
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
        }).then((result) => {
            if (result.isConfirmed) {
                post(`/admin/pengajuan/${pengajuan.id}/reject`, {
                    onSuccess: () => {
                        setShowRejectForm(false);
                        Swal.fire({
                            title: 'Ditolak!',
                            text: 'Pengajuan BHP telah ditolak.',
                            icon: 'info',
                            confirmButtonColor: '#ef4444',
                            timer: 2000,
                            timerProgressBar: true
                        });
                    }
                });
            }
        });
    };

    return (
        <AppLayout title={`Detail Pengajuan ${pengajuan.kode_pengajuan}`}>
            <Head title={`Pengajuan ${pengajuan.kode_pengajuan}`} />

            <div className="p-6 space-y-6 max-w-7xl mx-auto">

                {/* Top Actions Panel */}
                <div className="flex flex-wrap items-center justify-between gap-4 bg-dark-card p-4 rounded-xl border border-border">
                    <div className="flex items-center gap-3">
                        <Link href={backUrl} className="btn-secondary flex items-center gap-1.5 py-1.5 px-3">
                            <ArrowLeft size={14} /> Kembali
                        </Link>
                        <span className="text-text-secondary">|</span>
                        <div className="flex flex-col">
                            <h1 className="text-lg font-bold text-text-primary leading-tight">{pengajuan.kode_pengajuan}</h1>
                            <p className="text-2xs text-text-secondary mt-0.5">Dibuat pada: {formatTanggal(pengajuan.created_at)}</p>
                        </div>
                    </div>

                    <div className="flex gap-2">
                        {role === 'admin' && pengajuan.status === 'pending_review' && (
                            <>
                                <button
                                    onClick={() => setShowRejectForm(s => !s)}
                                    className="btn-danger flex items-center gap-1.5 px-4 py-2"
                                >
                                    <XCircle size={15} /> Tolak Pengajuan
                                </button>
                                <button
                                    onClick={handleApprove}
                                    disabled={processing}
                                    className="btn-primary flex items-center gap-1.5 px-4 py-2"
                                >
                                    <CheckCircle size={15} /> Setujui Pengajuan
                                </button>
                            </>
                        )}
                        {role === 'admin' && pengajuan.status === 'approved' && (
                            <button
                                onClick={handleComplete}
                                disabled={processing}
                                className="btn-success text-white flex items-center gap-1.5 px-5 py-2.5 rounded-lg font-semibold shadow-sm shadow-success/10"
                            >
                                <Package size={16} /> Serahkan Bahan & Selesai
                            </button>
                        )}
                    </div>
                </div>

                {/* Reject Form Drawer/Box */}
                {showRejectForm && (
                    <div className="card border-error/30 p-5 bg-error/5 animate-in fade-in duration-200">
                        <form onSubmit={handleReject} className="space-y-4">
                            <div className="flex items-start gap-3">
                                <AlertTriangle size={18} className="text-error mt-0.5 flex-shrink-0" />
                                <div className="flex-1">
                                    <h4 className="text-sm font-semibold text-text-primary">Alasan Penolakan Wajib Diisi</h4>
                                    <p className="text-2xs text-text-secondary mt-0.5">Alasan ini akan dikirimkan kepada mahasiswa sebagai pedoman revisi.</p>
                                </div>
                            </div>
                            <textarea
                                value={data.reject_reason}
                                onChange={e => setData('reject_reason', e.target.value)}
                                rows={3}
                                required
                                className="input w-full h-auto py-2.5 resize-none bg-dark-card border-border/80 focus:border-error"
                                placeholder="Tulis alasan penolakan secara jelas (misal: stok NaCl tidak mencukupi, harap kurangi jumlah pengajuan)..."
                            />
                            <div className="flex gap-2 justify-end">
                                <button type="button" onClick={() => setShowRejectForm(false)} className="btn-secondary px-4">Batal</button>
                                <button type="submit" disabled={processing} className="btn-danger px-5">Tolak Sekarang</button>
                            </div>
                        </form>
                    </div>
                )}

                {/* Main Content Layout */}
                <div className="grid grid-cols-1 lg:grid-cols-3 gap-6">

                    {/* Left & Middle Column (2/3) - Info & Items */}
                    <div className="lg:col-span-2 space-y-6">

                        {/* Information Grid Card */}
                        <div className="card overflow-hidden">
                            <div className="px-5 py-4 border-b border-border bg-dark-surface/30 flex justify-between items-center">
                                <h3 className="text-sm font-semibold text-text-primary">Informasi Pengajuan</h3>
                                <StatusBadge status={pengajuan.status} />
                            </div>
                            <div className="grid grid-cols-1 md:grid-cols-2 divide-y md:divide-y-0 md:divide-x divide-border bg-dark-card/20">

                                {/* Col 1 */}
                                <div className="p-5 space-y-4">
                                    <div className="flex gap-3">
                                        <div className="p-2 rounded-lg bg-violet/10 text-violet h-fit mt-0.5"><User size={16} /></div>
                                        <div>
                                            <p className="text-3xs uppercase tracking-widest text-text-secondary font-semibold">Nama Mahasiswa</p>
                                            <p className="text-sm font-semibold text-text-primary mt-0.5">{pengajuan.user?.name}</p>
                                        </div>
                                    </div>
                                    <div className="flex gap-3">
                                        <div className="p-2 rounded-lg bg-violet/10 text-violet h-fit mt-0.5"><Hash size={16} /></div>
                                        <div>
                                            <p className="text-3xs uppercase tracking-widest text-text-secondary font-semibold">NIM</p>
                                            <p className="text-sm font-mono text-text-primary mt-0.5">{pengajuan.user?.nim ?? '-'}</p>
                                        </div>
                                    </div>
                                    <div className="flex gap-3">
                                        <div className="p-2 rounded-lg bg-violet/10 text-violet h-fit mt-0.5"><BookOpen size={16} /></div>
                                        <div>
                                            <p className="text-3xs uppercase tracking-widest text-text-secondary font-semibold">Mata Kuliah</p>
                                            <p className="text-sm text-text-primary mt-0.5">{pengajuan.mata_kuliah ?? '-'}</p>
                                        </div>
                                    </div>
                                    <div className="flex gap-3">
                                        <div className="p-2 rounded-lg bg-violet/10 text-violet h-fit mt-0.5"><FileText size={16} /></div>
                                        <div>
                                            <p className="text-3xs uppercase tracking-widest text-text-secondary font-semibold">Jenis Pengajuan</p>
                                            <p className="text-xs font-semibold uppercase px-2 py-0.5 rounded bg-dark-surface border border-border inline-block mt-1">{pengajuan.jenis}</p>
                                        </div>
                                    </div>
                                </div>

                                {/* Col 2 */}
                                <div className="p-5 space-y-4">
                                    <div className="flex gap-3">
                                        <div className="p-2 rounded-lg bg-violet/10 text-violet h-fit mt-0.5"><Users size={16} /></div>
                                        <div>
                                            <p className="text-3xs uppercase tracking-widest text-text-secondary font-semibold">Kelas / Kelompok</p>
                                            <p className="text-sm text-text-primary mt-0.5">{pengajuan.kelas ?? '-'} / {pengajuan.kelompok ?? '-'}</p>
                                        </div>
                                    </div>
                                    <div className="flex gap-3">
                                        <div className="p-2 rounded-lg bg-violet/10 text-violet h-fit mt-0.5"><Users size={16} /></div>
                                        <div>
                                            <p className="text-3xs uppercase tracking-widest text-text-secondary font-semibold">Jumlah Anggota</p>
                                            <p className="text-sm text-text-primary mt-0.5">{pengajuan.jumlah_anggota ? `${pengajuan.jumlah_anggota} orang` : '-'}</p>
                                        </div>
                                    </div>
                                    <div className="flex gap-3">
                                        <div className="p-2 rounded-lg bg-violet/10 text-violet h-fit mt-0.5"><Calendar size={16} /></div>
                                        <div>
                                            <p className="text-3xs uppercase tracking-widest text-text-secondary font-semibold">Tanggal Pemakaian</p>
                                            <p className="text-sm font-semibold text-text-primary mt-0.5">{formatTanggal(pengajuan.tanggal_pakai)}</p>
                                        </div>
                                    </div>
                                    {pengajuan.jenis === 'modul' && (
                                        <div className="flex gap-3">
                                            <div className="p-2 rounded-lg bg-violet/10 text-violet h-fit mt-0.5"><CheckSquare size={16} /></div>
                                            <div>
                                                <p className="text-3xs uppercase tracking-widest text-text-secondary font-semibold">Modul Praktikum</p>
                                                <p className="text-sm text-text-primary mt-0.5">{pengajuan.modul?.nama_modul ?? '-'}</p>
                                            </div>
                                        </div>
                                    )}
                                </div>
                            </div>
                        </div>

                        {/* Items Table Card */}
                        <div className="card overflow-hidden">
                            <div className="px-5 py-4 border-b border-border bg-dark-surface/30">
                                <h3 className="text-sm font-semibold text-text-primary">Daftar Bahan BHP yang Diminta</h3>
                            </div>
                            <div className="overflow-x-auto">
                                <table className="w-full">
                                    <thead>
                                        <tr className="border-b border-border bg-dark-surface/10">
                                            <th className="section-header text-left py-3 px-5">Nama Bahan</th>
                                            <th className="section-header text-right py-3 px-5">Jumlah Kebutuhan</th>
                                            <th className="section-header text-center py-3 px-5">Satuan</th>
                                        </tr>
                                    </thead>
                                    <tbody className="divide-y divide-border/40">
                                        {pengajuan.items?.map(item => (
                                            <tr key={item.id} className="hover:bg-dark-surface/30 transition-colors">
                                                <td className="px-5 py-3.5 text-sm font-semibold text-text-primary">
                                                    {item.nama_bahan_snapshot}
                                                </td>
                                                <td className="px-5 py-3.5 text-sm font-mono font-bold text-right text-text-primary">
                                                    {parseFloat(item.jumlah)}
                                                </td>
                                                <td className="px-5 py-3.5 text-xs text-text-secondary font-semibold text-center">
                                                    <span className="px-2 py-0.5 rounded bg-dark-surface border border-border">
                                                        {item.satuan_snapshot}
                                                    </span>
                                                </td>
                                            </tr>
                                        ))}
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    {/* Right Column (1/3) - System Logs & Workflow */}
                    <div className="lg:col-span-1 space-y-6">

                        {/* Transaction Status Card & Logs */}
                        <div className="card p-5 space-y-5">
                            <h3 className="text-sm font-semibold text-text-primary border-b border-border pb-3">Alur & Log Pengajuan</h3>

                            <div className="relative border-l border-border pl-6 space-y-5 py-1">

                                {/* Step 1: Dibuat */}
                                <div className="relative">
                                    <span className="absolute -left-[31px] top-0 w-4 h-4 rounded-full bg-success flex items-center justify-center text-white ring-4 ring-dark-surface">
                                        <Check size={9} />
                                    </span>
                                    <div>
                                        <p className="text-xs font-semibold text-text-primary">Pengajuan Dikirim</p>
                                        <p className="text-3xs text-text-secondary mt-0.5">{formatTanggal(pengajuan.created_at)}</p>
                                        <p className="text-3xs text-text-secondary mt-1">Diajukan oleh {pengajuan.user?.name}</p>
                                    </div>
                                </div>

                                {/* Step 2: Review (Approve/Reject) */}
                                <div className="relative">
                                    <span className={`absolute -left-[31px] top-0 w-4 h-4 rounded-full flex items-center justify-center text-white ring-4 ring-dark-surface ${
                                        pengajuan.status === 'pending_review' ? 'bg-warning' : (pengajuan.status === 'rejected' ? 'bg-error' : 'bg-success')
                                    }`}>
                                        {pengajuan.status === 'pending_review' ? <Clock size={9} /> : (pengajuan.status === 'rejected' ? <Ban size={9} /> : <Check size={9} />)}
                                    </span>
                                    <div>
                                        <p className="text-xs font-semibold text-text-primary">
                                            {pengajuan.status === 'pending_review' ? 'Menunggu Review Laboran' : (pengajuan.status === 'rejected' ? 'Ditolak Laboran' : 'Disetujui Laboran')}
                                        </p>
                                        {pengajuan.approved_at && (
                                            <p className="text-3xs text-text-secondary mt-0.5">{formatTanggal(pengajuan.approved_at)}</p>
                                        )}
                                        {pengajuan.approver && (
                                            <p className="text-3xs text-text-secondary mt-1">Direview oleh {pengajuan.approver.name}</p>
                                        )}
                                    </div>
                                </div>

                                {/* Step 3: Complete */}
                                <div className="relative">
                                    <span className={`absolute -left-[31px] top-0 w-4 h-4 rounded-full flex items-center justify-center text-white ring-4 ring-dark-surface ${
                                        pengajuan.status === 'completed' ? 'bg-success' : 'bg-border text-text-secondary'
                                    }`}>
                                        <Check size={9} />
                                    </span>
                                    <div>
                                        <p className="text-xs font-semibold text-text-primary">
                                            {pengajuan.status === 'completed' ? 'Bahan Diserahkan & Selesai' : 'Penyerahan Bahan'}
                                        </p>
                                        {pengajuan.completed_at && (
                                            <p className="text-3xs text-text-secondary mt-0.5">{formatTanggal(pengajuan.completed_at)}</p>
                                        )}
                                        {pengajuan.completer && (
                                            <p className="text-3xs text-text-secondary mt-1">Diserahkan oleh {pengajuan.completer.name}</p>
                                        )}
                                    </div>
                                </div>
                            </div>
                        </div>

                        {/* Reject Reason / Notes Card */}
                        {pengajuan.reject_reason && (
                            <div className="card p-5 border-error/20 bg-error/5 space-y-2">
                                <h4 className="text-xs font-bold text-error uppercase tracking-wider flex items-center gap-1.5">
                                    <Ban size={14} /> Alasan Penolakan
                                </h4>
                                <p className="text-sm text-text-primary leading-relaxed">{pengajuan.reject_reason}</p>
                            </div>
                        )}

                        {/* Additional Notes / Keterangan */}
                        {pengajuan.keterangan && (
                            <div className="card p-5 space-y-2">
                                <h4 className="text-xs font-bold text-text-secondary uppercase tracking-wider">
                                    Catatan / Keterangan
                                </h4>
                                <p className="text-sm text-text-primary leading-relaxed">{pengajuan.keterangan}</p>
                            </div>
                        )}
                    </div>
                </div>
            </div>
        </AppLayout>
    );
}
