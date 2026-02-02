import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    darkMode: 'class',

    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
    ],

    safelist: [
        'bg-blue-500/10',
        'bg-blue-500/20',
        'bg-blue-600/5',
        'border-blue-500/30',
        'border-blue-500/50',
        'text-blue-400',
        'text-blue-300',
        'from-blue-500/10',
        'to-blue-600/5',
        'bg-emerald-500/10',
        'bg-emerald-500/20',
        'bg-emerald-600/5',
        'border-emerald-500/30',
        'border-emerald-500/50',
        'text-emerald-400',
        'text-emerald-300',
        'from-emerald-500/10',
        'to-emerald-600/5',
        'bg-purple-500/10',
        'bg-purple-500/20',
        'bg-purple-600/5',
        'border-purple-500/30',
        'border-purple-500/50',
        'text-purple-400',
        'text-purple-300',
        'from-purple-500/10',
        'to-purple-600/5',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Inter', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                gray: {
                    950: '#0a0a0f',
                }
            }
        },
    },

    plugins: [forms],
};
