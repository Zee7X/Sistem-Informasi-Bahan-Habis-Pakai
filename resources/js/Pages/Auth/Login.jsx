import { Head, useForm } from '@inertiajs/react';
import { FlaskConical, Eye, EyeOff, Mail, Lock, ArrowRight } from 'lucide-react';
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
            <div className="min-h-screen flex items-center justify-center bg-dark-bg font-sans relative overflow-hidden px-4">
                {/* Background decorative glows */}
                <div className="absolute top-[-20%] left-[-10%] w-[600px] h-[600px] bg-violet/5 rounded-full blur-3xl pointer-events-none" />
                <div className="absolute bottom-[-20%] right-[-10%] w-[600px] h-[600px] bg-violet/5 rounded-full blur-3xl pointer-events-none" />

                <div className="w-full max-w-md relative z-10">
                    {/* Main Card */}
                    <div className="bg-white border border-border/80 rounded-2xl shadow-modal p-8 sm:p-10 relative overflow-hidden">
                        {/* Subtle inner top glow accent */}
                        <div className="absolute top-0 left-0 right-0 h-[3px] bg-gradient-to-r from-violet/20 via-violet to-violet/20" />

                        {/* Logo & Header */}
                        <div className="flex flex-col items-center text-center mb-8">
                            <div className="w-12 h-12 rounded-2xl bg-violet/10 border border-violet/20 flex items-center justify-center shadow-sm mb-4">
                                <FlaskConical size={24} className="text-violet animate-pulse" />
                            </div>
                            <h1 className="text-xl font-bold text-text-primary tracking-tight">
                                Sistem BHP Laboratorium
                            </h1>
                            <p className="text-xs text-text-secondary mt-1">
                                Politeknik Negeri Cilacap
                            </p>
                        </div>

                        {status && (
                            <div className="mb-6 text-xs text-emerald-600 bg-emerald-500/10 border border-emerald-500/20 rounded-lg p-3 flex items-center gap-2">
                                <span className="w-1.5 h-1.5 rounded-full bg-emerald-500" />
                                {status}
                            </div>
                        )}

                        <form onSubmit={submit} className="space-y-4">
                            {/* Email */}
                            <div>
                                <label className="block text-xs font-semibold text-text-secondary mb-1.5">
                                    Alamat Email
                                </label>
                                <div className="relative">
                                    <div className="absolute left-3 top-1/2 -translate-y-1/2 text-text-secondary/60">
                                        <Mail size={15} />
                                    </div>
                                    <input
                                        id="email"
                                        type="email"
                                        value={data.email}
                                        onChange={(e) => setData('email', e.target.value)}
                                        autoFocus
                                        autoComplete="email"
                                        placeholder="nama@domain.com"
                                        className={`input pl-9 h-10 ${errors.email ? 'border-error focus:border-error' : ''}`}
                                    />
                                </div>
                                {errors.email && (
                                    <p className="mt-1.5 text-[11px] text-error font-medium">{errors.email}</p>
                                )}
                            </div>

                            {/* Password */}
                            <div>
                                <div className="flex items-center justify-between mb-1.5">
                                    <label className="text-xs font-semibold text-text-secondary">
                                        Kata Sandi
                                    </label>
                                    <a
                                        href="/forgot-password"
                                        className="text-xs text-violet hover:text-violet-hover font-medium transition-colors"
                                    >
                                        Lupa password?
                                    </a>
                                </div>
                                <div className="relative">
                                    <div className="absolute left-3 top-1/2 -translate-y-1/2 text-text-secondary/60">
                                        <Lock size={15} />
                                    </div>
                                    <input
                                        id="password"
                                        type={showPassword ? 'text' : 'password'}
                                        value={data.password}
                                        onChange={(e) => setData('password', e.target.value)}
                                        autoComplete="current-password"
                                        placeholder="••••••••"
                                        className={`input pl-9 pr-10 h-10 ${errors.password ? 'border-error focus:border-error' : ''}`}
                                    />
                                    <button
                                        type="button"
                                        onClick={() => setShowPassword(s => !s)}
                                        className="absolute right-3 top-1/2 -translate-y-1/2 text-text-secondary hover:text-text-primary transition-colors"
                                    >
                                        {showPassword ? <EyeOff size={15} /> : <Eye size={15} />}
                                    </button>
                                </div>
                                {errors.password && (
                                    <p className="mt-1.5 text-[11px] text-error font-medium">{errors.password}</p>
                                )}
                            </div>

                            {/* Remember me & Options */}
                            <div className="flex items-center justify-between pt-1">
                                <label className="flex items-center gap-2 cursor-pointer group">
                                    <input
                                        type="checkbox"
                                        checked={data.remember}
                                        onChange={(e) => setData('remember', e.target.checked)}
                                        className="w-4 h-4 rounded border-border bg-dark-card checked:bg-violet checked:border-violet accent-violet cursor-pointer"
                                    />
                                    <span className="text-xs text-text-secondary group-hover:text-text-primary transition-colors">
                                        Ingat saya
                                    </span>
                                </label>
                            </div>

                            {/* Submit */}
                            <button
                                type="submit"
                                disabled={processing}
                                className="btn-primary w-full justify-center h-10 mt-3 rounded-lg text-sm font-semibold shadow-lg shadow-violet/10 hover:shadow-violet/20 disabled:opacity-60 disabled:cursor-not-allowed transition-all"
                            >
                                {processing ? (
                                    <div className="flex items-center gap-2">
                                        <svg className="animate-spin w-4 h-4 text-white" fill="none" viewBox="0 0 24 24">
                                            <circle className="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" strokeWidth="4" />
                                            <path className="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z" />
                                        </svg>
                                        <span>Memproses...</span>
                                    </div>
                                ) : (
                                    <div className="flex items-center gap-1.5">
                                        <span>Masuk ke Akun</span>
                                        <ArrowRight size={15} />
                                    </div>
                                )}
                            </button>
                        </form>
                    </div>

                    {/* Footer copyright outside card */}
                    <p className="text-center text-[10px] text-text-secondary/60 mt-6">
                        © {new Date().getFullYear()} TPPL — Politeknik Negeri Cilacap. All rights reserved.
                    </p>
                </div>
            </div>
        </>
    );
}
