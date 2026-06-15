import { Head, Link } from '@inertiajs/react';
import AppLayout from '@/Layouts/AppLayout';
import { ArrowLeft, User, Hash, BookOpen, Calendar, Users, FileText, Check, Ban, Clock } from 'lucide-react';

function StatusBadge({ status }) {
    const map = {
        pending_review: { label: 'Menunggu Review', cls: 'bg-warning/10 text-warning border-warning/20' },
        approved:       { label: 'Disetujui',        cls: 'bg-violet/10 text-violet border-violet/20' },
        completed:      { label: 'Selesai',           cls: 'bg-success/10 text-success border-success/20' },
        rejected:       { label: 'Ditolak',           cls: 'bg-error/10 text-error border-error/20' },
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
    const formatTanggal = (dateString) => {
        if (!dateString) return '-';
        try {
            const date = new Date(dateString);
            return date.toLocaleDateString('id-ID', { day: '2-digit', month: 'long', year: 'numeric' });
        } catch (e) {
            return dateString;
        }
    };

    return (
        <AppLayout title={`Pengajuan ${pengajuan.kode_pengajuan}`}>
            <Head title={`Detail Pengajuan ${pengajuan.kode_pengajuan}`} />
            <div className="p-6 space-y-6 max-w-7xl mx-auto">

                {/* Header card */}
                <div className="flex flex-wrap items-center justify-between gap-4 bg-dark-card p-4 rounded-xl border border-border">
                    <div className="flex items-center gap-3">
                        <Link href="/mahasiswa/pengajuan" className="btn-secondary flex items-center gap-1.5 py-1.5 px-3">
                            <ArrowLeft size={14} /> Kembali
                        </Link>
                        <span className="text-text-secondary">|</span>
                        <div className="flex flex-col">
                            <h1 className="text-lg font-bold text-text-primary leading-tight">{pengajuan.kode_pengajuan}</h1>
                            <p className="text-2xs text-text-secondary mt-0.5">Dibuat pada: {formatTanggal(pengajuan.created_at)}</p>
                        </div>
                    </div>
                </div>

                {/* Main Content Layout */}
                <div className="grid grid-cols-1 lg:grid-cols-3 gap-6">

                    {/* Left Column: Info & Items */}
                    <div className="lg:col-span-2 space-y-6">

                        {/* Detail Info Card */}
                        <div className="card overflow-hidden">
                            <div className="px-5 py-4 border-b border-border bg-dark-surface/30 flex justify-between items-center">
                                <h3 className="text-sm font-semibold text-text-primary">Informasi Pengajuan</h3>
                                <StatusBadge status={pengajuan.status} />
                            </div>
                            <div className="grid grid-cols-1 md:grid-cols-2 divide-y md:divide-y-0 md:divide-x divide-border bg-dark-card/20">

                                <div className="p-5 space-y-4">
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

                                <div className="p-5 space-y-4">
                                    <div className="flex gap-3">
                                        <div className="p-2 rounded-lg bg-violet/10 text-violet h-fit mt-0.5"><Users size={16} /></div>
                                        <div>
                                            <p className="text-3xs uppercase tracking-widest text-text-secondary font-semibold">Kelas / Kelompok</p>
                                            <p className="text-sm text-text-primary mt-0.5">{pengajuan.kelas ?? '-'} / {pengajuan.kelompok ?? '-'}</p>
                                        </div>
                                    </div>
                                    <div className="flex gap-3">
                                        <div className="p-2 rounded-lg bg-violet/10 text-violet h-fit mt-0.5"><Calendar size={16} /></div>
                                        <div>
                                            <p className="text-3xs uppercase tracking-widest text-text-secondary font-semibold">Tanggal Pemakaian</p>
                                            <p className="text-sm font-semibold text-text-primary mt-0.5">{formatTanggal(pengajuan.tanggal_pakai)}</p>
                                        </div>
                                    </div>
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

                    {/* Right Column: Workflow Logs & Notes */}
                    <div className="lg:col-span-1 space-y-6">

                        {/* Alur Log */}
                        <div className="card p-5 space-y-5">
                            <h3 className="text-sm font-semibold text-text-primary border-b border-border pb-3">Alur & Log Pengajuan</h3>
                            <div className="relative border-l border-border pl-6 space-y-5 py-1">

                                <div className="relative">
                                    <span className="absolute -left-[31px] top-0 w-4 h-4 rounded-full bg-success flex items-center justify-center text-white ring-4 ring-dark-surface">
                                        <Check size={9} />
                                    </span>
                                    <div>
                                        <p className="text-xs font-semibold text-text-primary">Pengajuan Dikirim</p>
                                        <p className="text-3xs text-text-secondary mt-0.5">{formatTanggal(pengajuan.created_at)}</p>
                                    </div>
                                </div>

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
                                    </div>
                                </div>

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
                                    </div>
                                </div>

                            </div>
                        </div>

                        {/* Reject Reason */}
                        {pengajuan.reject_reason && (
                            <div className="card p-5 border-error/20 bg-error/5 space-y-2">
                                <h4 className="text-xs font-bold text-error uppercase tracking-wider flex items-center gap-1.5">
                                    <Ban size={14} /> Alasan Penolakan
                                </h4>
                                <p className="text-sm text-text-primary leading-relaxed">{pengajuan.reject_reason}</p>
                            </div>
                        )}

                        {/* Keterangan */}
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
