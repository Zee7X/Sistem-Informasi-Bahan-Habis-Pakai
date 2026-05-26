import { useState } from 'react';
import { Head, usePage, Link } from '@inertiajs/react';
import AppLayout from '@/Layouts/AppLayout';
import { 
    FlaskConical, Users, Clock, AlertTriangle, ArrowRight, 
    BarChart3, Check, ClipboardList, PackagePlus 
} from 'lucide-react';

function StatCard({ label, value, icon: Icon, trend, trendType = 'up', color = 'violet', footerType }) {
    const colors = {
        violet:  { bg: 'bg-violet/10',  text: 'text-violet',  border: 'border-violet/20' },
        success: { bg: 'bg-success/10', text: 'text-success', border: 'border-success/20' },
        warning: { bg: 'bg-warning/10', text: 'text-warning', border: 'border-warning/20' },
        error:   { bg: 'bg-error/10',   text: 'text-error',   border: 'border-error/20' },
    };
    const c = colors[color] || colors.violet;

    const trendClasses = trendType === 'up'
        ? 'bg-success/10 text-success'
        : trendType === 'down'
        ? 'bg-error/10 text-error'
        : 'bg-text-secondary/10 text-text-secondary';

    return (
        <div className="card p-5 flex flex-col justify-between min-h-[145px] hover:shadow-sm transition-all duration-200">
            <div className="flex justify-between items-start gap-4">
                <div className="min-w-0 flex-1">
                    <p className="text-2xs font-semibold text-text-secondary uppercase tracking-widest leading-none mb-2">{label}</p>
                    <div className="flex items-center gap-2 mt-1">
                        <span className="text-2xl font-bold text-text-primary leading-none tracking-tight">{value ?? 0}</span>
                        {trend && (
                            <span className={`text-[11px] font-semibold px-2 py-0.5 rounded-full select-none ${trendClasses}`}>
                                {trend}
                            </span>
                        )}
                    </div>
                </div>
                {/* Large Icon Badge */}
                <div className={`w-12 h-12 rounded-xl flex items-center justify-center flex-shrink-0 ${c.bg} ${c.text} border ${c.border} shadow-sm transition-transform hover:scale-105 duration-200`}>
                    <Icon size={22} strokeWidth={2} />
                </div>
            </div>
            
            {/* Footer representation matching the reference */}
            <div className="mt-4 pt-3 border-t border-border/40 flex items-center">
                {footerType === 'bar' && (
                    <div className="flex items-end gap-1 h-5 select-none w-full">
                        <div className="w-3.5 h-2 bg-violet/20 rounded-xs" />
                        <div className="w-3.5 h-3 bg-violet/20 rounded-xs" />
                        <div className="w-3.5 h-2 bg-violet/20 rounded-xs" />
                        <div className="w-3.5 h-4 bg-violet/20 rounded-xs" />
                        <div className="w-3.5 h-5 bg-violet/40 rounded-xs" />
                        <div className="w-3.5 h-6 bg-violet rounded-xs" />
                    </div>
                )}
                {footerType === 'sparkline-green' && (
                    <div className="h-5 select-none w-full">
                        <svg className="w-full h-full text-success" viewBox="0 0 100 30" fill="none" preserveAspectRatio="none">
                            <path d="M0 25 C 20 5, 40 35, 60 10 C 80 -5, 90 20, 100 8" stroke="currentColor" strokeWidth="2.5" strokeLinecap="round" strokeLinejoin="round" />
                        </svg>
                    </div>
                )}
                {footerType === 'sparkline-yellow' && (
                    <div className="h-5 select-none w-full">
                        <svg className="w-full h-full text-warning" viewBox="0 0 100 30" fill="none" preserveAspectRatio="none">
                            <path d="M0 10 C 15 25, 30 5, 45 20 C 60 30, 75 10, 100 25" stroke="currentColor" strokeWidth="2.5" strokeLinecap="round" strokeLinejoin="round" />
                        </svg>
                    </div>
                )}
                {footerType === 'status-kritis' && (
                    <div className="flex items-center gap-1.5 select-none w-full">
                        <span className={`w-1.5 h-1.5 rounded-full ${value > 0 ? 'bg-error animate-pulse' : 'bg-success'}`} />
                        <span className="text-3xs font-bold font-mono tracking-widest text-text-secondary uppercase">
                            {value > 0 ? 'STATUS: PERLU TINDAKAN' : 'STATUS: AMAN'}
                        </span>
                    </div>
                )}
            </div>
        </div>
    );
}

function StatusChip({ status }) {
    const map = {
        pending_review: { label: 'Pending',   cls: 'chip-warning' },
        approved:       { label: 'Approved',  cls: 'chip-violet' },
        completed:      { label: 'Selesai',   cls: 'chip-success' },
        rejected:       { label: 'Ditolak',   cls: 'chip-error' },
    };
    const s = map[status] ?? { label: status, cls: 'chip-neutral' };
    return <span className={`chip ${s.cls}`}>{s.label}</span>;
}

export default function Dashboard({ stats, stokKritis, recentPengajuan, chartData, topBahan }) {
    const { auth } = usePage().props;
    const role = auth?.user?.role;

    const getStatsConfig = () => {
        if (role === 'admin') {
            return [
                { label: 'Total Bahan', value: stats?.total_bahan, icon: FlaskConical, color: 'violet', trend: '+12%', trendType: 'up', footerType: 'bar' },
                { label: 'Mahasiswa Aktif', value: stats?.total_user, icon: Users, color: 'success', trend: '+8%', trendType: 'up', footerType: 'sparkline-green' },
                { label: 'Menunggu Review', value: stats?.pending_review, icon: Clock, color: 'warning', trend: stats?.pending_review > 0 ? `${stats.pending_review} pending` : 'Aman', trendType: stats?.pending_review > 0 ? 'down' : 'up', footerType: 'sparkline-yellow' },
                { label: 'Stok Kritis', value: stats?.stok_kritis, icon: AlertTriangle, color: 'error', trend: stats?.stok_kritis > 0 ? `${stats.stok_kritis} item` : 'Aman', trendType: stats?.stok_kritis > 0 ? 'down' : 'up', footerType: 'status-kritis' },
            ];
        }
        if (role === 'mahasiswa') {
            return [
                { label: 'Total Pengajuan', value: stats?.total_pengajuan, icon: ClipboardList, color: 'violet', trend: 'Riwayat', trendType: 'up', footerType: 'bar' },
                { label: 'Menunggu Review', value: stats?.pending_review, icon: Clock, color: 'warning', trend: 'Proses', trendType: 'neutral', footerType: 'sparkline-yellow' },
                { label: 'Disetujui', value: stats?.approved, icon: Check, color: 'success', trend: 'Disetujui', trendType: 'up', footerType: 'sparkline-green' },
                { label: 'Selesai', value: stats?.completed, icon: FlaskConical, color: 'violet', trend: 'Selesai', trendType: 'up', footerType: 'bar' },
            ];
        }
        if (role === 'ketua_jurusan') {
            return [
                { label: 'Total Transaksi', value: stats?.total_transaksi, icon: ClipboardList, color: 'violet', trend: '+15%', trendType: 'up', footerType: 'bar' },
                { label: 'Total Bahan', value: stats?.total_bahan, icon: FlaskConical, color: 'success', trend: 'Aktif', trendType: 'up', footerType: 'sparkline-green' },
                { label: 'Stok Kritis', value: stats?.stok_kritis, icon: AlertTriangle, color: 'error', trend: stats?.stok_kritis > 0 ? 'Perlu Order' : 'Aman', trendType: stats?.stok_kritis > 0 ? 'down' : 'up', footerType: 'status-kritis' },
                { label: 'Pending Belanja', value: stats?.pending_belanja, icon: PackagePlus, color: 'warning', trend: stats?.pending_belanja > 0 ? 'Review' : 'Selesai', trendType: stats?.pending_belanja > 0 ? 'down' : 'up', footerType: 'sparkline-yellow' },
            ];
        }
        return [];
    };

    const labels = chartData?.labels || [];
    const values = chartData?.values || [];
    const hasData = values.some(v => v > 0);
    const maxVal = values.length > 0 ? Math.max(...values) : 0;

    const [hoveredIndex, setHoveredIndex] = useState(null);

    // Y-axis tick calculations (guaranteed to be integers)
    const steps = 4;
    const displayMax = maxVal > 0 ? Math.max(steps, Math.ceil(maxVal / steps) * steps) : steps;
    const yAxisTicks = Array.from({ length: steps + 1 }, (_, idx) => (displayMax * (steps - idx)) / steps);

    // SVG coordinates config (wider aspect ratio to prevent excessive height on large screens)
    const svgWidth = 1000;
    const svgHeight = 160;
    const paddingLeft = 40;
    const paddingRight = 15;
    const paddingTop = 15;
    const paddingBottom = 25;

    const width = svgWidth - paddingLeft - paddingRight;
    const height = svgHeight - paddingTop - paddingBottom;

    // Coordinate points
    const points = labels.map((label, i) => {
        const val = values[i] ?? 0;
        const x = paddingLeft + (labels.length > 1 ? (i / (labels.length - 1)) * width : width / 2);
        const y = paddingTop + height - (val / displayMax) * height;
        return { x, y, label, val };
    });

    // Control point calculation for Catmull-Rom spline (smooth bezier curve)
    const getControlPoints = (p0, p1, p2, p3) => {
        const t = 0.18; // smooth tension
        const cp1x = p1.x + (p2.x - p0.x) * t;
        const cp1y = p1.y + (p2.y - p0.y) * t;
        const cp2x = p2.x - (p3.x - p1.x) * t;
        const cp2y = p2.y - (p3.y - p1.y) * t;
        return { cp1x, cp1y, cp2x, cp2y };
    };

    let linePath = '';
    let areaPath = '';

    if (points.length > 0) {
        linePath = `M ${points[0].x} ${points[0].y}`;
        for (let i = 0; i < points.length - 1; i++) {
            const p1 = points[i];
            const p2 = points[i + 1];
            const p0 = points[i - 1] || p1;
            const p3 = points[i + 2] || p2;
            const cp = getControlPoints(p0, p1, p2, p3);
            linePath += ` C ${cp.cp1x} ${cp.cp1y}, ${cp.cp2x} ${cp.cp2y}, ${p2.x} ${p2.y}`;
        }
        areaPath = `${linePath} L ${points[points.length - 1].x} ${paddingTop + height} L ${points[0].x} ${paddingTop + height} Z`;
    }

    const colWidth = points.length > 1 ? width / (points.length - 1) : width;
    const activeIdx = hoveredIndex !== null ? hoveredIndex : (points.length > 0 ? points.length - 1 : null);
    const activePoint = activeIdx !== null ? points[activeIdx] : null;

    const showRightPanel = (role === 'admin' && stokKritis?.length > 0) || (role === 'ketua_jurusan' && topBahan?.length > 0);

    return (
        <AppLayout title="Dashboard">
            <Head title="Dashboard" />
            <div className="p-5 space-y-6">

                {/* Stats Grid */}
                <div className="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4">
                    {getStatsConfig().map((cfg, idx) => (
                        <StatCard key={idx} {...cfg} />
                    ))}
                </div>

                <div className={`grid gap-6 ${showRightPanel ? 'xl:grid-cols-3' : 'xl:grid-cols-1'}`}>
                    {/* Chart Card */}
                    <div className={`card p-5 ${showRightPanel ? 'xl:col-span-2' : ''}`}>
                        <div className="flex items-center justify-between mb-6">
                            <div>
                                <h2 className="text-sm font-semibold text-text-primary">Tren Penggunaan BHP</h2>
                                <p className="text-2xs text-text-secondary mt-0.5">Jumlah transaksi selesai dalam 6 bulan terakhir</p>
                            </div>
                            <div className="flex items-center gap-3 text-2xs font-semibold text-text-secondary select-none">
                                <span className="flex items-center gap-1.5">
                                    <span className="w-2 h-2 rounded bg-violet" />
                                    Transaksi Selesai
                                </span>
                            </div>
                        </div>

                        <div className="relative w-full select-none">
                            {labels.length > 0 ? (
                                <div className="relative w-full">
                                    <svg 
                                        viewBox={`0 0 ${svgWidth} ${svgHeight}`} 
                                        className="w-full h-auto overflow-visible"
                                    >
                                        <defs>
                                            <linearGradient id="chart-gradient" x1="0" y1="0" x2="0" y2="1">
                                                <stop offset="0%" stopColor="#5E6AD2" stopOpacity="0.25" />
                                                <stop offset="100%" stopColor="#5E6AD2" stopOpacity="0.00" />
                                            </linearGradient>
                                        </defs>

                                        {/* Horizontal grid lines & Y-Axis Labels */}
                                        {yAxisTicks.map((tickVal, idx) => {
                                            const tickY = paddingTop + (idx / steps) * height;
                                            return (
                                                <g key={idx}>
                                                    <line 
                                                        x1={paddingLeft} 
                                                        y1={tickY} 
                                                        x2={svgWidth - paddingRight} 
                                                        y2={tickY} 
                                                        className="stroke-border/30" 
                                                        strokeWidth="1"
                                                    />
                                                    <text 
                                                        x={paddingLeft - 10} 
                                                        y={tickY + 3.5} 
                                                        textAnchor="end" 
                                                        className="text-[10px] font-semibold fill-text-secondary select-none"
                                                    >
                                                        {tickVal}
                                                    </text>
                                                </g>
                                            );
                                        })}

                                        {/* Gradient Area Fill */}
                                        {areaPath && (
                                            <path 
                                                d={areaPath} 
                                                fill="url(#chart-gradient)" 
                                            />
                                        )}

                                        {/* Smooth Path Stroke */}
                                        {linePath && (
                                            <path 
                                                d={linePath} 
                                                fill="none" 
                                                stroke="#5E6AD2" 
                                                strokeWidth="2.75" 
                                                strokeLinecap="round" 
                                                strokeLinejoin="round" 
                                            />
                                        )}

                                        {/* X-Axis Labels */}
                                        {points.map((pt, i) => (
                                            <text 
                                                key={i} 
                                                x={pt.x} 
                                                y={svgHeight - 4} 
                                                textAnchor="middle" 
                                                className="text-[10px] font-semibold fill-text-secondary select-none"
                                            >
                                                {pt.label}
                                            </text>
                                        ))}

                                        {/* Active Point Indicator Vertical Line */}
                                        {activePoint && (
                                            <line
                                                x1={activePoint.x}
                                                y1={paddingTop}
                                                x2={activePoint.x}
                                                y2={paddingTop + height}
                                                className="stroke-violet/20"
                                                strokeWidth="1.5"
                                                strokeDasharray="4 4"
                                            />
                                        )}

                                        {/* Active Point Circle Indicator */}
                                        {activePoint && (
                                            <circle
                                                cx={activePoint.x}
                                                cy={activePoint.y}
                                                r="5.5"
                                                fill="#FFFFFF"
                                                stroke="#5E6AD2"
                                                strokeWidth="3.5"
                                                className="drop-shadow-sm"
                                            />
                                        )}

                                        {/* Transparent Hover Trigger Rectangles */}
                                        {points.map((pt, i) => {
                                            const triggerWidth = i === 0 || i === points.length - 1 ? colWidth / 2 : colWidth;
                                            const triggerX = i === 0 ? pt.x : pt.x - colWidth / 2;
                                            return (
                                                <rect
                                                    key={i}
                                                    x={triggerX}
                                                    y={paddingTop}
                                                    width={triggerWidth}
                                                    height={height}
                                                    fill="transparent"
                                                    className="cursor-pointer"
                                                    onMouseEnter={() => setHoveredIndex(i)}
                                                    onMouseLeave={() => setHoveredIndex(null)}
                                                />
                                            );
                                        })}
                                    </svg>

                                    {/* Tooltip HTML Overlay positioned responsively with percentages */}
                                    {activePoint && (
                                        <div 
                                            className="absolute pointer-events-none transition-all duration-150 ease-out z-20"
                                            style={{
                                                left: `${(activePoint.x / svgWidth) * 100}%`,
                                                top: `${(activePoint.y / svgHeight) * 100}%`,
                                                transform: 'translate(-50%, -120%)'
                                            }}
                                        >
                                            <div className="bg-text-primary text-white px-2.5 py-1 rounded-md shadow-lg border border-white/10 text-center flex flex-col gap-0.5 min-w-[75px]">
                                                <span className="text-[9px] text-white/60 font-semibold">{activePoint.label}</span>
                                                <span className="text-2xs font-bold text-white font-mono leading-none py-0.5">
                                                    {activePoint.val} <span className="text-[9px] font-normal text-white/70">Tx</span>
                                                </span>
                                            </div>
                                            {/* Tooltip arrow/caret */}
                                            <div className="w-2.5 h-2.5 bg-text-primary rotate-45 mx-auto -mt-1.5 border-r border-b border-white/10" />
                                        </div>
                                    )}
                                </div>
                            ) : (
                                <div className="w-full h-36 flex items-center justify-center">
                                    <p className="text-xs text-text-secondary">Tidak ada data untuk ditampilkan</p>
                                </div>
                            )}

                            {/* Centered Empty State Overlay if count is 0 */}
                            {!hasData && labels.length > 0 && (
                                <div className="absolute inset-0 bg-white/70 backdrop-blur-[0.5px] flex flex-col items-center justify-center text-center z-15 select-none rounded-md">
                                    <div className="p-3 bg-violet/10 rounded-full text-violet mb-2">
                                        <BarChart3 size={20} />
                                    </div>
                                    <p className="text-xs font-semibold text-text-primary">Belum Ada Riwayat Transaksi</p>
                                    <p className="text-2xs text-text-secondary mt-0.5">Sistem belum mencatat transaksi selesai dalam 6 bulan terakhir</p>
                                </div>
                            )}
                        </div>
                    </div>

                    {/* Stok Kritis (Admin) */}
                    {role === 'admin' && stokKritis?.length > 0 && (
                        <div className="card h-fit">
                            <div className="flex items-center justify-between px-4 py-3 border-b border-border">
                                <h2 className="text-sm font-semibold text-text-primary flex items-center gap-1.5">
                                    <AlertTriangle size={14} className="text-error animate-pulse" /> Stok Kritis
                                </h2>
                                <Link href="/admin/bahan" className="text-xs text-text-secondary hover:text-violet transition-colors">
                                    Lihat semua
                                </Link>
                            </div>
                            <div className="divide-y divide-border">
                                {stokKritis.map(b => (
                                    <div key={b.id} className="flex items-center justify-between px-4 py-2.5">
                                        <div>
                                            <p className="text-sm text-text-primary font-medium">{b.nama_bahan}</p>
                                            <p className="text-xs text-text-secondary">Min: {b.minimal_stok} {b.satuan?.nama}</p>
                                        </div>
                                        <span className="text-error font-semibold text-sm">{b.stok} <span className="text-2xs font-normal text-error/60">{b.satuan?.nama}</span></span>
                                    </div>
                                ))}
                            </div>
                        </div>
                    )}

                    {/* Top Bahan (Ketua Jurusan) */}
                    {role === 'ketua_jurusan' && topBahan?.length > 0 && (
                        <div className="card h-fit">
                            <div className="flex items-center justify-between px-4 py-3 border-b border-border">
                                <h2 className="text-sm font-semibold text-text-primary">Bahan Terpopuler (6 Bulan)</h2>
                                <span className="text-3xs text-text-secondary uppercase tracking-widest font-mono select-none">Berdasarkan Jumlah</span>
                            </div>
                            <div className="divide-y divide-border">
                                {topBahan.map((tb, idx) => (
                                    <div key={tb.bahan_id} className="flex items-center justify-between px-4 py-2.5">
                                        <div className="flex items-center gap-3">
                                            <span className="w-5 h-5 rounded-full bg-violet/10 text-violet flex items-center justify-center text-xs font-bold select-none">
                                                {idx + 1}
                                            </span>
                                            <div>
                                                <p className="text-sm text-text-primary font-medium">{tb.bahan?.nama_bahan || 'Bahan Tidak Diketahui'}</p>
                                                <p className="text-xs text-text-secondary">{tb.bahan?.kode_bahan}</p>
                                            </div>
                                        </div>
                                        <span className="text-violet font-semibold text-sm">
                                            {tb.total_pakai} <span className="text-2xs font-normal text-text-secondary">{tb.bahan?.satuan?.nama}</span>
                                        </span>
                                    </div>
                                ))}
                            </div>
                        </div>
                    )}
                </div>

                {/* Pengajuan Terbaru */}
                {role === 'admin' && recentPengajuan?.length > 0 && (
                    <div className="card">
                        <div className="flex items-center justify-between px-4 py-3 border-b border-border">
                            <h2 className="text-sm font-semibold text-text-primary">Pengajuan Menunggu Review</h2>
                            <Link href="/admin/pengajuan" className="text-xs text-text-secondary hover:text-violet transition-colors flex items-center gap-1">
                                Lihat semua <ArrowRight size={11} />
                            </Link>
                        </div>
                        <div className="divide-y divide-border">
                            {recentPengajuan.map(p => (
                                <div key={p.id} className="issue-row">
                                    <span className="identifier">{p.kode_pengajuan}</span>
                                    <span className="text-sm text-text-primary flex-1 truncate">{p.user?.name}</span>
                                    <span className="text-xs text-text-secondary hidden sm:block">{p.mata_kuliah}</span>
                                    <StatusChip status={p.status} />
                                    <Link
                                        href={`/admin/pengajuan/${p.id}`}
                                        className="text-xs text-violet hover:text-violet-hover transition-colors ml-auto font-medium"
                                    >
                                        Review →
                                    </Link>
                                </div>
                            ))}
                        </div>
                    </div>
                )}

                {role === 'mahasiswa' && recentPengajuan?.length > 0 && (
                    <div className="card">
                        <div className="flex items-center justify-between px-4 py-3 border-b border-border">
                            <h2 className="text-sm font-semibold text-text-primary">Pengajuan Saya Terbaru</h2>
                            <Link href="/mahasiswa/pengajuan" className="text-xs text-text-secondary hover:text-violet transition-colors flex items-center gap-1">
                                Lihat semua <ArrowRight size={11} />
                            </Link>
                        </div>
                        <div className="divide-y divide-border">
                            {recentPengajuan.map(p => (
                                <div key={p.id} className="issue-row">
                                    <span className="identifier">{p.kode_pengajuan}</span>
                                    <span className="text-sm text-text-primary flex-1 truncate">{p.mata_kuliah || 'Mandiri'}</span>
                                    <span className="text-xs text-text-secondary hidden sm:block">{p.tanggal_pakai}</span>
                                    <StatusChip status={p.status} />
                                    <Link
                                        href={`/mahasiswa/pengajuan`}
                                        className="text-xs text-violet hover:text-violet-hover transition-colors ml-auto font-medium"
                                    >
                                        Detail →
                                    </Link>
                                </div>
                            ))}
                        </div>
                    </div>
                )}
            </div>
        </AppLayout>
    );
}

