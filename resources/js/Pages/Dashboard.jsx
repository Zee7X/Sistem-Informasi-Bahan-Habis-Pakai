import { useState } from 'react';
import { Head, usePage, Link } from '@inertiajs/react';
import AppLayout from '@/Layouts/AppLayout';
import {
    FlaskConical, Users, Clock, AlertTriangle, ArrowRight,
    BarChart3, Check, ClipboardList, PackagePlus
} from 'lucide-react';

/* ── Accent tokens ────────────────────────────────────── */
const ACCENT = {
    teal:   { bg: 'bg-teal/10',   text: 'text-teal',   bar: '#0F766E' },
    gold:   { bg: 'bg-gold/10',   text: 'text-gold',   bar: '#B45309' },
    coral:  { bg: 'bg-error/10',  text: 'text-error',  bar: '#DC2626' },
    sky:    { bg: 'bg-sky/10',    text: 'text-sky',    bar: '#0369A1' },
    success:{ bg: 'bg-success/10',text: 'text-success',bar: '#16A34A' },
};

// Legacy map so existing code passes color="violet" / color="success" etc.
const COLOR_MAP = {
    violet:  ACCENT.teal,
    success: ACCENT.success,
    warning: ACCENT.gold,
    error:   ACCENT.coral,
    teal:    ACCENT.teal,
    gold:    ACCENT.gold,
    coral:   ACCENT.coral,
    sky:     ACCENT.sky,
};

/* ── Stat Card ────────────────────────────────────────── */
function StatCard({ label, value, icon: Icon, color = 'teal', footerType, pulse }) {
    const c = COLOR_MAP[color] || ACCENT.teal;

    return (
        <div className="card p-5 flex flex-col justify-between min-h-[130px]">
            <div className="flex justify-between items-start gap-4">
                <div className="min-w-0 flex-1">
                    <p className="section-header leading-none mb-2.5">
                        {label}
                    </p>
                    <span className="text-3xl font-bold text-text-primary leading-none tracking-tight font-display">
                        {value ?? 0}
                    </span>
                </div>

                <div className={`w-11 h-11 rounded-md flex items-center justify-center flex-shrink-0 ${c.bg} ${c.text}`}>
                    <Icon size={20} strokeWidth={2} />
                </div>
            </div>

            {/* Footer: critical stock indicator only (real data) */}
            {footerType === 'status-kritis' && (
                <div className="mt-4 pt-3 border-t border-border flex items-center gap-2">
                    <span className={`w-1.5 h-1.5 rounded-full ${value > 0 ? 'bg-error' : 'bg-success'}`} />
                    <span className="text-2xs font-medium text-text-secondary uppercase tracking-wider">
                        {value > 0 ? 'Perlu tindakan' : 'Status: aman'}
                    </span>
                </div>
            )}
        </div>
    );
}

/* ── Status Chip ──────────────────────────────────────── */
function StatusChip({ status }) {
    const map = {
        pending_review: { label: 'Pending',  cls: 'bg-warning/10 text-warning' },
        approved:       { label: 'Approved', cls: 'bg-teal/10 text-teal' },
        completed:      { label: 'Selesai',  cls: 'bg-success/10 text-success' },
        rejected:       { label: 'Ditolak',  cls: 'bg-error/10 text-error' },
    };
    const s = map[status] ?? { label: status, cls: 'bg-dark-surface text-text-secondary' };
    return (
        <span className={`inline-flex items-center px-2.5 py-0.5 rounded-full text-2xs font-semibold ${s.cls}`}>
            {s.label}
        </span>
    );
}

/* ── Section Header ───────────────────────────────────── */
function SectionTitle({ icon: Icon, title, iconColor = 'text-teal' }) {
    return (
        <div className="flex items-center gap-2">
            <Icon size={15} className={iconColor} strokeWidth={2} />
            <h2 className="text-sm font-semibold text-text-primary font-display">{title}</h2>
        </div>
    );
}

/* ── Main Dashboard ───────────────────────────────────── */
export default function Dashboard({ stats, stokKritis, recentPengajuan, chartData, topBahan }) {
    const { auth } = usePage().props;
    const role = auth?.user?.role;

    const getStatsConfig = () => {
        if (role === 'admin') {
            return [
                { label: 'Total Bahan',        value: stats?.total_bahan,    icon: FlaskConical,  color: 'teal' },
                { label: 'Mahasiswa Aktif',    value: stats?.total_user,     icon: Users,         color: 'success' },
                { label: 'Menunggu Review',    value: stats?.pending_review, icon: Clock,         color: 'gold' },
                { label: 'Stok Kritis',        value: stats?.stok_kritis,    icon: AlertTriangle, color: 'coral', footerType: 'status-kritis' },
            ];
        }
        if (role === 'mahasiswa') {
            return [
                { label: 'Total Pengajuan',  value: stats?.total_pengajuan, icon: ClipboardList, color: 'teal' },
                { label: 'Menunggu Review',  value: stats?.pending_review,  icon: Clock,         color: 'gold' },
                { label: 'Disetujui',        value: stats?.approved,        icon: Check,         color: 'success' },
                { label: 'Selesai',          value: stats?.completed,       icon: FlaskConical,  color: 'sky' },
            ];
        }
        if (role === 'ketua_jurusan') {
            return [
                { label: 'Total Transaksi', value: stats?.total_transaksi, icon: ClipboardList,  color: 'teal' },
                { label: 'Total Bahan',     value: stats?.total_bahan,     icon: FlaskConical,   color: 'success' },
                { label: 'Stok Kritis',     value: stats?.stok_kritis,     icon: AlertTriangle,  color: 'coral', footerType: 'status-kritis' },
                { label: 'Pending Belanja', value: stats?.pending_belanja, icon: PackagePlus,    color: 'gold' },
            ];
        }
        return [];
    };

    /* ── Chart ── */
    const labels = chartData?.labels || [];
    const values = chartData?.values || [];
    const hasData = values.some(v => v > 0);
    const maxVal = values.length > 0 ? Math.max(...values) : 0;

    const [hoveredIndex, setHoveredIndex] = useState(null);

    const steps = 4;
    const displayMax = maxVal > 0 ? Math.max(steps, Math.ceil(maxVal / steps) * steps) : steps;
    const yAxisTicks = Array.from({ length: steps + 1 }, (_, idx) => (displayMax * (steps - idx)) / steps);

    const svgWidth = 1000, svgHeight = 160;
    const paddingLeft = 40, paddingRight = 15, paddingTop = 15, paddingBottom = 25;
    const width = svgWidth - paddingLeft - paddingRight;
    const height = svgHeight - paddingTop - paddingBottom;

    const points = labels.map((label, i) => {
        const val = values[i] ?? 0;
        const x = paddingLeft + (labels.length > 1 ? (i / (labels.length - 1)) * width : width / 2);
        const y = paddingTop + height - (val / displayMax) * height;
        return { x, y, label, val };
    });

    const getControlPoints = (p0, p1, p2, p3) => {
        const t = 0.18;
        return {
            cp1x: p1.x + (p2.x - p0.x) * t, cp1y: p1.y + (p2.y - p0.y) * t,
            cp2x: p2.x - (p3.x - p1.x) * t, cp2y: p2.y - (p3.y - p1.y) * t,
        };
    };

    let linePath = '', areaPath = '';
    if (points.length > 0) {
        linePath = `M ${points[0].x} ${points[0].y}`;
        for (let i = 0; i < points.length - 1; i++) {
            const p1 = points[i], p2 = points[i + 1];
            const p0 = points[i - 1] || p1, p3 = points[i + 2] || p2;
            const cp = getControlPoints(p0, p1, p2, p3);
            linePath += ` C ${cp.cp1x} ${cp.cp1y}, ${cp.cp2x} ${cp.cp2y}, ${p2.x} ${p2.y}`;
        }
        areaPath = `${linePath} L ${points[points.length - 1].x} ${paddingTop + height} L ${points[0].x} ${paddingTop + height} Z`;
    }

    const colWidth = points.length > 1 ? width / (points.length - 1) : width;
    const activeIdx = hoveredIndex !== null ? hoveredIndex : (points.length > 0 ? points.length - 1 : null);
    const activePoint = activeIdx !== null ? points[activeIdx] : null;

    return (
        <AppLayout title="Dashboard">
            <Head title="Dashboard" />
            <div className="p-6 space-y-6 max-w-7xl mx-auto">

                {/* Page heading */}
                <div>
                    <h1 className="text-xl font-semibold text-text-primary font-display">
                        Selamat datang, {auth?.user?.name?.split(' ')[0]}
                    </h1>
                    <p className="text-xs text-text-secondary mt-1">
                        Sistem Informasi BHP — Politeknik Negeri Cilacap, Lab TPPL
                    </p>
                </div>

                {/* Main Layout */}
                <div className="grid grid-cols-1 lg:grid-cols-3 gap-6">

                    {/* Left: Stats + Chart */}
                    <div className="lg:col-span-2 space-y-6">

                        {/* Stats Grid */}
                        <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            {getStatsConfig().map((cfg, idx) => (
                                <StatCard key={idx} {...cfg} />
                            ))}
                        </div>

                        {/* Chart Card */}
                        <div className="card p-5">
                            <div className="flex items-center justify-between mb-5">
                                <div>
                                    <h2 className="text-sm font-semibold text-text-primary font-display">
                                        Tren Penggunaan BHP
                                    </h2>
                                    <p className="text-2xs text-text-secondary mt-0.5">Jumlah transaksi selesai dalam 6 bulan terakhir</p>
                                </div>
                                <span className="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-2xs font-medium bg-teal/10 text-teal">
                                    <TrendingUpPlaceholder />
                                    Transaksi Selesai
                                </span>
                            </div>

                            <div className="relative w-full select-none">
                                {labels.length > 0 ? (
                                    <div className="relative w-full">
                                        <svg viewBox={`0 0 ${svgWidth} ${svgHeight}`} className="w-full h-auto overflow-visible">
                                            <defs>
                                                <linearGradient id="chart-gradient" x1="0" y1="0" x2="0" y2="1">
                                                    <stop offset="0%"   stopColor="#0F766E" stopOpacity="0.15" />
                                                    <stop offset="100%" stopColor="#0F766E" stopOpacity="0.00" />
                                                </linearGradient>
                                            </defs>

                                            {/* Grid lines */}
                                            {yAxisTicks.map((tickVal, idx) => {
                                                const tickY = paddingTop + (idx / steps) * height;
                                                return (
                                                    <g key={idx}>
                                                        <line x1={paddingLeft} y1={tickY} x2={svgWidth - paddingRight} y2={tickY}
                                                            stroke="#E2E8F0" strokeWidth="1" />
                                                        <text x={paddingLeft - 10} y={tickY + 3.5} textAnchor="end"
                                                            fill="#64748B" fontSize="10" fontWeight="500">
                                                            {tickVal}
                                                        </text>
                                                    </g>
                                                );
                                            })}

                                            {/* Gradient area */}
                                            {areaPath && <path d={areaPath} fill="url(#chart-gradient)" />}

                                            {/* Line */}
                                            {linePath && (
                                                <path d={linePath} fill="none" stroke="#0F766E" strokeWidth="2"
                                                    strokeLinecap="round" strokeLinejoin="round" />
                                            )}

                                            {/* X-axis labels */}
                                            {points.map((pt, i) => (
                                                <text key={i} x={pt.x} y={svgHeight - 4} textAnchor="middle"
                                                    fill="#64748B" fontSize="10" fontWeight="500">
                                                    {pt.label}
                                                </text>
                                            ))}

                                            {/* Active vertical line */}
                                            {activePoint && (
                                                <line x1={activePoint.x} y1={paddingTop} x2={activePoint.x} y2={paddingTop + height}
                                                    stroke="#CBD5E1" strokeWidth="1" strokeDasharray="4 4" />
                                            )}

                                            {/* Active dot */}
                                            {activePoint && (
                                                <circle cx={activePoint.x} cy={activePoint.y} r="5"
                                                    fill="#FFFFFF" stroke="#0F766E" strokeWidth="2.5" />
                                            )}

                                            {/* Hover rects */}
                                            {points.map((pt, i) => {
                                                const triggerWidth = i === 0 || i === points.length - 1 ? colWidth / 2 : colWidth;
                                                const triggerX = i === 0 ? pt.x : pt.x - colWidth / 2;
                                                return (
                                                    <rect key={i} x={triggerX} y={paddingTop} width={triggerWidth} height={height}
                                                        fill="transparent" className="cursor-pointer"
                                                        onMouseEnter={() => setHoveredIndex(i)}
                                                        onMouseLeave={() => setHoveredIndex(null)} />
                                                );
                                            })}
                                        </svg>

                                        {/* Tooltip */}
                                        {activePoint && (
                                            <div
                                                className="absolute pointer-events-none transition-all duration-150 z-20"
                                                style={{
                                                    left: `${(activePoint.x / svgWidth) * 100}%`,
                                                    top: `${(activePoint.y / svgHeight) * 100}%`,
                                                    transform: 'translate(-50%, -120%)',
                                                }}
                                            >
                                                <div className="px-3 py-1.5 rounded-md text-center flex flex-col gap-0.5 min-w-[80px] bg-slate-900 shadow-modal">
                                                    <span className="text-[9px] font-medium text-slate-400">
                                                        {activePoint.label}
                                                    </span>
                                                    <span className="text-xs font-semibold text-white font-mono">
                                                        {activePoint.val} <span className="text-[9px] font-normal text-slate-400">Tx</span>
                                                    </span>
                                                </div>
                                            </div>
                                        )}
                                    </div>
                                ) : (
                                    <div className="w-full h-36 flex items-center justify-center">
                                        <p className="text-xs text-text-secondary">Tidak ada data untuk ditampilkan</p>
                                    </div>
                                )}

                                {/* Empty state overlay */}
                                {!hasData && labels.length > 0 && (
                                    <div className="absolute inset-0 flex flex-col items-center justify-center text-center z-10 select-none rounded-lg bg-white/85">
                                        <div className="w-10 h-10 rounded-full flex items-center justify-center mb-2 bg-teal/10">
                                            <BarChart3 size={20} className="text-teal" />
                                        </div>
                                        <p className="text-xs font-semibold text-text-primary">
                                            Belum Ada Riwayat Transaksi
                                        </p>
                                        <p className="text-2xs text-text-secondary mt-0.5">Sistem belum mencatat transaksi selesai dalam 6 bulan terakhir</p>
                                    </div>
                                )}
                            </div>
                        </div>
                    </div>

                    {/* Right Column: Cards */}
                    <div className="lg:col-span-1 space-y-5">

                        {/* Admin: Recent Transactions */}
                        {role === 'admin' && (
                            <div className="card overflow-hidden">
                                <div className="px-5 py-4 border-b border-border flex items-center justify-between">
                                    <SectionTitle icon={Clock} title="Transaksi Terbaru" />
                                    <Link
                                        href="/admin/pengajuan"
                                        className="text-xs font-medium flex items-center gap-1 text-teal hover:text-teal-dark transition-colors"
                                    >
                                        Semua <ArrowRight size={11} />
                                    </Link>
                                </div>
                                <div className="divide-y divide-border">
                                    {recentPengajuan?.length > 0 ? (
                                        recentPengajuan.slice(0, 5).map(p => (
                                            <Link
                                                key={p.id}
                                                href={`/admin/pengajuan/${p.id}`}
                                                className="block px-5 py-3 hover:bg-dark-surface/50 transition-colors"
                                            >
                                                <div className="flex items-start justify-between gap-3">
                                                    <div className="min-w-0 flex-1">
                                                        <p className="text-xs font-medium font-mono mb-0.5 text-teal">
                                                            {p.kode_pengajuan}
                                                        </p>
                                                        <p className="text-sm font-medium text-text-primary truncate">{p.user?.name}</p>
                                                        <p className="text-xs text-text-secondary truncate mt-0.5">{p.mata_kuliah || 'Mandiri'}</p>
                                                    </div>
                                                    <StatusChip status={p.status} />
                                                </div>
                                            </Link>
                                        ))
                                    ) : (
                                        <div className="px-5 py-8 text-center text-xs text-text-secondary">
                                            Belum ada pengajuan transaksi.
                                        </div>
                                    )}
                                </div>
                            </div>
                        )}

                        {/* Admin: Stok Kritis */}
                        {role === 'admin' && (
                            <div className="card overflow-hidden">
                                <div className="px-5 py-4 border-b border-border flex items-center justify-between">
                                    <SectionTitle icon={AlertTriangle} title="Stok Kritis" iconColor="text-error" />
                                    <Link href="/admin/bahan" className="text-xs font-medium flex items-center gap-1 text-teal hover:text-teal-dark transition-colors">
                                        Semua <ArrowRight size={11} />
                                    </Link>
                                </div>
                                <div className="divide-y divide-border">
                                    {stokKritis?.length > 0 ? (
                                        stokKritis.slice(0, 5).map(b => (
                                            <div key={b.id} className="flex items-center justify-between px-5 py-2.5">
                                                <div className="min-w-0 flex-1">
                                                    <p className="text-sm font-medium text-text-primary truncate">{b.nama_bahan}</p>
                                                    <p className="text-xs text-text-secondary">Min: {b.minimal_stok} {b.satuan?.nama}</p>
                                                </div>
                                                <span className="text-sm font-semibold ml-3 text-error">
                                                    {b.stok}
                                                </span>
                                            </div>
                                        ))
                                    ) : (
                                        <div className="px-5 py-8 text-center text-xs text-text-secondary">
                                            Semua stok bahan dalam kondisi aman.
                                        </div>
                                    )}
                                </div>
                            </div>
                        )}

                        {/* Ketua Jurusan: Top Bahan */}
                        {role === 'ketua_jurusan' && (
                            <div className="card overflow-hidden">
                                <div className="px-5 py-4 border-b border-border flex items-center justify-between">
                                    <SectionTitle icon={BarChart3} title="Bahan Terpopuler" />
                                    <span className="text-2xs font-medium uppercase tracking-wider text-text-secondary">
                                        6 Bulan
                                    </span>
                                </div>
                                <div className="divide-y divide-border">
                                    {topBahan?.length > 0 ? (
                                        topBahan.slice(0, 5).map((tb, idx) => (
                                            <div key={tb.bahan_id} className="flex items-center justify-between px-5 py-2.5">
                                                <div className="flex items-center gap-3 min-w-0 flex-1">
                                                    <span className="w-6 h-6 rounded-full flex items-center justify-center text-2xs font-semibold flex-shrink-0 bg-dark-surface text-text-secondary">
                                                        {idx + 1}
                                                    </span>
                                                    <div className="min-w-0 flex-1">
                                                        <p className="text-sm font-medium text-text-primary truncate">{tb.bahan?.nama_bahan || 'Tidak Diketahui'}</p>
                                                        <p className="text-xs text-text-secondary truncate">{tb.bahan?.kode_bahan}</p>
                                                    </div>
                                                </div>
                                                <span className="text-sm font-semibold ml-3 text-text-primary">
                                                    {tb.total_pakai}
                                                </span>
                                            </div>
                                        ))
                                    ) : (
                                        <div className="px-5 py-8 text-center text-xs text-text-secondary">
                                            Belum ada data penggunaan bahan.
                                        </div>
                                    )}
                                </div>
                            </div>
                        )}

                        {/* Mahasiswa: Recent Pengajuan */}
                        {role === 'mahasiswa' && (
                            <div className="card overflow-hidden">
                                <div className="px-5 py-4 border-b border-border flex items-center justify-between">
                                    <SectionTitle icon={ClipboardList} title="Pengajuan Terbaru" />
                                    <Link
                                        href="/mahasiswa/pengajuan"
                                        className="text-xs font-medium flex items-center gap-1 text-teal hover:text-teal-dark transition-colors"
                                    >
                                        Semua <ArrowRight size={11} />
                                    </Link>
                                </div>
                                <div className="divide-y divide-border">
                                    {recentPengajuan?.length > 0 ? (
                                        recentPengajuan.slice(0, 5).map(p => (
                                            <Link
                                                key={p.id}
                                                href={`/mahasiswa/pengajuan/${p.id}`}
                                                className="block px-5 py-3 hover:bg-dark-surface/50 transition-colors"
                                            >
                                                <div className="flex items-start justify-between gap-3">
                                                    <div className="min-w-0 flex-1">
                                                        <p className="text-xs font-medium font-mono mb-0.5 text-teal">
                                                            {p.kode_pengajuan}
                                                        </p>
                                                        <p className="text-sm font-medium text-text-primary truncate">{p.mata_kuliah || 'Mandiri'}</p>
                                                        <p className="text-xs text-text-secondary mt-0.5">
                                                            {p.tanggal_pakai ? new Date(p.tanggal_pakai).toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' }) : '-'}
                                                        </p>
                                                    </div>
                                                    <StatusChip status={p.status} />
                                                </div>
                                            </Link>
                                        ))
                                    ) : (
                                        <div className="px-5 py-8 text-center text-xs text-text-secondary">
                                            Anda belum pernah membuat pengajuan.
                                        </div>
                                    )}
                                </div>
                            </div>
                        )}
                    </div>
                </div>

            </div>
        </AppLayout>
    );
}

/* Small inline icon for the chart legend */
function TrendingUpPlaceholder() {
    return (
        <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2.5" strokeLinecap="round" strokeLinejoin="round" aria-hidden="true">
            <polyline points="23 6 13.5 15.5 8.5 10.5 1 18" />
            <polyline points="17 6 23 6 23 12" />
        </svg>
    );
}
