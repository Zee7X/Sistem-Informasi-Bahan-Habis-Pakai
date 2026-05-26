import { Head, useForm } from '@inertiajs/react';
import { FlaskConical, Eye, EyeOff } from 'lucide-react';
import { useState } from 'react';

export default function Login({ status }) {
    const { data, setData, post, processing, errors } = useForm({
        email: '',
        password: '',
        remember: false,
    });

    const [showPassword, setShowPassword] = useState(false);

    const submit = (e) => {
        e.preventDefault();
        post('/login');
    };

    return (
        <>
            <Head title="Login" />
            <div className="min-h-screen flex bg-dark-bg">
                {/* Left Panel — ilustrasi / branding */}
                <div className="hidden lg:flex flex-col items-center justify-center flex-1 bg-dark-card border-r border-border p-12 relative overflow-hidden">
                    {/* Decorative violet glow */}
                    <div className="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-96 h-96 bg-violet/5 rounded-full blur-3xl pointer-events-none" />
                    <div className="absolute top-1/4 right-1/4 w-48 h-48 bg-violet/8 rounded-full blur-2xl pointer-events-none" />

                    <div className="relative z-10 text-center max-w-sm">
                        <div className="w-16 h-16 bg-violet/10 border border-violet/20 rounded-2xl flex items-center justify-center mx-auto mb-6">
                            <FlaskConical size={30} className="text-violet" />
                        </div>
                        <h1 className="text-2xl font-semibold text-text-primary tracking-tight mb-3">
                            Sistem BHP Laboratorium
                        </h1>
                        <p className="text-sm text-text-secondary leading-relaxed">
                            Platform manajemen bahan habis pakai laboratorium<br />
                            Politeknik Negeri Cilacap
                        </p>

                        {/* Feature list */}
                        <div className="mt-10 text-left space-y-3">
                            {[
                                'Pengajuan BHP digital & terekam',
                                'Monitoring stok real-time',
                                'Laporan konsumsi per semester',
                                'Approval multi-level terstruktur',
                            ].map((f) => (
                                <div key={f} className="flex items-center gap-2.5 text-sm text-text-secondary">
                                    <div className="w-1.5 h-1.5 rounded-full bg-violet flex-shrink-0" />
                                    {f}
                                </div>
                            ))}
                        </div>
                    </div>

                    <p className="absolute bottom-6 text-2xs text-text-secondary/50">
                        © {new Date().getFullYear()} TPPL — Politeknik Negeri Cilacap
                    </p>
                </div>

                {/* Right Panel — form login */}
                <div className="flex flex-col items-center justify-center w-full lg:w-[400px] px-8 lg:px-12">
                    {/* Mobile logo */}
                    <div className="lg:hidden flex items-center gap-2.5 mb-10">
                        <div className="w-8 h-8 bg-violet/10 border border-violet/20 rounded-lg flex items-center justify-center">
                            <FlaskConical size={16} className="text-violet" />
                        </div>
                        <span className="text-sm font-semibold text-text-primary">BHP Laboratorium</span>
                    </div>

                    <div className="w-full max-w-sm">
                        <div className="mb-8">
                            <h2 className="text-xl font-semibold text-text-primary mb-1">Masuk ke akun</h2>
                            <p className="text-sm text-text-secondary">Masukkan email dan password Anda</p>
                        </div>

                        {/* Status message */}
                        {status && (
                            <div className="mb-4 text-sm text-success bg-success/10 border border-success/20 rounded px-3 py-2">
                                {status}
                            </div>
                        )}

                        <form onSubmit={submit} className="space-y-4">
                            {/* Email */}
                            <div>
                                <label className="block text-xs font-medium text-text-secondary mb-1.5">
                                    Email
                                </label>
                                <input
                                    id="email"
                                    type="email"
                                    value={data.email}
                                    onChange={(e) => setData('email', e.target.value)}
                                    autoFocus
                                    autoComplete="email"
                                    placeholder="email@bhp.com"
                                    className={`input ${errors.email ? 'border-error focus:border-error' : ''}`}
                                />
                                {errors.email && (
                                    <p className="mt-1.5 text-xs text-error">{errors.email}</p>
                                )}
                            </div>

                            {/* Password */}
                            <div>
                                <div className="flex items-center justify-between mb-1.5">
                                    <label className="text-xs font-medium text-text-secondary">
                                        Password
                                    </label>
                                    <a
                                        href="/forgot-password"
                                        className="text-xs text-text-secondary hover:text-violet transition-colors"
                                    >
                                        Lupa password?
                                    </a>
                                </div>
                                <div className="relative">
                                    <input
                                        id="password"
                                        type={showPassword ? 'text' : 'password'}
                                        value={data.password}
                                        onChange={(e) => setData('password', e.target.value)}
                                        autoComplete="current-password"
                                        placeholder="••••••••"
                                        className={`input pr-9 ${errors.password ? 'border-error focus:border-error' : ''}`}
                                    />
                                    <button
                                        type="button"
                                        onClick={() => setShowPassword(s => !s)}
                                        className="absolute right-2.5 top-1/2 -translate-y-1/2 text-text-secondary hover:text-text-primary transition-colors"
                                    >
                                        {showPassword ? <EyeOff size={14} /> : <Eye size={14} />}
                                    </button>
                                </div>
                                {errors.password && (
                                    <p className="mt-1.5 text-xs text-error">{errors.password}</p>
                                )}
                            </div>

                            {/* Remember me */}
                            <label className="flex items-center gap-2 cursor-pointer group">
                                <input
                                    type="checkbox"
                                    checked={data.remember}
                                    onChange={(e) => setData('remember', e.target.checked)}
                                    className="w-3.5 h-3.5 rounded-sm border-border bg-dark-card checked:bg-violet checked:border-violet accent-violet"
                                />
                                <span className="text-xs text-text-secondary group-hover:text-text-primary transition-colors">
                                    Ingat saya
                                </span>
                            </label>

                            {/* Submit */}
                            <button
                                type="submit"
                                disabled={processing}
                                className="btn-primary w-full justify-center h-9 mt-2 disabled:opacity-60 disabled:cursor-not-allowed"
                            >
                                {processing ? (
                                    <>
                                        <svg className="animate-spin w-3.5 h-3.5" fill="none" viewBox="0 0 24 24">
                                            <circle className="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" strokeWidth="4" />
                                            <path className="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z" />
                                        </svg>
                                        Memproses...
                                    </>
                                ) : 'Masuk'}
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </>
    );
}
