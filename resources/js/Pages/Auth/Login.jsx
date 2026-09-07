import { Head, useForm } from '@inertiajs/react';
import { FlaskConical, Eye, EyeOff, Mail, Lock } from 'lucide-react';
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
            <Head title="Login — BHP Lab" />

            <div className="min-h-screen flex items-center justify-center bg-dark-bg px-4 py-10">
                <div className="w-full max-w-[400px]">

                    {/* Card */}
                    <div className="card shadow-modal px-8 pt-8 pb-8">
                        {/* Header */}
                        <div className="text-center mb-7">
                            <div className="flex justify-center mb-4">
                                <div className="w-12 h-12 rounded-lg bg-teal flex items-center justify-center">
                                    <FlaskConical size={24} className="text-white" strokeWidth={2} />
                                </div>
                            </div>
                            <h1 className="text-xl font-semibold text-text-primary font-display">BHP Lab</h1>
                            <p className="text-xs text-text-secondary mt-1.5">
                                Sistem Informasi Bahan Habis Pakai
                            </p>
                            <p className="text-xs text-text-secondary">
                                Politeknik Negeri Cilacap
                            </p>
                        </div>

                        {/* Status message */}
                        {status && (
                            <div className="mb-5 text-xs px-3.5 py-2.5 rounded-md bg-success/10 text-success font-medium">
                                {status}
                            </div>
                        )}

                        <form onSubmit={submit} className="space-y-4">

                            {/* Email Field */}
                            <div>
                                <label htmlFor="email" className="block text-xs font-medium text-text-primary mb-1.5">
                                    Alamat Email
                                </label>
                                <div className="relative">
                                    <div className="absolute left-3 top-1/2 -translate-y-1/2 text-text-secondary pointer-events-none">
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
                                        className={`input pl-10 h-11 ${errors.email ? 'input-error' : ''}`}
                                    />
                                </div>
                                {errors.email && (
                                    <p className="mt-1.5 text-xs text-error">{errors.email}</p>
                                )}
                            </div>

                            {/* Password Field */}
                            <div>
                                <label htmlFor="password" className="block text-xs font-medium text-text-primary mb-1.5">
                                    Kata Sandi
                                </label>
                                <div className="relative">
                                    <div className="absolute left-3 top-1/2 -translate-y-1/2 text-text-secondary pointer-events-none">
                                        <Lock size={15} />
                                    </div>
                                    <input
                                        id="password"
                                        type={showPassword ? 'text' : 'password'}
                                        value={data.password}
                                        onChange={(e) => setData('password', e.target.value)}
                                        autoComplete="current-password"
                                        placeholder="••••••••"
                                        className={`input pl-10 pr-11 h-11 ${errors.password ? 'input-error' : ''}`}
                                    />
                                    <button
                                        type="button"
                                        onClick={() => setShowPassword(s => !s)}
                                        className="absolute right-3 top-1/2 -translate-y-1/2 text-text-secondary hover:text-text-primary transition-colors cursor-pointer"
                                        title={showPassword ? 'Sembunyikan kata sandi' : 'Tampilkan kata sandi'}
                                    >
                                        {showPassword ? <EyeOff size={15} /> : <Eye size={15} />}
                                    </button>
                                </div>
                                {errors.password && (
                                    <p className="mt-1.5 text-xs text-error">{errors.password}</p>
                                )}
                            </div>

                            {/* Remember me */}
                            <div className="flex items-center pt-1">
                                <label className="flex items-center gap-2 cursor-pointer">
                                    <input
                                        type="checkbox"
                                        checked={data.remember}
                                        onChange={(e) => setData('remember', e.target.checked)}
                                        className="w-4 h-4 rounded cursor-pointer accent-teal"
                                    />
                                    <span className="text-xs text-text-secondary">
                                        Ingat saya
                                    </span>
                                </label>
                            </div>

                            {/* Submit */}
                            <button
                                type="submit"
                                disabled={processing}
                                className="btn-primary w-full h-11 mt-1"
                            >
                                {processing ? (
                                    <div className="flex items-center gap-2">
                                        <svg className="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24">
                                            <circle className="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" strokeWidth="4" />
                                            <path className="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z" />
                                        </svg>
                                        <span>Memproses...</span>
                                    </div>
                                ) : (
                                    'Masuk'
                                )}
                            </button>
                        </form>
                    </div>

                    {/* Footer */}
                    <p className="text-center text-2xs text-text-secondary mt-6">
                        © {new Date().getFullYear()} Laboratorium TPPL — Politeknik Negeri Cilacap
                    </p>
                </div>
            </div>
        </>
    );
}
