import { Head, Link } from '@inertiajs/react';
import AppLayout from '@/Layouts/AppLayout';
import { ArrowLeft } from 'lucide-react';

function StatusChip({ status }) {
    const map = {
        pending_review: { label: 'Menunggu Review', cls: 'chip-warning' },
        approved:       { label: 'Disetujui',        cls: 'chip-violet' },
        completed:      { label: 'Selesai',           cls: 'chip-success' },
        rejected:       { label: 'Ditolak',           cls: 'chip-error' },
    };
    const s = map[status] ?? { label: status, cls: 'chip-neutral' };
    return <span className={`chip ${s.cls}`}>{s.label}</span>;
}

export default function Show({ pengajuan }) {
    return (
        <AppLayout title={`Pengajuan ${pengajuan.kode_pengajuan}`}>
            <Head title={`Detail Pengajuan ${pengajuan.kode_pengajuan}`} />
            <div className="p-5 max-w-2xl space-y-4">
                <div className="flex items-center gap-3">
                    <Link href="/mahasiswa/pengajuan" className="btn-ghost btn-sm">
                        <ArrowLeft size={13} /> Kembali
                    </Link>
                </div>

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
                            { label: 'Mata Kuliah', value: pengajuan.mata_kuliah ?? '-' },
                            { label: 'Kelas / Kelompok', value: `${pengajuan.kelas ?? '-'} / ${pengajuan.kelompok ?? '-'}` },
                            { label: 'Tanggal Pakai', value: pengajuan.tanggal_pakai },
                            { label: 'Tanggal Pengajuan', value: new Date(pengajuan.created_at).toLocaleDateString('id-ID') },
                        ].map(({ label, value }) => (
                            <div key={label} className="bg-dark-card px-5 py-3">
                                <p className="text-2xs text-text-secondary uppercase tracking-wider mb-0.5">{label}</p>
                                <p className="text-sm text-text-primary">{value}</p>
                            </div>
                        ))}
                    </div>

                    <div className="border-t border-border">
                        <p className="section-header border-b border-border">Bahan yang Diminta</p>
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
