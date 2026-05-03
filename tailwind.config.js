import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    // 1. Tambahkan baris ini agar switch Dark Mode berfungsi via class
    darkMode: 'class',

    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
            },
            // Kamu bisa tambah warna custom RailExpress di sini kalau mau
            colors: {
                railBlue: '#2563eb',
                railOrange: '#fb923c',
            },
        },
    },

    plugins: [forms],
};
