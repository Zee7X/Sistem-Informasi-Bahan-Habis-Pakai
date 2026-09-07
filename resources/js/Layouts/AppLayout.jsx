import { useState, useEffect } from 'react';
import { Link, usePage, router } from '@inertiajs/react';
import {
    LayoutDashboard, FlaskConical, PackagePlus, ClipboardList,
    BarChart3, Users, Tag, BookOpen, ClipboardCheck,
    FileText, ChevronLeft, ChevronRight, LogOut, Menu, X,
    HelpCircle, Keyboard,
} from 'lucide-react';

const navConfig = {
    admin: [
        {
            section: 'Transaksi',
            items: [
                { label: 'Dashboard',       href: '/dashboard',             icon: LayoutDashboard, route: 'dashboard' },
                { label: 'Pengajuan BHP',   href: '/admin/pengajuan',       icon: ClipboardList,   route: 'admin.pengajuan' },
                { label: 'Bahan Masuk',     href: '/admin/bahan-masuk',     icon: PackagePlus,     route: 'admin.bahan-masuk' },
                { label: 'Stock Opname',    href: '/admin/stock-opname',    icon: ClipboardCheck,  route: 'admin.stock-opname' },
            ],
        },
        {
            section: 'Master Data',
            items: [
                { label: 'Master Bahan',    href: '/admin/bahan',           icon: FlaskConical,    route: 'admin.bahan' },
                { label: 'Modul Praktikum', href: '/admin/modul-praktikum', icon: BookOpen,        route: 'admin.modul-praktikum' },
                { label: 'Satuan',          href: '/admin/satuan',          icon: Tag,             route: 'admin.satuan' },
                { label: 'Kelola Users',    href: '/admin/users',           icon: Users,           route: 'admin.users' },
            ],
        },
        {
            section: 'Laporan & Log',
            items: [
                { label: 'Laporan BHP',     href: '/admin/laporan',         icon: BarChart3,       route: 'admin.laporan' },
                { label: 'Log Stok',        href: '/admin/log-stok',        icon: FileText,        route: 'admin.log-stok' },
            ],
        },
    ],
    mahasiswa: [
        {
            section: 'BHP',
            items: [
                { label: 'Dashboard',       href: '/dashboard',                  icon: LayoutDashboard, route: 'dashboard' },
                { label: 'Katalog Bahan',   href: '/mahasiswa/katalog',          icon: FlaskConical,    route: 'mahasiswa.katalog' },
                { label: 'Pengajuan Saya',  href: '/mahasiswa/pengajuan',        icon: ClipboardList,   route: 'mahasiswa.pengajuan' },
            ],
        },
    ],
    ketua_jurusan: [
        {
            section: 'Monitoring',
            items: [
                { label: 'Dashboard',       href: '/dashboard',                  icon: LayoutDashboard, route: 'dashboard' },
                { label: 'Transaksi BHP',   href: '/kjur/transaksi',             icon: ClipboardList,   route: 'kjur.transaksi' },
                { label: 'Data Bahan',      href: '/kjur/bahan',                 icon: FlaskConical,    route: 'kjur.bahan' },
                { label: 'Approval Belanja',href: '/kjur/bahan-masuk',           icon: PackagePlus,     route: 'kjur.bahan-masuk' },
            ],
        },
        {
            section: 'Laporan',
            items: [
                { label: 'Rekap Semester',  href: '/kjur/laporan/rekap',         icon: FileText,        route: 'kjur.laporan' },
            ],
        },
    ],
};

function NavItem({ item, collapsed }) {
    const { url } = usePage();
    const path = url.split('?')[0];
    const isActive = path === item.href || path.startsWith(item.href + '/');
    const Icon = item.icon;

    return (
        <Link
            href={item.href}
            className={`
                ${isActive ? 'nav-item-active' : 'nav-item'}
                ${collapsed ? 'justify-center px-0 w-10 mx-auto' : ''}
            `}
            title={collapsed ? item.label : undefined}
        >
            <Icon size={16} strokeWidth={2} />
            {!collapsed && <span>{item.label}</span>}
        </Link>
    );
}

export default function AppLayout({ children, title }) {
    const { auth } = usePage().props;
    const role = auth?.user?.role ?? 'mahasiswa';
    const navGroups = navConfig[role] ?? navConfig.mahasiswa;
    const [collapsed, setCollapsed] = useState(false);
    const [mobileOpen, setMobileOpen] = useState(false);

    const [switcherOpen, setSwitcherOpen] = useState(false);
    const [helpModalOpen, setHelpModalOpen] = useState(false);
    const [shortcutsModalOpen, setShortcutsModalOpen] = useState(false);

    const user = auth?.user;

    const initials = user?.name
        ? user.name.split(' ').map(n => n[0]).slice(0, 2).join('').toUpperCase()
        : '??';

    const roleLabel = {
        admin:          'Laboran / Admin',
        mahasiswa:      'Mahasiswa',
        ketua_jurusan:  'Ketua Jurusan',
    }[role] ?? role;

    // Keyboard navigation shortcuts
    useEffect(() => {
        let lastKey = '';
        const handleKeyDown = (e) => {
            if (['INPUT', 'TEXTAREA', 'SELECT'].includes(document.activeElement.tagName)) {
                return;
            }

            if (e.key === '?') {
                e.preventDefault();
                setShortcutsModalOpen(true);
                return;
            }

            if (e.key === 'Escape') {
                setShortcutsModalOpen(false);
                setHelpModalOpen(false);
                setSwitcherOpen(false);
                return;
            }

            if (lastKey === 'g') {
                if (e.key === 'd') {
                    e.preventDefault();
                    router.visit('/dashboard');
                } else if (e.key === 'p') {
                    e.preventDefault();
                    const path = role === 'admin' ? '/admin/pengajuan' : (role === 'ketua_jurusan' ? '/kjur/transaksi' : '/mahasiswa/pengajuan');
                    router.visit(path);
                } else if (e.key === 'b') {
                    e.preventDefault();
                    const path = role === 'admin' ? '/admin/bahan' : (role === 'ketua_jurusan' ? '/kjur/bahan' : '/mahasiswa/katalog');
                    router.visit(path);
                } else if (e.key === 'm' && role === 'admin') {
                    e.preventDefault();
                    router.visit('/admin/modul-praktikum');
                }
                lastKey = '';
            } else if (e.key === 'g') {
                lastKey = 'g';
                setTimeout(() => {
                    if (lastKey === 'g') lastKey = '';
                }, 1000);
            }
        };

        window.addEventListener('keydown', handleKeyDown);
        return () => window.removeEventListener('keydown', handleKeyDown);
    }, [role]);

    const SidebarContent = () => (
        <div className="flex flex-col h-full">

            {/* Brand */}
            <div className={`h-14 flex items-center gap-2.5 border-b border-border flex-shrink-0 px-4 ${collapsed ? 'justify-center px-0' : ''}`}>
                <div className="w-8 h-8 rounded-md bg-teal flex items-center justify-center flex-shrink-0">
                    <FlaskConical size={16} className="text-white" strokeWidth={2} />
                </div>
                {!collapsed && (
                    <div className="min-w-0">
                        <p className="text-sm font-bold text-text-primary leading-none font-display">BHP Lab</p>
                        <p className="text-3xs font-medium text-text-secondary uppercase tracking-wider mt-1 leading-none">
                            Politeknik Negeri Cilacap
                        </p>
                    </div>
                )}
            </div>

            {/* Navigation */}
            <nav className="flex-1 overflow-y-auto scrollbar-hidden px-2 py-4 space-y-5">
                {navGroups.map((group) => (
                    <div key={group.section}>
                        {!collapsed && (
                            <p className="section-header px-3 mb-1.5">{group.section}</p>
                        )}
                        <div className="space-y-0.5">
                            {group.items.map((item) => (
                                <NavItem key={item.href} item={item} collapsed={collapsed} />
                            ))}
                        </div>
                    </div>
                ))}
            </nav>

            {/* Bottom Actions */}
            <div className="flex-shrink-0 border-t border-border p-2 space-y-0.5">
                <button
                    onClick={() => setHelpModalOpen(true)}
                    className={`nav-item w-full ${collapsed ? 'justify-center px-0 w-10 mx-auto' : ''}`}
                    title="Bantuan & FAQ"
                >
                    <HelpCircle size={15} />
                    {!collapsed && <span>Bantuan</span>}
                </button>
                <button
                    onClick={() => setShortcutsModalOpen(true)}
                    className={`nav-item w-full ${collapsed ? 'justify-center px-0 w-10 mx-auto' : ''}`}
                    title="Pintasan Keyboard"
                >
                    <Keyboard size={15} />
                    {!collapsed && <span>Pintasan Keyboard</span>}
                </button>
            </div>
        </div>
    );

    return (
        <div className="flex h-screen overflow-hidden bg-dark-bg">

            {/* Desktop Sidebar */}
            <aside
                className={`hidden lg:flex flex-col flex-shrink-0 bg-white border-r border-border transition-all duration-200 relative ${
                    collapsed ? 'w-16' : 'w-60'
                }`}
            >
                <SidebarContent />
                {/* Collapse toggle */}
                <button
                    onClick={() => setCollapsed(c => !c)}
                    className="absolute bottom-20 -right-3 w-6 h-6 rounded-full border border-border bg-white flex items-center justify-center text-text-secondary hover:text-teal hover:border-teal transition-colors z-10 shadow-sm"
                    title={collapsed ? 'Perluas sidebar' : 'Ciutkan sidebar'}
                >
                    {collapsed ? <ChevronRight size={12} strokeWidth={2} /> : <ChevronLeft size={12} strokeWidth={2} />}
                </button>
            </aside>

            {/* Mobile Overlay */}
            {mobileOpen && (
                <div
                    className="fixed inset-0 z-40 bg-black/50 lg:hidden"
                    onClick={() => setMobileOpen(false)}
                />
            )}

            {/* Mobile Sidebar */}
            <aside
                className={`fixed left-0 top-0 bottom-0 z-50 w-60 bg-white border-r border-border flex flex-col lg:hidden transition-transform duration-200 ${
                    mobileOpen ? 'translate-x-0' : '-translate-x-full'
                }`}
            >
                <SidebarContent />
            </aside>

            {/* Main Content Area */}
            <div className="flex flex-col flex-1 min-w-0 overflow-hidden">

                {/* Topbar */}
                <header className="flex items-center gap-4 px-6 h-14 flex-shrink-0 justify-between bg-white border-b border-border">

                    {/* Left: Mobile toggle + Title */}
                    <div className="flex items-center gap-3 min-w-0 flex-1">
                        <button
                            className="lg:hidden nav-item w-9 h-9 justify-center px-0 flex-shrink-0"
                            onClick={() => setMobileOpen(o => !o)}
                            title="Menu"
                        >
                            {mobileOpen ? <X size={16} /> : <Menu size={16} />}
                        </button>
                        <h1 className="lg:hidden text-sm font-semibold text-text-primary truncate font-display">
                            {title}
                        </h1>
                    </div>

                    {/* Right: User */}
                    <div className="flex items-center gap-3 flex-shrink-0">

                        {/* User Profile Dropdown */}
                        <div className="relative">
                            {switcherOpen && (
                                <div className="fixed inset-0 z-40 bg-transparent" onClick={() => setSwitcherOpen(false)} />
                            )}

                            <button
                                onClick={() => setSwitcherOpen(!switcherOpen)}
                                className="flex items-center gap-2.5 hover:bg-dark-surface pl-3 pr-2 py-1.5 rounded-md border border-transparent hover:border-border select-none cursor-pointer transition-colors text-left relative z-50"
                            >
                                <div className="hidden sm:block text-right">
                                    <p className="text-xs font-semibold text-text-primary leading-none truncate max-w-[140px]">
                                        {user?.name}
                                    </p>
                                    <p className="text-2xs text-text-secondary mt-0.5 leading-none">{roleLabel}</p>
                                </div>
                                <div className="w-7 h-7 rounded-full bg-teal flex items-center justify-center font-semibold text-xs text-white flex-shrink-0">
                                    {initials}
                                </div>
                            </button>

                            {/* Dropdown */}
                            {switcherOpen && (
                                <div className="absolute right-0 mt-2 w-56 py-1 flex flex-col z-50 rounded-md border border-border bg-white shadow-modal animate-slide-up">
                                    <div className="px-3 py-2.5 mb-1 border-b border-border">
                                        <p className="text-xs font-semibold text-text-primary truncate">
                                            {user?.name}
                                        </p>
                                        <p className="text-2xs text-text-secondary truncate mt-0.5">{user?.email}</p>
                                    </div>
                                    <Link
                                        href="/logout"
                                        method="post"
                                        as="button"
                                        className="flex items-center gap-2 px-3 py-2 text-xs text-error hover:bg-error/10 rounded-sm transition-colors text-left w-full cursor-pointer font-medium"
                                        onClick={() => setSwitcherOpen(false)}
                                    >
                                        <LogOut size={13} />
                                        <span>Keluar</span>
                                    </Link>
                                </div>
                            )}
                        </div>
                    </div>
                </header>

                {/* Page Content */}
                <main className="flex-1 overflow-y-auto">
                    {children}
                </main>
            </div>

            {/* ── MODALS ───────────────────────────────────── */}

            {/* Help Modal */}
            {helpModalOpen && (
                <div className="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 animate-slide-up">
                    <div onClick={() => setHelpModalOpen(false)} className="absolute inset-0" />
                    <div className="relative z-10 card-surface shadow-modal max-w-md w-full max-h-[85vh] overflow-y-auto">
                        <div className="flex items-center justify-between px-5 py-4 border-b border-border">
                            <h3 className="text-sm font-semibold text-text-primary flex items-center gap-2 font-display">
                                <HelpCircle size={16} className="text-teal" />
                                Bantuan & Informasi
                            </h3>
                            <button
                                onClick={() => setHelpModalOpen(false)}
                                className="w-7 h-7 rounded-md flex items-center justify-center text-text-secondary hover:text-text-primary hover:bg-dark-surface transition-colors cursor-pointer"
                            >
                                <X size={14} />
                            </button>
                        </div>
                        <div className="p-5 space-y-4 text-xs text-text-secondary">
                            <div>
                                <h4 className="font-semibold text-text-primary mb-1.5 text-xs">Tentang BHP Lab</h4>
                                <p className="leading-relaxed">
                                    Sistem Informasi Bahan Habis Pakai (BHP) Politeknik Negeri Cilacap membantu mahasiswa, laboran (admin), dan ketua jurusan mendokumentasikan pemakaian bahan secara akurat dan transparan.
                                </p>
                            </div>
                            <div>
                                <h4 className="font-semibold text-text-primary mb-1.5 text-xs">Panduan Alur BHP</h4>
                                <p className="leading-relaxed">
                                    Mahasiswa mengajukan bahan melalui katalog. Admin menyetujui (<span className="text-teal font-medium">Approved</span>) dan memotong stok setelah bahan diserahkan secara fisik (<span className="text-success font-medium">Completed</span>).
                                </p>
                            </div>
                            <div>
                                <h4 className="font-semibold text-text-primary mb-1.5 text-xs">Hubungi Administrator</h4>
                                <p className="leading-relaxed">
                                    Email: <span className="text-text-primary font-medium">admin@lab.ac.id</span><br />
                                    Kunjungi Unit Laboratorium TPPL PNC untuk info lebih lanjut.
                                </p>
                            </div>
                        </div>
                        <div className="px-5 py-3 border-t border-border flex justify-end">
                            <button onClick={() => setHelpModalOpen(false)} className="btn-primary btn-sm cursor-pointer">
                                Tutup
                            </button>
                        </div>
                    </div>
                </div>
            )}

            {/* Keyboard Shortcuts Modal */}
            {shortcutsModalOpen && (
                <div className="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 animate-slide-up">
                    <div onClick={() => setShortcutsModalOpen(false)} className="absolute inset-0" />
                    <div className="relative z-10 card-surface shadow-modal max-w-sm w-full max-h-[85vh] overflow-y-auto">
                        <div className="flex items-center justify-between px-5 py-4 border-b border-border">
                            <h3 className="text-sm font-semibold text-text-primary flex items-center gap-2 font-display">
                                <Keyboard size={16} className="text-teal" />
                                Pintasan Keyboard
                            </h3>
                            <button
                                onClick={() => setShortcutsModalOpen(false)}
                                className="w-7 h-7 rounded-md flex items-center justify-center text-text-secondary hover:text-text-primary hover:bg-dark-surface transition-colors cursor-pointer"
                            >
                                <X size={14} />
                            </button>
                        </div>
                        <div className="p-5 space-y-3 text-xs text-text-secondary">
                            <p className="leading-relaxed">Gunakan tombol keyboard berikut di luar input form untuk navigasi cepat:</p>
                            <div className="space-y-2">
                                {[
                                    { label: 'Ke Dashboard',           keys: ['g', 'd'] },
                                    { label: 'Ke Pengajuan BHP',       keys: ['g', 'p'] },
                                    { label: 'Ke Master / Katalog Bahan', keys: ['g', 'b'] },
                                    ...(role === 'admin' ? [{ label: 'Ke Modul Praktikum', keys: ['g', 'm'] }] : []),
                                ].map((item, i) => (
                                    <div key={i} className="flex items-center justify-between">
                                        <span>{item.label}</span>
                                        <div className="flex items-center gap-1">
                                            {item.keys.map((k, ki) => (
                                                <span key={k} className="flex items-center gap-1">
                                                    <kbd className="kbd-hint">{k}</kbd>
                                                    {ki < item.keys.length - 1 && <span className="text-text-secondary/50">→</span>}
                                                </span>
                                            ))}
                                        </div>
                                    </div>
                                ))}
                                <div className="flex items-center justify-between pt-2 border-t border-border">
                                    <span>Buka Pintasan Ini</span>
                                    <kbd className="kbd-hint">?</kbd>
                                </div>
                                <div className="flex items-center justify-between">
                                    <span>Tutup Menu / Modal</span>
                                    <kbd className="kbd-hint">Esc</kbd>
                                </div>
                            </div>
                        </div>
                        <div className="px-5 py-3 border-t border-border flex justify-end">
                            <button onClick={() => setShortcutsModalOpen(false)} className="btn-primary btn-sm cursor-pointer">
                                Tutup
                            </button>
                        </div>
                    </div>
                </div>
            )}

        </div>
    );
}
