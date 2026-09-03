import { useState, useEffect } from 'react';
import { Link, usePage, router } from '@inertiajs/react';
import {
    LayoutDashboard, FlaskConical, PackagePlus, ClipboardList,
    BarChart3, Users, Tag, BookOpen, ClipboardCheck,
    FileText, ChevronLeft, ChevronRight, LogOut, Menu, X,
    HelpCircle, Keyboard,
    Bell, History
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
                { label: 'Log Stok',        href: '/admin/log-stok',        icon: History,         route: 'admin.log-stok' },
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
            <Icon
                size={16}
                className={isActive ? 'text-teal' : 'text-text-secondary'}
                strokeWidth={isActive ? 2.5 : 2}
            />
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

    // UI Modals & Dropdown States
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
        <div className="flex flex-col h-full relative">

            {/* Brand Logo */}
            <div className={`px-4 py-5 border-b border-border flex-shrink-0 flex items-center gap-3 ${collapsed ? 'justify-center px-0' : ''}`}>
                {/* Icon */}
                <div
                    className="w-9 h-9 rounded-xl flex items-center justify-center flex-shrink-0"
                    style={{
                        background: 'linear-gradient(135deg, #2BA8A2 0%, #3CC4BD 100%)',
                        boxShadow: '0 4px 12px rgba(43,168,162,0.40)',
                    }}
                >
                    <FlaskConical size={18} className="text-white" strokeWidth={2.5} />
                </div>
                {!collapsed && (
                    <div className="min-w-0">
                        <p className="text-sm font-extrabold text-text-primary leading-none tracking-tight" style={{ fontFamily: 'Outfit, sans-serif' }}>
                            BHP Lab
                        </p>
                        <p className="text-[9px] font-semibold text-text-secondary uppercase tracking-widest mt-1 leading-none">
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
                            <p className="section-header mx-1 mb-2">{group.section}</p>
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

    /* ── Modal base styling shared ──────────────────────── */
    const ModalBackdrop = ({ onClose, children }) => (
        <div
            className="fixed inset-0 z-50 flex items-center justify-center p-4 animate-slide-up"
            style={{ background: 'rgba(13,59,56,0.55)', backdropFilter: 'blur(4px)' }}
        >
            <div onClick={onClose} className="absolute inset-0" />
            <div className="relative z-10">{children}</div>
        </div>
    );

    const ModalCard = ({ children, className = '' }) => (
        <div
            className={`bg-white rounded-xl overflow-hidden flex flex-col ${className}`}
            style={{ boxShadow: '0 20px 60px rgba(43,168,162,0.20), 0 4px 16px rgba(0,0,0,0.08)' }}
        >
            {children}
        </div>
    );

    const ModalHeader = ({ icon: Icon, title, onClose }) => (
        <div
            className="flex items-center justify-between px-5 py-4"
            style={{ background: 'linear-gradient(135deg, #EFF8F7, #E8F6F5)', borderBottom: '2px dashed rgba(43,168,162,0.25)' }}
        >
            <h3 className="text-sm font-extrabold text-text-primary flex items-center gap-2" style={{ fontFamily: 'Outfit, sans-serif' }}>
                <Icon size={16} className="text-teal" />
                {title}
            </h3>
            <button
                onClick={onClose}
                className="w-7 h-7 rounded-full flex items-center justify-center text-text-secondary hover:text-coral hover:bg-coral/10 transition-colors cursor-pointer"
            >
                <X size={14} />
            </button>
        </div>
    );

    const ModalFooter = ({ onClose, label = 'Tutup' }) => (
        <div className="px-5 py-3 flex justify-end" style={{ background: '#F8FFFE', borderTop: '1px solid #C8E6E4' }}>
            <button onClick={onClose} className="btn-primary btn-sm cursor-pointer">
                {label}
            </button>
        </div>
    );

    return (
        <div className="flex h-screen overflow-hidden" style={{ background: '#EFF8F7' }}>

            {/* Desktop Sidebar */}
            <aside
                className={`hidden lg:flex flex-col flex-shrink-0 bg-white border-r border-border transition-all duration-200 relative ${
                    collapsed ? 'w-14' : 'w-[232px]'
                }`}
                style={{ boxShadow: '4px 0 20px rgba(43,168,162,0.07)' }}
            >
                <SidebarContent />
                {/* Collapse toggle */}
                <button
                    onClick={() => setCollapsed(c => !c)}
                    className="absolute bottom-24 -right-3.5 w-7 h-7 rounded-full border-2 border-border flex items-center justify-center text-text-secondary hover:text-teal hover:border-teal transition-colors z-10 bg-white"
                    style={{ boxShadow: '0 2px 8px rgba(43,168,162,0.15)' }}
                >
                    {collapsed ? <ChevronRight size={12} strokeWidth={2.5} /> : <ChevronLeft size={12} strokeWidth={2.5} />}
                </button>
            </aside>

            {/* Mobile Overlay */}
            {mobileOpen && (
                <div
                    className="fixed inset-0 z-40 lg:hidden"
                    style={{ background: 'rgba(13,59,56,0.50)', backdropFilter: 'blur(4px)' }}
                    onClick={() => setMobileOpen(false)}
                />
            )}

            {/* Mobile Sidebar */}
            <aside
                className={`fixed left-0 top-0 bottom-0 z-50 w-[232px] bg-white border-r border-border flex flex-col lg:hidden transition-transform duration-200 ${
                    mobileOpen ? 'translate-x-0' : '-translate-x-full'
                }`}
                style={{ boxShadow: '4px 0 20px rgba(43,168,162,0.10)' }}
            >
                <SidebarContent />
            </aside>

            {/* Main Content Area */}
            <div className="flex flex-col flex-1 min-w-0 overflow-hidden">

                {/* Topbar */}
                <header
                    className="flex items-center gap-4 px-6 h-14 flex-shrink-0 justify-between bg-white border-b border-border relative"
                    style={{ boxShadow: '0 2px 12px rgba(43,168,162,0.07)' }}
                >
                    {/* Teal accent stripe */}
                    <div
                        className="absolute top-0 left-0 right-0 h-0.5"
                        style={{ background: 'linear-gradient(90deg, #2BA8A2, #FFD23F, #EF6C4A, #2BA8A2)' }}
                    />

                    {/* Left: Mobile toggle + Title */}
                    <div className="flex items-center gap-3 min-w-0 flex-1">
                        <button
                            className="lg:hidden nav-item w-9 h-9 justify-center px-0 flex-shrink-0"
                            onClick={() => setMobileOpen(o => !o)}
                        >
                            {mobileOpen ? <X size={16} /> : <Menu size={16} />}
                        </button>
                        <h1 className="lg:hidden text-sm font-extrabold text-text-primary truncate" style={{ fontFamily: 'Outfit, sans-serif' }}>
                            {title}
                        </h1>
                    </div>

                    {/* Right: Workspace badge + Bell + User */}
                    <div className="flex items-center gap-3 flex-shrink-0">

                        {/* Workspace Pill */}
                        <div
                            className="hidden md:flex items-center gap-2 px-3 py-1 rounded-full border border-border bg-dark-bg"
                            style={{ boxShadow: '0 1px 4px rgba(43,168,162,0.12)' }}
                        >
                            <span
                                className="text-[9px] font-extrabold uppercase tracking-wider px-2 py-0.5 rounded-full text-white"
                                style={{ background: 'linear-gradient(135deg, #2BA8A2, #3CC4BD)', fontFamily: 'Outfit, sans-serif' }}
                            >
                                Lab
                            </span>
                            <span className="text-xs font-bold text-text-primary">BHP Lab</span>
                        </div>

                        <div className="hidden md:block w-px h-5 bg-border" />

                        {/* Notification Bell */}
                        <button
                            className="w-9 h-9 rounded-full border border-border flex items-center justify-center text-text-secondary hover:text-teal hover:border-teal transition-colors relative"
                            style={{ background: '#FFF8E7' }}
                            title="Notifikasi"
                        >
                            <Bell size={15} />
                            <span
                                className="absolute top-1.5 right-1.5 w-2 h-2 rounded-full border-2 border-white"
                                style={{ background: '#EF6C4A' }}
                            />
                        </button>

                        {/* User Profile Dropdown */}
                        <div className="relative">
                            {switcherOpen && (
                                <div className="fixed inset-0 z-40 bg-transparent" onClick={() => setSwitcherOpen(false)} />
                            )}

                            <button
                                onClick={() => setSwitcherOpen(!switcherOpen)}
                                className="flex items-center gap-2.5 hover:bg-teal/5 px-2.5 py-1 rounded-full border border-border select-none cursor-pointer transition-colors text-left relative z-50"
                                style={{ background: '#FAFFFE' }}
                            >
                                <div className="hidden sm:block text-right">
                                    <p className="text-xs font-bold text-text-primary leading-none truncate max-w-[110px]" style={{ fontFamily: 'Outfit, sans-serif' }}>
                                        {user?.name}
                                    </p>
                                    <p className="text-[10px] text-text-secondary mt-0.5 leading-none">{roleLabel}</p>
                                </div>
                                {/* Avatar */}
                                <div
                                    className="w-7 h-7 rounded-full flex items-center justify-center font-extrabold text-xs text-white flex-shrink-0"
                                    style={{
                                        background: 'linear-gradient(135deg, #2BA8A2 0%, #1E8C86 100%)',
                                        boxShadow: '0 2px 8px rgba(43,168,162,0.40)',
                                        fontFamily: 'Outfit, sans-serif',
                                    }}
                                >
                                    {initials}
                                </div>
                            </button>

                            {/* Dropdown */}
                            {switcherOpen && (
                                <div
                                    className="absolute right-0 mt-2 w-52 py-1 px-1 flex flex-col z-50 rounded-xl overflow-hidden border border-border animate-slide-up"
                                    style={{
                                        background: 'white',
                                        boxShadow: '0 12px 40px rgba(43,168,162,0.15), 0 2px 8px rgba(0,0,0,0.06)',
                                    }}
                                >
                                    <div className="px-3 py-2.5 mb-1" style={{ borderBottom: '2px dashed rgba(43,168,162,0.20)' }}>
                                        <p className="text-xs font-extrabold text-text-primary truncate" style={{ fontFamily: 'Outfit, sans-serif' }}>
                                            {user?.name}
                                        </p>
                                        <p className="text-[10px] text-text-secondary truncate mt-0.5">{user?.email}</p>
                                    </div>
                                    <Link
                                        href="/logout"
                                        method="post"
                                        as="button"
                                        className="flex items-center gap-2 px-3 py-2 text-xs text-coral/80 hover:text-coral hover:bg-coral/10 rounded-lg transition-colors text-left w-full cursor-pointer font-medium"
                                        onClick={() => setSwitcherOpen(false)}
                                    >
                                        <LogOut size={13} />
                                        <span>Keluar / Logout</span>
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
                <ModalBackdrop onClose={() => setHelpModalOpen(false)}>
                    <ModalCard className="max-w-md w-full">
                        <ModalHeader icon={HelpCircle} title="Bantuan & Informasi" onClose={() => setHelpModalOpen(false)} />
                        <div className="p-5 space-y-4 text-xs text-text-secondary">
                            <div>
                                <h4 className="font-extrabold text-text-primary mb-1.5" style={{ fontFamily: 'Outfit, sans-serif' }}>Tentang BHP Lab</h4>
                                <p className="leading-relaxed">
                                    Sistem Informasi Bahan Habis Pakai (BHP) Politeknik Negeri Cilacap membantu mahasiswa, laboran (admin), dan ketua jurusan mendokumentasikan pemakaian bahan secara akurat dan transparan.
                                </p>
                            </div>
                            <div>
                                <h4 className="font-extrabold text-text-primary mb-1.5" style={{ fontFamily: 'Outfit, sans-serif' }}>Panduan Alur BHP</h4>
                                <p className="leading-relaxed">
                                    Mahasiswa mengajukan bahan melalui katalog. Admin menyetujui (<span className="text-teal font-semibold">Approved</span>) dan memotong stok setelah bahan diserahkan secara fisik (<span className="text-success font-semibold">Completed</span>).
                                </p>
                            </div>
                            <div>
                                <h4 className="font-extrabold text-text-primary mb-1.5" style={{ fontFamily: 'Outfit, sans-serif' }}>Hubungi Administrator</h4>
                                <p className="leading-relaxed">
                                    Email: <span className="text-text-primary font-semibold">admin@lab.ac.id</span><br />
                                    Kunjungi Unit Laboratorium TPPL PNC untuk info lebih lanjut.
                                </p>
                            </div>
                        </div>
                        <ModalFooter onClose={() => setHelpModalOpen(false)} />
                    </ModalCard>
                </ModalBackdrop>
            )}

            {/* Keyboard Shortcuts Modal */}
            {shortcutsModalOpen && (
                <ModalBackdrop onClose={() => setShortcutsModalOpen(false)}>
                    <ModalCard className="max-w-sm w-full">
                        <ModalHeader icon={Keyboard} title="Pintasan Keyboard" onClose={() => setShortcutsModalOpen(false)} />
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
                                                <>
                                                    <kbd key={k} className="kbd-hint">{k}</kbd>
                                                    {ki < item.keys.length - 1 && <span key={`sep-${ki}`} className="text-text-secondary/50">→</span>}
                                                </>
                                            ))}
                                        </div>
                                    </div>
                                ))}
                                <div className="flex items-center justify-between pt-2" style={{ borderTop: '2px dashed rgba(43,168,162,0.20)' }}>
                                    <span>Buka Pintasan Ini</span>
                                    <kbd className="kbd-hint">?</kbd>
                                </div>
                                <div className="flex items-center justify-between">
                                    <span>Tutup Menu / Modal</span>
                                    <kbd className="kbd-hint">Esc</kbd>
                                </div>
                            </div>
                        </div>
                        <ModalFooter onClose={() => setShortcutsModalOpen(false)} />
                    </ModalCard>
                </ModalBackdrop>
            )}

        </div>
    );
}
