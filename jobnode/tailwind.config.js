import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './resources/js/**/*.vue',
    ],
    theme: {
        extend: {
            colors: {
                charcoal: {
                    DEFAULT: '#132322',
                    dark: '#0E1A1A',
                },
                jobnode: {
                    emerald: '#3DDC91',
                    emeraldHover: '#2EC977',
                    sky: '#007AFF',
                },
                neutral: {
                    stone: '#434E4E',
                    light: '#D0D3D3',
                }
            },
            fontFamily: {
                display: ['AeonikFono', '-apple-system', 'BlinkMacSystemFont', '"Segoe UI"', 'sans-serif'],
                body: ['Aeonik', '-apple-system', 'BlinkMacSystemFont', '"Segoe UI"', 'sans-serif'],
                ui: ['Arial', 'sans-serif'],
            },
            boxShadow: {
                'l1': '0px 4px 12px rgba(0, 0, 0, 0.08)',
                'l2': '0px 8px 24px rgba(0, 0, 0, 0.12)',
                'l3': '0px 16px 32px rgba(0, 0, 0, 0.1)',
                'l4': '0px 24px 48px rgba(0, 0, 0, 0.2)',
                'dark-l1': '0px 4px 12px rgba(0, 0, 0, 0.2)',
                'dark-l2': '0px 8px 24px rgba(61, 220, 145, 0.15)',
            },
            borderRadius: {
                '4xl': '32px',
            },
            spacing: {
                '40': '10rem',
            }
        },
    },
    plugins: [forms],
};