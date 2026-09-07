/** @type {import('tailwindcss').Config} */
export default {
    content: [
        "./resources/**/*.blade.php",
        "./resources/**/*.jsx",
        "./resources/**/*.js",
    ],
    darkMode: 'class',
    theme: {
        extend: {
            colors: {
                // ── Brand: deep teal (WCAG-safe on white) ─────────────
                teal: {
                    DEFAULT: '#0F766E',
                    light:   '#0D9488',
                    dark:    '#115E59',
                    bg:      '#F0FDFA',
                    soft:    'rgba(15,118,110,0.08)',
                    glow:    'rgba(15,118,110,0.12)',
                },
                // Legacy alias — existing pages still use `violet-*` classes
                violet: {
                    DEFAULT:   '#0F766E',
                    hover:     '#115E59',
                    secondary: '#0D9488',
                    soft:      'rgba(15,118,110,0.08)',
                    glow:      'rgba(15,118,110,0.12)',
                },
                // ── Semantic accents ─────────────────────────────────
                gold:  { DEFAULT: '#B45309', light: '#D97706', dark: '#92400E', glow: 'rgba(180,83,9,0.12)' },
                coral: { DEFAULT: '#DC2626', light: '#EF4444', dark: '#B91C1C', glow: 'rgba(220,38,38,0.12)' },
                sky:   { DEFAULT: '#0369A1', glow: 'rgba(3,105,161,0.12)' },
                // ── Surfaces & neutrals (slate) ──────────────────────
                cream: '#F8FAFC',
                surface: {
                    base: '#F8FAFC',
                    card: '#FFFFFF',
                },
                dark: {
                    bg:       '#F8FAFC',
                    card:     '#FFFFFF',
                    surface:  '#F1F5F9',
                    elevated: '#E2E8F0',
                },
                border: { DEFAULT: '#E2E8F0' },
                text: {
                    primary:   '#0F172A',
                    secondary: '#64748B',
                },
                // ── Status ───────────────────────────────────────────
                success: '#16A34A',
                warning: '#B45309',
                error:   '#DC2626',
            },
            fontFamily: {
                sans:    ['Inter', 'ui-sans-serif', 'system-ui', 'sans-serif'],
                mono:    ['JetBrains Mono', 'ui-monospace', 'monospace'],
                display: ['Outfit', 'Inter', 'ui-sans-serif', 'system-ui', 'sans-serif'],
            },
            fontSize: {
                '3xs': ['10px', { lineHeight: '1.4' }],
                '2xs': ['11px', { lineHeight: '1.4' }],
                xs:    ['12px', { lineHeight: '1.5' }],
                sm:    ['13px', { lineHeight: '1.5' }],
                base:  ['14px', { lineHeight: '1.6' }],
                lg:    ['16px', { lineHeight: '1.5' }],
                xl:    ['20px', { lineHeight: '1.4' }],
            },
            fontWeight: {
                // Legacy pages use font-extrabold; render it as bold instead of 800
                extrabold: '700',
            },
            letterSpacing: {
                widest:  '0.06em',
                wider:   '0.04em',
                display: '0.01em',
            },
            borderRadius: {
                xs:      '4px',
                sm:      '6px',
                DEFAULT: '8px',
                md:      '10px',
                lg:      '12px',
                xl:      '16px',
                full:    '9999px',
            },
            boxShadow: {
                sm:    '0 1px 2px rgba(15,23,42,0.05)',
                md:    '0 2px 4px rgba(15,23,42,0.04), 0 4px 12px rgba(15,23,42,0.06)',
                lg:    '0 8px 24px rgba(15,23,42,0.10)',
                card:  '0 1px 2px rgba(15,23,42,0.05)',
                modal: '0 10px 40px rgba(15,23,42,0.14), 0 2px 8px rgba(15,23,42,0.06)',
                focus: '0 0 0 3px rgba(15,118,110,0.15)',
                // Legacy aliases — resolve to neutral shadows now
                violet:      '0 1px 2px rgba(15,23,42,0.05)',
                'violet-sm': '0 0 0 3px rgba(15,118,110,0.12)',
                success:     '0 1px 2px rgba(15,23,42,0.05)',
            },
            transitionDuration: { fast: '150ms', DEFAULT: '200ms' },
            transitionTimingFunction: {
                bounce: 'ease-out',
            },
            keyframes: {
                'slide-up': {
                    '0%':   { opacity: '0', transform: 'translateY(4px)' },
                    '100%': { opacity: '1', transform: 'translateY(0)' },
                },
            },
            animation: {
                'slide-up': 'slide-up 0.2s ease-out',
            },
        },
    },
    plugins: [],
};
