import { useState } from 'react';
import { Head, usePage, Link } from '@inertiajs/react';
import AppLayout from '@/Layouts/AppLayout';
import {
    FlaskConical, Users, Clock, AlertTriangle, ArrowRight,
    BarChart3, Check, ClipboardList, PackagePlus, TrendingUp
} from 'lucide-react';

/* ── Design token helpers (Flip7 colors) ─────────────── */
const ACCENT = {
    teal:  { bg: 'rgba(43,168,162,0.10)',  text: '#2BA8A2', border: 'rgba(43,168,162,0.25)', glow: 'rgba(43,168,162,0.25)', bar: '#2BA8A2' },
    gold:  { bg: 'rgba(255,210,63,0.12)',  text: '#E6B800', border: 'rgba(255,210,63,0.30)', glow: 'rgba(255,210,63,0.30)', bar: '#FFD23F' },
    coral: { bg: 'rgba(239,108,74,0.10)',  text: '#EF6C4A', border: 'rgba(239,108,74,0.25)', glow: 'rgba(239,108,74,0.30)', bar: '#EF6C4A' },
    sky:   { bg: 'rgba(93,173,226,0.10)',  text: '#5DADE2', border: 'rgba(93,173,226,0.25)', glow: 'rgba(93,173,226,0.25)', bar: '#5DADE2' },
    success:{ bg: 'rgba(39,174,96,0.10)', text: '#27AE60', border: 'rgba(39,174,96,0.25)',  glow: 'rgba(39,174,96,0.20)',  bar: '#27AE60' },
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
function StatCard({ label, value, icon: Icon, trend, trendType = 'up', color = 'teal', footerType, pulse }) {
    const c = COLOR_MAP[color] || ACCENT.teal;

    const trendStyle = trendType === 'up'
        ? { background: 'rgba(39,174,96,0.12)', color: '#27AE60', border: '1px solid rgba(39,174,96,0.25)' }
        : trendType === 'down'
        ? { background: 'rgba(239,108,74,0.12)', color: '#EF6C4A', border: '1px solid rgba(239,108,74,0.25)' }
        : { background: 'rgba(90,138,134,0.10)', color: '#5A8A86',  border: '1px solid rgba(90,138,134,0.20)' };

    return (
        <div
            className={`relative overflow-hidden rounded-xl bg-white flex flex-col justify-between min-h-[150px] transition-all duration-300 hover:-translate-y-0.5 ${pulse ? 'animate-glow-pulse' : ''}`}
            style={{
                borderTop: `1px solid ${c.border}`,
                borderRight: `1px solid ${c.border}`,
                borderBottom: `1px solid ${c.border}`,
                borderLeft: `6px solid ${c.bar}`,
                boxShadow: `0 4px 20px ${c.glow}, 0 1px 4px rgba(0,0,0,0.04)`,
            }}
        >
            {/* Top glow decoration */}
            <div
                className="absolute top-0 right-0 w-32 h-32 rounded-full pointer-events-none"
                style={{ background: c.bg, transform: 'translate(30%, -30%)', filter: 'blur(20px)' }}
            />

            <div className="p-5 relative">
                <div className="flex justify-between items-start gap-4">
                    <div className="min-w-0 flex-1">
                        <p className="text-2xs font-bold uppercase tracking-widest leading-none mb-2" style={{ color: '#5A8A86', letterSpacing: '0.12em' }}>
                            {label}
                        </p>
                        <div className="flex items-end gap-2 mt-2">
                            <span className="text-3xl font-extrabold leading-none" style={{ color: '#0D3B38', fontFamily: 'Outfit, sans-serif' }}>
                                {value ?? 0}
                            </span>
                            {trend && (
                                <span
                                    className="text-[10px] font-bold px-2 py-0.5 rounded-full mb-0.5"
                                    style={trendStyle}
                                >
                                    {trend}
                                </span>
                            )}
                        </div>
                    </div>

                    {/* Icon Badge */}
                    <div
                        className="w-12 h-12 rounded-xl flex items-center justify-center flex-shrink-0 transition-transform duration-200 hover:scale-110"
                        style={{
                            background: c.bg,
                            border: `1.5px solid ${c.border}`,
                            boxShadow: `0 4px 12px ${c.glow}`,
                        }}
                    >
                        <Icon size={22} strokeWidth={2.5} style={{ color: c.text }} />
                    </div>
                </div>

                {/* Footer sparklines / indicators */}
                <div className="mt-4 pt-3" style={{ borderTop: '1px dashed rgba(43,168,162,0.20)' }}>
                    {footerType === 'bar' && (
                        <div className="flex items-end gap-1 h-5 select-none w-full">
                            {[2,3,2,4,5,7].map((h, i) => (
                                <div
                                    key={i}
                                    className="flex-1 rounded-sm transition-all duration-300"
                                    style={{
                                        height: `${h * 3}px`,
                                        background: i === 5 ? c.bar : `${c.bar}40`,
                                    }}
                                />
                            ))}
                        </div>
                    )}
                    {footerType === 'sparkline-green' && (
                        <div className="h-5 select-none w-full">
                            <svg className="w-full h-full" viewBox="0 0 100 30" fill="none" preserveAspectRatio="none">
                                <path d="M0 25 C 20 5, 40 35, 60 10 C 80 -5, 90 20, 100 8"
                                    stroke="#27AE60" strokeWidth="2.5" strokeLinecap="round" strokeLinejoin="round" />
                            </svg>
                        </div>
                    )}
                    {footerType === 'sparkline-yellow' && (
                        <div className="h-5 select-none w-full">
                            <svg className="w-full h-full" viewBox="0 0 100 30" fill="none" preserveAspectRatio="none">
                                <path d="M0 10 C 15 25, 30 5, 45 20 C 60 30, 75 10, 100 25"
                                    stroke="#FFD23F" strokeWidth="2.5" strokeLinecap="round" strokeLinejoin="round" />
                            </svg>
                        </div>
                    )}
                    {footerType === 'status-kritis' && (
                        <div className="flex items-center gap-2 select-none w-full">
                            <span
                                className="w-2 h-2 rounded-full"
                                style={{
                                    background: value > 0 ? '#EF6C4A' : '#27AE60',
                                    boxShadow: value > 0 ? '0 0 6px rgba(239,108,74,0.60)' : '0 0 6px rgba(39,174,96,0.60)',
                                    animation: value > 0 ? 'coral-pulse-shadow 1.5s ease-in-out infinite' : 'none',
                                }}
                            />
                            <span className="text-2xs font-bold font-mono tracking-widest text-text-secondary uppercase">
                                {value > 0 ? 'PERLU TINDAKAN' : 'STATUS: AMAN'}
                            </span>
                        </div>
                    )}
                </div>
            </div>
        </div>
    );
}

/* ── Status Chip ──────────────────────────────────────── */
function StatusChip({ status }) {
    const map = {
        pending_review: { label: 'Pending',  style: { background: 'rgba(255,210,63,0.18)', color: '#0D3B38', border: '1px solid rgba(230,184,0,0.35)' } },
        approved:       { label: 'Approved', style: { background: 'rgba(43,168,162,0.12)', color: '#2BA8A2', border: '1px solid rgba(43,168,162,0.30)' } },
        completed:      { label: 'Selesai',  style: { background: 'rgba(39,174,96,0.12)',  color: '#27AE60', border: '1px solid rgba(39,174,96,0.30)' } },
        rejected:       { label: 'Ditolak',  style: { background: 'rgba(239,108,74,0.12)', color: '#EF6C4A', border: '1px solid rgba(239,108,74,0.30)' } },
    };
    const s = map[status] ?? { label: status, style: { background: 'rgba(90,138,134,0.10)', color: '#5A8A86', border: '1px solid rgba(90,138,134,0.20)' } };
    return (
        <span
            className="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wide"
            style={s.style}
        >
            {s.label}
        </span>
    );
}

/* ── Section Header (Flip7 dashed style) ─────────────── */
function SectionTitle({ icon: Icon, title, iconColor = '#2BA8A2' }) {
    return (
        <div className="flex items-center gap-2 pb-2 mb-1" style={{ borderBottom: '2px dashed rgba(43,168,162,0.25)' }}>
            <div className="w-6 h-6 flex items-center justify-center">
                <Icon size={15} style={{ color: iconColor }} strokeWidth={2.5} />
            </div>
            <h2 className="text-sm font-extrabold text-text-primary" style={{ fontFamily: 'Outfit, sans-serif' }}>{title}</h2>
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
                { label: 'Total Bahan',       value: stats?.total_bahan,    icon: FlaskConical,   color: 'teal',    trend: '+12%',                                                 trendType: 'up',      footerType: 'bar' },
                { label: 'Mahasiswa Aktif',    value: stats?.total_user,     icon: Users,          color: 'success', trend: '+8%',                                                  trendType: 'up',      footerType: 'sparkline-green' },
                { label: 'Menunggu Review',    value: stats?.pending_review, icon: Clock,          color: 'gold',    trend: stats?.pending_review > 0 ? `${stats.pending_review} pending` : 'Aman', trendType: stats?.pending_review > 0 ? 'down' : 'up', footerType: 'sparkline-yellow' },
                { label: 'Stok Kritis',        value: stats?.stok_kritis,    icon: AlertTriangle,  color: 'coral',   trend: stats?.stok_kritis > 0 ? `${stats.stok_kritis} item` : 'Aman',     trendType: stats?.stok_kritis > 0 ? 'down' : 'up',    footerType: 'status-kritis', pulse: stats?.stok_kritis > 0 },
            ];
        }
        if (role === 'mahasiswa') {
            return [
                { label: 'Total Pengajuan',   value: stats?.total_pengajuan, icon: ClipboardList,  color: 'teal',    trend: 'Riwayat',  trendType: 'up',      footerType: 'bar' },
                { label: 'Menunggu Review',   value: stats?.pending_review,  icon: Clock,          color: 'gold',    trend: 'Proses',   trendType: 'neutral', footerType: 'sparkline-yellow' },
                { label: 'Disetujui',         value: stats?.approved,        icon: Check,          color: 'success', trend: 'Approved', trendType: 'up',      footerType: 'sparkline-green' },
                { label: 'Selesai',           value: stats?.completed,       icon: FlaskConical,   color: 'teal',    trend: 'Selesai',  trendType: 'up',      footerType: 'bar' },
            ];
        }
        if (role === 'ketua_jurusan') {
            return [
                { label: 'Total Transaksi',   value: stats?.total_transaksi, icon: ClipboardList,  color: 'teal',    trend: '+15%',                                                       trendType: 'up',      footerType: 'bar' },
                { label: 'Total Bahan',       value: stats?.total_bahan,     icon: FlaskConical,   color: 'success', trend: 'Aktif',                                                      trendType: 'up',      footerType: 'sparkline-green' },
                { label: 'Stok Kritis',       value: stats?.stok_kritis,     icon: AlertTriangle,  color: 'coral',   trend: stats?.stok_kritis > 0 ? 'Perlu Order' : 'Aman',              trendType: stats?.stok_kritis > 0 ? 'down' : 'up',    footerType: 'status-kritis', pulse: stats?.stok_kritis > 0 },
                { label: 'Pending Belanja',   value: stats?.pending_belanja, icon: PackagePlus,    color: 'gold',    trend: stats?.pending_belanja > 0 ? 'Review' : 'Selesai',             trendType: stats?.pending_belanja > 0 ? 'down' : 'up',footerType: 'sparkline-yellow' },
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
            <div className="p-6 space-y-6">

                {/* Greeting Banner */}
                <div
                    className="rounded-xl px-6 py-5 flex items-center justify-between overflow-hidden relative"
                    style={{
                        background: 'linear-gradient(135deg, #2BA8A2 0%, #1E8C86 50%, #3CC4BD 100%)',
                        boxShadow: '0 8px 32px rgba(43,168,162,0.30)',
                    }}
                >
                    {/* Decorative circles */}
                    <div className="absolute -right-8 -top-8 w-40 h-40 rounded-full" style={{ background: 'rgba(255,255,255,0.07)' }} />
                    <div className="absolute right-16 bottom-0 w-20 h-20 rounded-full" style={{ background: 'rgba(255,210,63,0.12)' }} />

                    <div className="relative z-10">
                        <p className="text-white/70 text-xs font-semibold uppercase tracking-widest mb-1">
                            Sistem Informasi BHP
                        </p>
                        <h1 className="text-white text-2xl font-extrabold leading-tight" style={{ fontFamily: 'Outfit, sans-serif' }}>
                            Selamat datang 👋
                        </h1>
                        <p className="text-white/80 text-xs mt-1">
                            Politeknik Negeri Cilacap — Lab TPPL
                        </p>
                    </div>
                    <div
                        className="hidden sm:flex w-12 h-12 rounded-xl items-center justify-center flex-shrink-0 relative z-10"
                        style={{ background: 'rgba(255,210,63,0.25)', border: '1.5px solid rgba(255,210,63,0.40)' }}
                    >
                        <FlaskConical size={24} className="text-white" strokeWidth={2} />
                    </div>
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
                        <div
                            className="bg-white rounded-xl p-5 overflow-hidden relative"
                            style={{
                                borderTop: '1px solid rgba(43,168,162,0.20)',
                                borderRight: '1px solid rgba(43,168,162,0.20)',
                                borderBottom: '1px solid rgba(43,168,162,0.20)',
                                borderLeft: '6px solid #2BA8A2',
                                boxShadow: '0 4px 20px rgba(43,168,162,0.10)',
                            }}
                        >
                            <div className="flex items-center justify-between mb-5">
                                <div>
                                    <h2 className="text-sm font-extrabold text-text-primary" style={{ fontFamily: 'Outfit, sans-serif' }}>
                                        Tren Penggunaan BHP
                                    </h2>
                                    <p className="text-2xs text-text-secondary mt-0.5">Jumlah transaksi selesai dalam 6 bulan terakhir</p>
                                </div>
                                <div className="flex items-center gap-2 text-2xs font-semibold text-text-secondary select-none">
                                    <span
                                        className="flex items-center gap-1.5 px-2.5 py-1 rounded-full"
                                        style={{ background: 'rgba(43,168,162,0.10)', color: '#2BA8A2', border: '1px solid rgba(43,168,162,0.25)' }}
                                    >
                                        <TrendingUp size={10} strokeWidth={2.5} />
                                        Transaksi Selesai
                                    </span>
                                </div>
                            </div>

                            <div className="relative w-full select-none">
                                {labels.length > 0 ? (
                                    <div className="relative w-full">
                                        <svg viewBox={`0 0 ${svgWidth} ${svgHeight}`} className="w-full h-auto overflow-visible">
                                            <defs>
                                                <linearGradient id="chart-gradient" x1="0" y1="0" x2="0" y2="1">
                                                    <stop offset="0%"   stopColor="#2BA8A2" stopOpacity="0.30" />
                                                    <stop offset="100%" stopColor="#2BA8A2" stopOpacity="0.00" />
                                                </linearGradient>
                                            </defs>

                                            {/* Grid lines */}
                                            {yAxisTicks.map((tickVal, idx) => {
                                                const tickY = paddingTop + (idx / steps) * height;
                                                return (
                                                    <g key={idx}>
                                                        <line x1={paddingLeft} y1={tickY} x2={svgWidth - paddingRight} y2={tickY}
                                                            stroke="rgba(43,168,162,0.15)" strokeWidth="1" strokeDasharray="4 4" />
                                                        <text x={paddingLeft - 10} y={tickY + 3.5} textAnchor="end"
                                                            fill="#5A8A86" fontSize="10" fontWeight="600">
                                                            {tickVal}
                                                        </text>
                                                    </g>
                                                );
                                            })}

                                            {/* Gradient area */}
                                            {areaPath && <path d={areaPath} fill="url(#chart-gradient)" />}

                                            {/* Line */}
                                            {linePath && (
                                                <path d={linePath} fill="none" stroke="#2BA8A2" strokeWidth="3"
                                                    strokeLinecap="round" strokeLinejoin="round" />
                                            )}

                                            {/* X-axis labels */}
                                            {points.map((pt, i) => (
                                                <text key={i} x={pt.x} y={svgHeight - 4} textAnchor="middle"
                                                    fill="#5A8A86" fontSize="10" fontWeight="600">
                                                    {pt.label}
                                                </text>
                                            ))}

                                            {/* Active vertical line */}
                                            {activePoint && (
                                                <line x1={activePoint.x} y1={paddingTop} x2={activePoint.x} y2={paddingTop + height}
                                                    stroke="rgba(43,168,162,0.30)" strokeWidth="1.5" strokeDasharray="4 4" />
                                            )}

                                            {/* Active dot */}
                                            {activePoint && (
                                                <circle cx={activePoint.x} cy={activePoint.y} r="6"
                                                    fill="#FFFFFF" stroke="#2BA8A2" strokeWidth="3.5" />
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
                                                <div
                                                    className="px-3 py-1.5 rounded-xl text-center flex flex-col gap-0.5 min-w-[80px]"
                                                    style={{
                                                        background: '#0D3B38',
                                                        boxShadow: '0 4px 16px rgba(13,59,56,0.30)',
                                                        border: '1px solid rgba(255,255,255,0.10)',
                                                    }}
                                                >
                                                    <span className="text-[9px] font-semibold" style={{ color: 'rgba(255,255,255,0.60)' }}>
                                                        {activePoint.label}
                                                    </span>
                                                    <span className="text-xs font-extrabold text-white font-mono">
                                                        {activePoint.val} <span className="text-[9px] font-normal" style={{ color: 'rgba(255,255,255,0.60)' }}>Tx</span>
                                                    </span>
                                                </div>
                                                <div
                                                    className="w-2.5 h-2.5 mx-auto -mt-1.5 rotate-45"
                                                    style={{ background: '#0D3B38' }}
                                                />
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
                                    <div
                                        className="absolute inset-0 flex flex-col items-center justify-center text-center z-10 select-none rounded-lg"
                                        style={{ background: 'rgba(239,248,247,0.85)', backdropFilter: 'blur(2px)' }}
                                    >
                                        <div
                                            className="w-10 h-10 rounded-full flex items-center justify-center mb-2"
                                            style={{ background: 'rgba(43,168,162,0.12)', boxShadow: '0 4px 12px rgba(43,168,162,0.20)' }}
                                        >
                                            <BarChart3 size={20} style={{ color: '#2BA8A2' }} />
                                        </div>
                                        <p className="text-xs font-extrabold text-text-primary" style={{ fontFamily: 'Outfit, sans-serif' }}>
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
                            <div
                                className="bg-white rounded-xl overflow-hidden"
                                style={{ border: '1px solid rgba(43,168,162,0.20)', boxShadow: '0 4px 20px rgba(43,168,162,0.08)' }}
                            >
                                <div className="px-5 pt-4 pb-3">
                                    <div className="flex items-center justify-between mb-3">
                                        <SectionTitle icon={Clock} title="Transaksi Terbaru" />
                                        <Link
                                            href="/admin/pengajuan"
                                            className="text-xs font-bold flex items-center gap-1 transition-colors hover:gap-1.5"
                                            style={{ color: '#2BA8A2' }}
                                        >
                                            Semua <ArrowRight size={11} />
                                        </Link>
                                    </div>
                                </div>
                                <div className="divide-y" style={{ borderColor: 'rgba(43,168,162,0.12)' }}>
                                    {recentPengajuan?.length > 0 ? (
                                        recentPengajuan.slice(0, 5).map(p => (
                                            <Link
                                                key={p.id}
                                                href={`/admin/pengajuan/${p.id}`}
                                                className="block px-5 py-3 hover:bg-teal/5 transition-colors"
                                            >
                                                <div className="flex items-start justify-between gap-3">
                                                    <div className="min-w-0 flex-1">
                                                        <p className="text-xs font-bold font-mono mb-0.5" style={{ color: '#2BA8A2' }}>
                                                            {p.kode_pengajuan}
                                                        </p>
                                                        <p className="text-sm font-semibold text-text-primary truncate">{p.user?.name}</p>
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
                            <div
                                className="bg-white rounded-xl overflow-hidden"
                                style={{
                                    borderLeft: '6px solid #EF6C4A',
                                    border: '1px solid rgba(239,108,74,0.20)',
                                    boxShadow: '0 4px 20px rgba(239,108,74,0.10)',
                                }}
                            >
                                <div className="px-5 pt-4 pb-3">
                                    <div className="flex items-center justify-between mb-3">
                                        <SectionTitle icon={AlertTriangle} title="Stok Kritis" iconColor="#EF6C4A" />
                                        <Link href="/admin/bahan" className="text-xs font-bold flex items-center gap-1" style={{ color: '#EF6C4A' }}>
                                            Semua <ArrowRight size={11} />
                                        </Link>
                                    </div>
                                </div>
                                <div className="divide-y" style={{ borderColor: 'rgba(239,108,74,0.12)' }}>
                                    {stokKritis?.length > 0 ? (
                                        stokKritis.slice(0, 5).map(b => (
                                            <div key={b.id} className="flex items-center justify-between px-5 py-2.5 hover:bg-coral/5 transition-colors">
                                                <div className="min-w-0 flex-1">
                                                    <p className="text-sm font-semibold text-text-primary truncate">{b.nama_bahan}</p>
                                                    <p className="text-xs text-text-secondary">Min: {b.minimal_stok} {b.satuan?.nama}</p>
                                                </div>
                                                <span
                                                    className="text-sm font-extrabold ml-3 px-2.5 py-0.5 rounded-full"
                                                    style={{
                                                        color: '#EF6C4A',
                                                        background: 'rgba(239,108,74,0.10)',
                                                        fontFamily: 'Outfit, sans-serif',
                                                    }}
                                                >
                                                    {b.stok}
                                                </span>
                                            </div>
                                        ))
                                    ) : (
                                        <div className="px-5 py-8 text-center text-xs text-text-secondary">
                                            Semua stok bahan dalam kondisi aman. ✅
                                        </div>
                                    )}
                                </div>
                            </div>
                        )}

                        {/* Ketua Jurusan: Top Bahan */}
                        {role === 'ketua_jurusan' && (
                            <div
                                className="bg-white rounded-xl overflow-hidden"
                                style={{ border: '1px solid rgba(43,168,162,0.20)', boxShadow: '0 4px 20px rgba(43,168,162,0.08)' }}
                            >
                                <div className="px-5 pt-4 pb-3">
                                    <div className="flex items-center justify-between mb-3">
                                        <SectionTitle icon={BarChart3} title="Bahan Terpopuler" />
                                        <span
                                            className="text-[9px] font-bold uppercase tracking-widest px-2 py-0.5 rounded-full"
                                            style={{ background: 'rgba(43,168,162,0.10)', color: '#2BA8A2' }}
                                        >
                                            6 Bulan
                                        </span>
                                    </div>
                                </div>
                                <div className="divide-y" style={{ borderColor: 'rgba(43,168,162,0.12)' }}>
                                    {topBahan?.length > 0 ? (
                                        topBahan.slice(0, 5).map((tb, idx) => {
                                            const rankColors = ['#FFD23F', '#C0C0C0', '#EF6C4A', '#2BA8A2', '#5DADE2'];
                                            return (
                                                <div key={tb.bahan_id} className="flex items-center justify-between px-5 py-2.5 hover:bg-teal/5 transition-colors">
                                                    <div className="flex items-center gap-3 min-w-0 flex-1">
                                                        <span
                                                            className="w-6 h-6 rounded-full flex items-center justify-center text-xs font-extrabold flex-shrink-0"
                                                            style={{
                                                                background: `${rankColors[idx]}20`,
                                                                color: rankColors[idx],
                                                                border: `1.5px solid ${rankColors[idx]}40`,
                                                                fontFamily: 'Outfit, sans-serif',
                                                            }}
                                                        >
                                                            {idx + 1}
                                                        </span>
                                                        <div className="min-w-0 flex-1">
                                                            <p className="text-sm font-semibold text-text-primary truncate">{tb.bahan?.nama_bahan || 'Tidak Diketahui'}</p>
                                                            <p className="text-xs text-text-secondary truncate">{tb.bahan?.kode_bahan}</p>
                                                        </div>
                                                    </div>
                                                    <span className="text-sm font-extrabold ml-3" style={{ color: '#2BA8A2', fontFamily: 'Outfit, sans-serif' }}>
                                                        {tb.total_pakai}
                                                    </span>
                                                </div>
                                            );
                                        })
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
                            <div
                                className="bg-white rounded-xl overflow-hidden"
                                style={{ border: '1px solid rgba(43,168,162,0.20)', boxShadow: '0 4px 20px rgba(43,168,162,0.08)' }}
                            >
                                <div className="px-5 pt-4 pb-3">
                                    <div className="flex items-center justify-between mb-3">
                                        <SectionTitle icon={ClipboardList} title="Pengajuan Terbaru" />
                                        <Link
                                            href="/mahasiswa/pengajuan"
                                            className="text-xs font-bold flex items-center gap-1"
                                            style={{ color: '#2BA8A2' }}
                                        >
                                            Semua <ArrowRight size={11} />
                                        </Link>
                                    </div>
                                </div>
                                <div className="divide-y" style={{ borderColor: 'rgba(43,168,162,0.12)' }}>
                                    {recentPengajuan?.length > 0 ? (
                                        recentPengajuan.slice(0, 5).map(p => (
                                            <Link
                                                key={p.id}
                                                href={`/mahasiswa/pengajuan/${p.id}`}
                                                className="block px-5 py-3 hover:bg-teal/5 transition-colors"
                                            >
                                                <div className="flex items-start justify-between gap-3">
                                                    <div className="min-w-0 flex-1">
                                                        <p className="text-xs font-bold font-mono mb-0.5" style={{ color: '#2BA8A2' }}>
                                                            {p.kode_pengajuan}
                                                        </p>
                                                        <p className="text-sm font-semibold text-text-primary truncate">{p.mata_kuliah || 'Mandiri'}</p>
                                                        <p className="text-xs text-text-secondary mt-0.5">{p.tanggal_pakai}</p>
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
