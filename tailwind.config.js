import plugin from 'tailwindcss/plugin';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './resources/views/**/*.blade.php',
        './resources/js/**/*.vue',
        './packages/**/*.vue',
    ],

    theme: {
        screens: {
            sm: '640px',
            md: '768px',
            lg: '1024px',
            xl: '1280px',
            '2xl': '1536px',
        },
        container: {
            screens: {
                sm: '640px',
                md: '768px',
                lg: '1024px',
                xl: '1280px',
            },
        },
        extend: {
            colors: {
                primary: {
                    50: 'var(--color-primary-50)',
                    100: 'var(--color-primary-100)',
                    200: 'var(--color-primary-200)',
                    300: 'var(--color-primary-300)',
                    400: 'var(--color-primary-400)',
                    500: 'var(--color-primary-500)',
                    600: 'var(--color-primary-600)',
                    700: 'var(--color-primary-700)',
                    800: 'var(--color-primary-800)',
                    900: 'var(--color-primary-900)',
                }
            },
            fontFamily: {
            },
        },
    },

    plugins: [
        forms,
        plugin(function ({ matchUtilities, theme }) {
            matchUtilities({
                'gap-x': (value) => {
                    return {
                        'column-gap': value,
                        '--gap-x': value,
                    };
                },
                'gap-y': (value) => {
                    return {
                        'row-gap': value,
                        '--gap-y': value,
                    };
                },
            }, { values: theme('gap') });
        }),
    ],
};
