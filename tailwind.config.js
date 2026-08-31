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
                // ── Flip7 Primary Teal ──────────────────────────────
                teal: {
                    DEFAULT: '#2BA8A2',
                    light:   '#3CC4BD',
                    dark:    '#1E8C86',
                    bg:      '#E8F6F5',
                    soft:    'rgba(43,168,162,0.10)',
                    glow:    'rgba(43,168,162,0.30)',
                },
                // Alias violet → teal so all existing `text-violet`, `bg-violet` classes
                // automatically adopt the Flip7 primary without touching every file.
                violet: {
                    DEFAULT:   '#2BA8A2',
                    hover:     '#1E8C86',
                    secondary: '#3CC4BD',
                    soft:      'rgba(43,168,162,0.10)',
                    glow:      'rgba(43,168,162,0.15)',
                },
                // ── Flip7 Accent Gold ────────────────────────────────
                gold: {
                    DEFAULT: '#FFD23F',
                    light:   '#FFE47A',
                    dark:    '#E6B800',
                    glow:    'rgba(255,210,63,0.40)',
                },
                // ── Flip7 Coral ──────────────────────────────────────
                coral: {
                    DEFAULT: '#EF6C4A',
                    light:   '#FF8A6A',
                    dark:    '#D45233',
                    glow:    'rgba(239,108,74,0.35)',
                },
                // ── Flip7 Sky Blue ───────────────────────────────────
                sky: {
                    DEFAULT: '#5DADE2',
                    glow:    'rgba(93,173,226,0.30)',
                },
                // ── Flip7 Surface / Neutral ──────────────────────────
                cream: '#FFF8E7',
                surface: {
                    base: '#EFF8F7',
                    card: '#FFFFFF',
                },
                dark: {
                    bg:       '#EFF8F7', // Flip7 Surface Base
                    card:     '#FFFFFF',
                    surface:  '#E8F6F5', // Teal BG tint
                    elevated: '#D4EEEC',
                },
                border: { DEFAULT: '#C8E6E4' },
                text: {
                    primary:   '#0D3B38',
                    secondary: '#5A8A86',
                },
                // ── Semantic ─────────────────────────────────────────
                success: '#27AE60',
                warning: '#FFD23F',
                error:   '#EF6C4A', // Coral as error/warning accent
            },
            fontFamily: {
                sans:    ['Outfit', 'Inter', 'ui-sans-serif', 'system-ui', 'sans-serif'],
                mono:    ['JetBrains Mono', 'ui-monospace', 'monospace'],
                display: ['Outfit', 'ui-sans-serif', 'system-ui', 'sans-serif'],
            },
            fontSize: {
                '3xs': ['9px',  { lineHeight: '1.4' }],
                '2xs': ['11px', { lineHeight: '1.4' }],
                xs:    ['12px', { lineHeight: '1.5' }],
                sm:    ['13px', { lineHeight: '1.5' }],
                base:  ['14px', { lineHeight: '1.6' }],
                lg:    ['16px', { lineHeight: '1.5' }],
                xl:    ['20px', { lineHeight: '1.4' }],
            },
            fontWeight: {
                extrabold: '800',
            },
            letterSpacing: {
                widest:  '0.2em',
                wider:   '0.1em',
                display: '0.05em',
            },
            borderRadius: {
                xs:      '4px',
                sm:      '8px',
                DEFAULT: '8px',
                md:      '16px',
                lg:      '24px',
                xl:      '32px',
                full:    '9999px',
            },
            boxShadow: {
                // Standard
                sm:    '0 2px 8px rgba(0,0,0,0.08)',
                md:    '0 4px 16px rgba(0,0,0,0.12)',
                lg:    '0 8px 32px rgba(0,0,0,0.16)',
                // Flip7 colored glow shadows
                card:         '0 4px 20px rgba(43,168,162,0.10)',
                modal:        '0 20px 40px rgba(30,140,134,0.12), 0 1px 3px rgba(0,0,0,0.04)',
                'teal-glow':  '0 4px 20px rgba(43,168,162,0.30)',
                'gold-glow':  '0 4px 20px rgba(255,210,63,0.40)',
                'coral-glow': '0 4px 20px rgba(239,108,74,0.35)',
                'sky-glow':   '0 4px 16px rgba(93,173,226,0.30)',
                'focus':      '0 0 0 4px rgba(43,168,162,0.15)',
                // Legacy alias
                violet:       '0 0 24px rgba(43,168,162,0.20)',
                'violet-sm':  '0 0 0 3px rgba(43,168,162,0.20)',
            },
            transitionDuration: { fast: '150ms', DEFAULT: '250ms' },
            transitionTimingFunction: {
                bounce: 'cubic-bezier(0.34, 1.56, 0.64, 1)',
            },
            keyframes: {
                'glow-pulse': {
                    '0%, 100%': { opacity: '0.7', transform: 'scale(1.00)' },
                    '50%':      { opacity: '1.0', transform: 'scale(1.03)' },
                },
                'coral-pulse': {
                    '0%, 100%': { boxShadow: '0 4px 20px rgba(239,108,74,0.20)' },
                    '50%':      { boxShadow: '0 4px 20px rgba(239,108,74,0.50)' },
                },
                'crown-bounce': {
                    '0%':   { transform: 'rotate(-5deg) scale(1.0)' },
                    '25%':  { transform: 'rotate(5deg) scale(1.1)' },
                    '50%':  { transform: 'rotate(-3deg) scale(1.05)' },
                    '75%':  { transform: 'rotate(3deg) scale(1.1)' },
                    '100%': { transform: 'rotate(-5deg) scale(1.0)' },
                },
                'slide-up': {
                    '0%':   { opacity: '0', transform: 'translateY(8px)' },
                    '100%': { opacity: '1', transform: 'translateY(0)' },
                },
                'float': {
                    '0%, 100%': { transform: 'translateY(0px)' },
                    '50%':      { transform: 'translateY(-6px)' },
                },
            },
            animation: {
                'glow-pulse':   'glow-pulse 2s ease-in-out infinite',
                'coral-pulse':  'coral-pulse 2s ease-in-out infinite',
                'crown-bounce': 'crown-bounce 1.5s ease-in-out infinite',
                'slide-up':     'slide-up 0.3s ease-out',
                'float':        'float 3s ease-in-out infinite',
            },
        },
    },
    plugins: [],
};
