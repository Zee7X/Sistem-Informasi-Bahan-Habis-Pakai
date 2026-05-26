import { useState, useEffect } from 'react';
import { Link, usePage, router } from '@inertiajs/react';
import {
    LayoutDashboard, FlaskConical, PackagePlus, ClipboardList,
    BarChart3, Users, Tag, BookOpen, ClipboardCheck,
    FileText, ChevronLeft, ChevronRight, LogOut, Menu, X,
    ChevronsUpDown, Settings, HelpCircle, Keyboard, Check, User,
    Bell, Search
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
            section: 'Laporan',
            items: [
                { label: 'Laporan',         href: '/admin/laporan',         icon: BarChart3,       route: 'admin.laporan' },
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
            className={`${isActive ? 'nav-item-active' : 'nav-item'} ${collapsed ? 'justify-center px-0 w-8 mx-auto' : ''}`}
            title={collapsed ? item.label : undefined}
        >
            <Icon size={15} className={isActive ? 'text-violet' : ''} />
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
    const [profileModalOpen, setProfileModalOpen] = useState(false);
    const [helpModalOpen, setHelpModalOpen] = useState(false);
    const [shortcutsModalOpen, setShortcutsModalOpen] = useState(false);

    const user = auth?.user;

    const initials = user?.name
        ? user.name.split(' ').map(n => n[0]).slice(0, 2).join('').toUpperCase()
        : '??';

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
                setProfileModalOpen(false);
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
            {/* Static Brand Logo */}
            <div className={`px-4 py-4 border-b border-border flex-shrink-0 flex items-center ${collapsed ? 'justify-center' : 'gap-2.5'}`}>
                <div className="w-8 h-8 rounded bg-violet flex items-center justify-center flex-shrink-0 shadow-violet">
                    <FlaskConical size={16} className="text-white" />
                </div>
                {!collapsed && (
                    <div className="min-w-0">
                        <p className="text-sm font-semibold text-text-primary leading-none truncate">BHP Laboratorium</p>
                        <p className="text-[8px] font-semibold text-text-secondary uppercase tracking-widest mt-1">Politeknik Negeri Cilacap</p>
                    </div>
                )}
            </div>

            {/* Navigation */}
            <nav className="flex-1 overflow-y-auto scrollbar-hidden px-2 py-3 space-y-4">
                {navGroups.map((group) => (
                    <div key={group.section}>
                        {!collapsed && (
                            <p className="section-header px-2 mb-1">{group.section}</p>
                        )}
                        <div className="space-y-0.5">
                            {group.items.map((item) => (
                                <NavItem key={item.href} item={item} collapsed={collapsed} />
                            ))}
                        </div>
                    </div>
                ))}
            </nav>

            {/* Bottom Actions: Settings, Help, Keyboard Shortcuts */}
            <div className="flex-shrink-0 border-t border-border p-2 space-y-1">
                <button
                    onClick={() => setProfileModalOpen(true)}
                    className={`nav-item w-full ${collapsed ? 'justify-center px-0 w-8 mx-auto' : ''}`}
                    title="Pengaturan Profil"
                >
                    <Settings size={14} />
                    {!collapsed && <span>Pengaturan</span>}
                </button>
                <button
                    onClick={() => setHelpModalOpen(true)}
                    className={`nav-item w-full ${collapsed ? 'justify-center px-0 w-8 mx-auto' : ''}`}
                    title="Bantuan & FAQ"
                >
                    <HelpCircle size={14} />
                    {!collapsed && <span>Bantuan</span>}
                </button>
                <button
                    onClick={() => setShortcutsModalOpen(true)}
                    className={`nav-item w-full ${collapsed ? 'justify-center px-0 w-8 mx-auto' : ''}`}
                    title="Pintasan Keyboard"
                >
                    <Keyboard size={14} />
                    {!collapsed && <span>Pintasan Keyboard</span>}
                </button>
            </div>
        </div>
    );

    return (
        <div className="flex h-screen bg-dark-bg overflow-hidden">
            {/* Desktop Sidebar */}
            <aside
                className={`hidden lg:flex flex-col flex-shrink-0 bg-dark-card border-r border-border transition-all duration-150 ${
                    collapsed ? 'w-12' : 'w-[220px]'
                }`}
            >
                <SidebarContent />
                {/* Collapse toggle */}
                <button
                    onClick={() => setCollapsed(c => !c)}
                    className="absolute bottom-20 -right-3 w-6 h-6 bg-dark-surface border border-border rounded-full flex items-center justify-center text-text-secondary hover:text-text-primary transition-colors z-10"
                    style={{ left: collapsed ? '34px' : '208px' }}
                >
                    {collapsed ? <ChevronRight size={12} /> : <ChevronLeft size={12} />}
                </button>
            </aside>

            {/* Mobile Overlay */}
            {mobileOpen && (
                <div
                    className="fixed inset-0 z-40 bg-black/60 backdrop-blur-sm lg:hidden"
                    onClick={() => setMobileOpen(false)}
                />
            )}

            {/* Mobile Sidebar */}
            <aside
                className={`fixed left-0 top-0 bottom-0 z-50 w-[220px] bg-dark-card border-r border-border flex flex-col lg:hidden transition-transform duration-150 ${
                    mobileOpen ? 'translate-x-0' : '-translate-x-full'
                }`}
            >
                <SidebarContent />
            </aside>

            {/* Main */}
            <div className="flex flex-col flex-1 min-w-0 overflow-hidden">
                {/* Topbar / Appbar */}
                <header className="flex items-center gap-4 px-6 h-14 border-b border-border bg-dark-card flex-shrink-0 justify-between">
                    {/* Left: Mobile Menu Toggle & Search Bar / Page Title */}
                    <div className="flex items-center gap-3 min-w-0 flex-1">
                        <button
                            className="lg:hidden nav-item w-8 h-8 justify-center px-0 flex-shrink-0"
                            onClick={() => setMobileOpen(o => !o)}
                        >
                            {mobileOpen ? <X size={16} /> : <Menu size={16} />}
                        </button>
                        
                        {/* Search Input bar mimicking reference */}
                        <div className="hidden lg:flex items-center gap-2 px-3 py-1.5 w-64 bg-dark-bg border border-border rounded-full text-xs text-text-secondary focus-within:border-violet focus-within:bg-white focus-within:shadow-violet-sm transition-all">
                            <Search size={14} className="text-text-secondary flex-shrink-0" />
                            <input 
                                type="text" 
                                placeholder="Cari bahan..." 
                                className="bg-transparent border-0 p-0 outline-none text-text-primary placeholder:text-text-secondary/70 text-xs w-full"
                            />
                        </div>
                        
                        {/* Page Title on Mobile or Desktop */}
                        <h1 className="lg:hidden text-sm font-semibold text-text-primary truncate">{title}</h1>
                    </div>

                    {/* Right: Workspace Badge, Notification, User Profile */}
                    <div className="flex items-center gap-3.5 flex-shrink-0">
                        {/* Workspace Badge (Large, matching reference) */}
                        <div className="hidden md:flex items-center border border-border rounded-full p-0.5 pr-2.5 bg-dark-bg shadow-sm">
                            <span className="bg-violet text-white text-[10px] font-bold uppercase tracking-wider px-2.5 py-0.5 rounded-full select-none">
                                Workspace
                            </span>
                            <span className="pl-2 text-xs font-semibold text-text-primary">
                                BHP Lab
                            </span>
                        </div>

                        {/* Spacer vertical line */}
                        <div className="hidden md:block w-px h-5 bg-border" />

                        {/* Notification Bell */}
                        <button 
                            className="w-8 h-8 rounded-full border border-border flex items-center justify-center text-text-secondary hover:text-text-primary hover:bg-dark-surface transition-colors relative"
                            title="Notifikasi"
                        >
                            <Bell size={15} />
                            <span className="absolute top-1 right-1 w-1.5 h-1.5 bg-error rounded-full" />
                        </button>

                        {/* User Profile dropdown wrapper */}
                        <div className="relative">
                            {/* click catcher for topbar dropdown */}
                            {switcherOpen && (
                                <div className="fixed inset-0 z-40 bg-transparent" onClick={() => setSwitcherOpen(false)} />
                            )}

                            <button
                                onClick={() => setSwitcherOpen(!switcherOpen)}
                                className="flex items-center gap-2.5 hover:bg-dark-surface px-2.5 py-1 rounded-full border border-border select-none cursor-pointer transition-colors text-left bg-dark-bg relative z-50 animate-in fade-in duration-200"
                            >
                                <div className="hidden sm:block text-right">
                                    <p className="text-xs font-semibold text-text-primary leading-none truncate max-w-[120px]">{user?.name}</p>
                                    <p className="text-[10px] text-text-secondary capitalize mt-0.5 leading-none">{role.replace('_', ' ')}</p>
                                </div>
                                <div className="w-7 h-7 rounded-full bg-violet/10 text-violet flex items-center justify-center font-semibold text-xs border border-violet/20 flex-shrink-0">
                                    {initials}
                                </div>
                            </button>

                            {/* Dropdown Menu floating from the profile card */}
                            {switcherOpen && (
                                <div className="absolute right-0 mt-2 bg-dark-card border border-border rounded-md shadow-modal w-52 py-1 px-1 flex flex-col z-50 animate-in fade-in slide-in-from-top-2 duration-100">
                                    <div className="px-2 py-1.5 border-b border-border/50 mb-1">
                                        <p className="text-xs font-bold text-text-primary truncate">{user?.name}</p>
                                        <p className="text-[10px] text-text-secondary truncate">{user?.email}</p>
                                    </div>
                                    <button
                                        onClick={() => {
                                            setSwitcherOpen(false);
                                            setProfileModalOpen(true);
                                        }}
                                        className="flex items-center gap-2 px-2.5 py-1.5 text-xs text-text-secondary hover:text-text-primary hover:bg-dark-surface rounded transition-colors text-left w-full cursor-pointer"
                                    >
                                        <User size={13} />
                                        <span>Profil Saya</span>
                                    </button>
                                    <Link
                                        href="/logout"
                                        method="post"
                                        as="button"
                                        className="flex items-center gap-2 px-2.5 py-1.5 text-xs text-error/80 hover:text-error hover:bg-error/10 rounded transition-colors text-left w-full cursor-pointer"
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

            {/* Modals */}
            {/* User Profile Modal */}
            {profileModalOpen && (
                <div className="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm animate-in fade-in duration-100">
                    <div className="card max-w-sm w-full bg-dark-surface border border-border shadow-modal rounded-md overflow-hidden flex flex-col animate-in zoom-in-95 duration-100">
                        <div className="flex items-center justify-between px-4 py-3 border-b border-border">
                            <h3 className="text-sm font-semibold text-text-primary flex items-center gap-2">
                                <User size={15} className="text-violet" />
                                Profil Pengguna
                            </h3>
                            <button
                                onClick={() => setProfileModalOpen(false)}
                                className="text-text-secondary hover:text-text-primary transition-colors cursor-pointer"
                            >
                                <X size={15} />
                            </button>
                        </div>
                        <div className="p-4 space-y-4">
                            <div className="flex flex-col items-center text-center pb-3 border-b border-border/40">
                                <div className="w-14 h-14 rounded-full bg-violet/20 flex items-center justify-center text-violet text-base font-bold mb-2">
                                    {initials}
                                </div>
                                <h4 className="text-sm font-semibold text-text-primary">{user?.name}</h4>
                                <p className="text-2xs text-text-secondary uppercase tracking-wider mt-0.5">{role.replace('_', ' ')}</p>
                            </div>
                            <div className="space-y-2.5 text-xs">
                                <div className="grid grid-cols-3 gap-2">
                                    <span className="text-text-secondary font-medium col-span-1">Email</span>
                                    <span className="text-text-primary col-span-2 break-all">{user?.email ?? '-'}</span>
                                </div>
                                {user?.nim && (
                                    <div className="grid grid-cols-3 gap-2">
                                        <span className="text-text-secondary font-medium col-span-1">{role === 'mahasiswa' ? 'NIM' : 'NIDN / NIP'}</span>
                                        <span className="text-text-primary col-span-2">{user.nim}</span>
                                    </div>
                                )}
                                {user?.kelas && (
                                    <div className="grid grid-cols-3 gap-2">
                                        <span className="text-text-secondary font-medium col-span-1">Kelas</span>
                                        <span className="text-text-primary col-span-2">{user.kelas}</span>
                                    </div>
                                )}
                                {user?.program_studi && (
                                    <div className="grid grid-cols-3 gap-2">
                                        <span className="text-text-secondary font-medium col-span-1">Prodi</span>
                                        <span className="text-text-primary col-span-2">{user.program_studi}</span>
                                    </div>
                                )}
                                {user?.angkatan && (
                                    <div className="grid grid-cols-3 gap-2">
                                        <span className="text-text-secondary font-medium col-span-1">Angkatan</span>
                                        <span className="text-text-primary col-span-2">{user.angkatan}</span>
                                    </div>
                                )}
                                {user?.no_telp && (
                                    <div className="grid grid-cols-3 gap-2">
                                        <span className="text-text-secondary font-medium col-span-1">No. Telp</span>
                                        <span className="text-text-primary col-span-2">{user.no_telp}</span>
                                    </div>
                                )}
                            </div>
                        </div>
                        <div className="px-4 py-3 bg-dark-card border-t border-border flex justify-end">
                            <button
                                onClick={() => setProfileModalOpen(false)}
                                className="btn-primary btn-sm cursor-pointer"
                            >
                                Tutup
                            </button>
                        </div>
                    </div>
                </div>
            )}

            {/* Help Modal */}
            {helpModalOpen && (
                <div className="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm animate-in fade-in duration-100">
                    <div className="card max-w-md w-full bg-dark-surface border border-border shadow-modal rounded-md overflow-hidden flex flex-col animate-in zoom-in-95 duration-100">
                        <div className="flex items-center justify-between px-4 py-3 border-b border-border">
                            <h3 className="text-sm font-semibold text-text-primary flex items-center gap-2">
                                <HelpCircle size={15} className="text-violet" />
                                Bantuan & Informasi
                            </h3>
                            <button
                                onClick={() => setHelpModalOpen(false)}
                                className="text-text-secondary hover:text-text-primary transition-colors cursor-pointer"
                            >
                                <X size={15} />
                            </button>
                        </div>
                        <div className="p-4 space-y-4 text-xs text-text-secondary">
                            <div>
                                <h4 className="font-semibold text-text-primary mb-1">Tentang BHP Lab</h4>
                                <p className="leading-relaxed">
                                    Sistem Informasi Bahan Habis Pakai (BHP) Politeknik Negeri Cilacap membantu mahasiswa, laboran (admin), dan ketua jurusan mendokumentasikan pemakaian bahan secara akurat dan transparan.
                                </p>
                            </div>
                            <div>
                                <h4 className="font-semibold text-text-primary mb-1">Panduan Alur BHP</h4>
                                <p className="leading-relaxed">
                                    Mahasiswa mengajukan bahan melalui katalog. Admin menyetujui (<span className="text-violet font-medium">Approved</span>) and memotong stok setelah bahan diserahkan secara fisik (<span className="text-success font-medium">Completed</span>).
                                </p>
                            </div>
                            <div>
                                <h4 className="font-semibold text-text-primary mb-1">Hubungi Administrator</h4>
                                <p className="leading-relaxed">
                                    Email: <span className="text-text-primary font-medium">admin@lab.ac.id</span><br />
                                    Kunjungi Unit Laboratorium TPPL PNC untuk info lebih lanjut.
                                </p>
                            </div>
                        </div>
                        <div className="px-4 py-3 bg-dark-card border-t border-border flex justify-end">
                            <button
                                onClick={() => setHelpModalOpen(false)}
                                className="btn-primary btn-sm cursor-pointer"
                            >
                                Tutup
                            </button>
                        </div>
                    </div>
                </div>
            )}

            {/* Keyboard Shortcuts Modal */}
            {shortcutsModalOpen && (
                <div className="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm animate-in fade-in duration-100">
                    <div className="card max-w-sm w-full bg-dark-surface border border-border shadow-modal rounded-md overflow-hidden flex flex-col animate-in zoom-in-95 duration-100">
                        <div className="flex items-center justify-between px-4 py-3 border-b border-border">
                            <h3 className="text-sm font-semibold text-text-primary flex items-center gap-2">
                                <Keyboard size={15} className="text-violet" />
                                Pintasan Keyboard
                            </h3>
                            <button
                                onClick={() => setShortcutsModalOpen(false)}
                                className="text-text-secondary hover:text-text-primary transition-colors cursor-pointer"
                            >
                                <X size={15} />
                            </button>
                        </div>
                        <div className="p-4 space-y-3.5 text-xs text-text-secondary">
                            <p>Gunakan tombol keyboard berikut di luar input form untuk navigasi cepat:</p>
                            <div className="space-y-2">
                                <div className="flex items-center justify-between">
                                    <span>Ke Dashboard</span>
                                    <div className="flex items-center gap-1">
                                        <kbd className="kbd-hint">g</kbd>
                                        <span>lalu</span>
                                        <kbd className="kbd-hint">d</kbd>
                                    </div>
                                </div>
                                <div className="flex items-center justify-between">
                                    <span>Ke Pengajuan BHP</span>
                                    <div className="flex items-center gap-1">
                                        <kbd className="kbd-hint">g</kbd>
                                        <span>lalu</span>
                                        <kbd className="kbd-hint">p</kbd>
                                    </div>
                                </div>
                                <div className="flex items-center justify-between">
                                    <span>Ke Master / Katalog Bahan</span>
                                    <div className="flex items-center gap-1">
                                        <kbd className="kbd-hint">g</kbd>
                                        <span>lalu</span>
                                        <kbd className="kbd-hint">b</kbd>
                                    </div>
                                </div>
                                {role === 'admin' && (
                                    <div className="flex items-center justify-between">
                                        <span>Ke Modul Praktikum</span>
                                        <div className="flex items-center gap-1">
                                            <kbd className="kbd-hint">g</kbd>
                                            <span>lalu</span>
                                            <kbd className="kbd-hint">m</kbd>
                                        </div>
                                    </div>
                                )}
                                <div className="flex items-center justify-between border-t border-border/40 pt-2">
                                    <span>Buka Pintasan Ini</span>
                                    <kbd className="kbd-hint">?</kbd>
                                </div>
                                <div className="flex items-center justify-between">
                                    <span>Tutup Menu / Modal</span>
                                    <kbd className="kbd-hint">Esc</kbd>
                                </div>
                            </div>
                        </div>
                        <div className="px-4 py-3 bg-dark-card border-t border-border flex justify-end">
                            <button
                                onClick={() => setShortcutsModalOpen(false)}
                                className="btn-primary btn-sm cursor-pointer"
                            >
                                Tutup
                            </button>
                        </div>
                    </div>
                </div>
            )}
        </div>
    );
}
