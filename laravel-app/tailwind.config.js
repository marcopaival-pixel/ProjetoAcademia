import defaultTheme from 'tailwindcss/defaultTheme';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/**/*.blade.php',
        './resources/**/*.js',
        './resources/**/*.vue',
    ],
    theme: {
        extend: {
            fontFamily: {
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                cyber: {
                    blue: '#3b82f6',
                    emerald: '#10b981',
                    indigo: '#8b5cf6',
                },
            },
            keyframes: {
                'dashboard-entry': {
                    from: { opacity: '0', transform: 'translateY(40px) scale(0.98)' },
                    to: { opacity: '1', transform: 'translateY(0) scale(1)' },
                },
            },
            animation: {
                'dashboard-entry': 'dashboard-entry 1.2s cubic-bezier(0.16, 1, 0.3, 1) forwards',
            },
        },
    },
    plugins: [],
};
