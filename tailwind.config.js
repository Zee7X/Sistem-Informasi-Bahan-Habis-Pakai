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
                violet: {
                    DEFAULT: '#5E6AD2',
                    hover:   '#4E5BBF',
                    secondary: '#6E79D6',
                    soft:    'rgba(94,106,210,0.10)',
                    glow:    'rgba(94,106,210,0.15)',
                },
                dark: {
                    bg:       '#F8FAFC', // Slate 50
                    card:     '#FFFFFF', // White
                    surface:  '#F1F5F9', // Slate 100
                    elevated: '#E2E8F0', // Slate 200
                },
                border: { DEFAULT: '#E2E8F0' }, // Slate 200
                text: {
                    primary:   '#0F172A', // Slate 900
                    secondary: '#64748B', // Slate 500
                },
                success: '#10B981', // Emerald 500
                warning: '#F59E0B', // Amber 500
                error:   '#EF4444', // Red 500
            },
            fontFamily: {
                sans:  ['Inter', 'ui-sans-serif', 'system-ui', 'sans-serif'],
                mono:  ['JetBrains Mono', 'ui-monospace', 'monospace'],
            },
            fontSize: {
                '2xs': ['11px', { lineHeight: '1.4' }],
                xs:    ['12px', { lineHeight: '1.5' }],
                sm:    ['13px', { lineHeight: '1.5' }],
                base:  ['14px', { lineHeight: '1.6' }],
            },
            borderRadius: {
                sm:  '4px',
                DEFAULT: '6px',
                md:  '8px',
                lg:  '12px',
                full: '9999px',
            },
            boxShadow: {
                modal:  '0 20px 40px rgba(15, 23, 42, 0.08), 0 1px 3px rgba(15, 23, 42, 0.04)',
                card:   '0 1px 3px 0 rgba(0, 0, 0, 0.05), 0 1px 2px 0 rgba(0, 0, 0, 0.03)',
                violet: '0 0 24px rgba(94,106,210,0.15)',
                'violet-sm': '0 0 0 2px rgba(94,106,210,0.15)',
            },
            transitionDuration: { fast: '100ms', DEFAULT: '150ms' },
        },
    },
    plugins: [],
};
