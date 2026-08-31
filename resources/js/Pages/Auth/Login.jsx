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
            <Head title="Login — BHP Lab" />

            {/* Full page: Flip7 teal surface + decorative blobs */}
            <div
                className="min-h-screen flex items-center justify-center font-sans relative overflow-hidden px-4 py-10"
                style={{ background: '#EFF8F7' }}
            >
                {/* Background blobs */}
                <div
                    className="absolute -top-32 -left-32 w-96 h-96 rounded-full pointer-events-none"
                    style={{ background: 'rgba(43,168,162,0.12)', filter: 'blur(60px)' }}
                />
                <div
                    className="absolute -bottom-32 -right-32 w-96 h-96 rounded-full pointer-events-none"
                    style={{ background: 'rgba(255,210,63,0.15)', filter: 'blur(60px)' }}
                />
                <div
                    className="absolute top-1/3 right-10 w-48 h-48 rounded-full pointer-events-none"
                    style={{ background: 'rgba(239,108,74,0.08)', filter: 'blur(40px)' }}
                />

                {/* Card */}
                <div className="w-full max-w-[420px] relative z-10">
                    <div
                        className="bg-white rounded-2xl overflow-hidden"
                        style={{
                            boxShadow: '0 20px 60px rgba(43,168,162,0.18), 0 4px 16px rgba(0,0,0,0.06)',
                            border: '1px solid rgba(43,168,162,0.20)',
                        }}
                    >
                        {/* Rainbow top stripe */}
                        <div
                            className="h-1"
                            style={{ background: 'linear-gradient(90deg, #2BA8A2 0%, #FFD23F 50%, #EF6C4A 100%)' }}
                        />

                        {/* Header section with teal gradient */}
                        <div
                            className="px-8 pt-8 pb-7 text-center"
                            style={{ background: 'linear-gradient(180deg, #E8F6F5 0%, #FFFFFF 100%)' }}
                        >
                            {/* Logo icon */}
                            <div className="flex justify-center mb-5">
                                <div
                                    className="w-16 h-16 rounded-2xl flex items-center justify-center relative"
                                    style={{
                                        background: 'linear-gradient(135deg, #2BA8A2 0%, #3CC4BD 100%)',
                                        boxShadow: '0 8px 24px rgba(43,168,162,0.40), 0 2px 6px rgba(43,168,162,0.20)',
                                    }}
                                >
                                    {/* Gloss highlight */}
                                    <div
                                        className="absolute top-1 left-1 right-4 bottom-4 rounded-xl opacity-30"
                                        style={{ background: 'linear-gradient(135deg, rgba(255,255,255,0.6), transparent)' }}
                                    />
                                    <FlaskConical size={30} className="text-white relative z-10" strokeWidth={2.5} />
                                </div>
                            </div>

                            {/* Title */}
                            <h1
                                className="text-2xl font-extrabold leading-tight"
                                style={{ color: '#0D3B38', fontFamily: 'Outfit, sans-serif' }}
                            >
                                BHP Lab
                            </h1>
                            <p className="text-xs font-semibold mt-1.5 uppercase tracking-widest" style={{ color: '#5A8A86' }}>
                                Sistem Informasi Bahan Habis Pakai
                            </p>
                            <p className="text-xs mt-1" style={{ color: '#5A8A86' }}>
                                Politeknik Negeri Cilacap
                            </p>
                        </div>

                        {/* Form body */}
                        <div className="px-8 pb-8 pt-2">

                            {/* Status message */}
                            {status && (
                                <div
                                    className="mb-5 text-xs px-4 py-3 rounded-xl flex items-center gap-2 font-medium"
                                    style={{
                                        background: 'rgba(39,174,96,0.10)',
                                        color: '#27AE60',
                                        border: '1px solid rgba(39,174,96,0.25)',
                                    }}
                                >
                                    <span className="w-1.5 h-1.5 rounded-full" style={{ background: '#27AE60' }} />
                                    {status}
                                </div>
                            )}

                            <form onSubmit={submit} className="space-y-4">

                                {/* Email Field */}
                                <div>
                                    <label className="block text-xs font-bold mb-1.5" style={{ color: '#5A8A86', letterSpacing: '0.05em' }}>
                                        ALAMAT EMAIL
                                    </label>
                                    <div className="relative">
                                        <div className="absolute left-3.5 top-1/2 -translate-y-1/2" style={{ color: '#5A8A86' }}>
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
                                        <p className="mt-1.5 text-[11px] font-semibold" style={{ color: '#EF6C4A' }}>{errors.email}</p>
                                    )}
                                </div>

                                {/* Password Field */}
                                <div>
                                    <div className="flex items-center justify-between mb-1.5">
                                        <label className="text-xs font-bold" style={{ color: '#5A8A86', letterSpacing: '0.05em' }}>
                                            KATA SANDI
                                        </label>
                                    </div>
                                    <div className="relative">
                                        <div className="absolute left-3.5 top-1/2 -translate-y-1/2" style={{ color: '#5A8A86' }}>
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
                                            className="absolute right-3.5 top-1/2 -translate-y-1/2 transition-colors cursor-pointer"
                                            style={{ color: '#5A8A86' }}
                                            onMouseEnter={e => e.currentTarget.style.color = '#2BA8A2'}
                                            onMouseLeave={e => e.currentTarget.style.color = '#5A8A86'}
                                        >
                                            {showPassword ? <EyeOff size={15} /> : <Eye size={15} />}
                                        </button>
                                    </div>
                                    {errors.password && (
                                        <p className="mt-1.5 text-[11px] font-semibold" style={{ color: '#EF6C4A' }}>{errors.password}</p>
                                    )}
                                </div>

                                {/* Remember me */}
                                <div className="flex items-center justify-between pt-1">
                                    <label className="flex items-center gap-2 cursor-pointer group">
                                        <input
                                            type="checkbox"
                                            checked={data.remember}
                                            onChange={(e) => setData('remember', e.target.checked)}
                                            className="w-4 h-4 rounded cursor-pointer"
                                            style={{ accentColor: '#2BA8A2' }}
                                        />
                                        <span className="text-xs font-medium" style={{ color: '#5A8A86' }}>
                                            Ingat saya
                                        </span>
                                    </label>
                                </div>

                                {/* Submit — Gold CTA Pill */}
                                <button
                                    type="submit"
                                    disabled={processing}
                                    className="btn-primary w-full justify-center h-12 mt-2 disabled:opacity-60 disabled:cursor-not-allowed"
                                    style={{ fontSize: '14px' }}
                                >
                                    {processing ? (
                                        <div className="flex items-center gap-2">
                                            <svg className="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24" style={{ color: '#0D3B38' }}>
                                                <circle className="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" strokeWidth="4" />
                                                <path className="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z" />
                                            </svg>
                                            <span style={{ color: '#0D3B38', fontFamily: 'Outfit, sans-serif' }}>Memproses...</span>
                                        </div>
                                    ) : (
                                        <div className="flex items-center gap-2" style={{ fontFamily: 'Outfit, sans-serif' }}>
                                            <span className="font-extrabold">Masuk ke Akun</span>
                                            <ArrowRight size={16} strokeWidth={2.5} />
                                        </div>
                                    )}
                                </button>
                            </form>

                            {/* Demo credentials hint */}
                            <div
                                className="mt-5 p-3 rounded-xl text-center"
                                style={{
                                    background: 'rgba(43,168,162,0.06)',
                                    border: '1px dashed rgba(43,168,162,0.30)',
                                }}
                            >
                                <p className="text-[10px] font-semibold uppercase tracking-widest mb-1.5" style={{ color: '#5A8A86' }}>
                                    Akun Demo
                                </p>
                                <div className="space-y-0.5 text-[11px]" style={{ color: '#5A8A86' }}>
                                    <p><span className="font-bold" style={{ color: '#0D3B38' }}>Admin:</span> admin@bhp.com / 12345</p>
                                    <p><span className="font-bold" style={{ color: '#0D3B38' }}>Mahasiswa:</span> mahasiswa@bhp.com / 12345</p>
                                    <p><span className="font-bold" style={{ color: '#0D3B38' }}>Ketua Jurusan:</span> ketua@bhp.com / 12345</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    {/* Footer */}
                    <p className="text-center text-[10px] mt-6 font-medium" style={{ color: 'rgba(90,138,134,0.70)' }}>
                        © {new Date().getFullYear()} TPPL — Politeknik Negeri Cilacap. All rights reserved.
                    </p>
                </div>
            </div>
        </>
    );
}
