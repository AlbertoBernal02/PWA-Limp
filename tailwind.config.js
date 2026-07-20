import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    darkMode: 'class',
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './resources/js/**/*.js',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                // Azul del logo de Sensilimp (extraído de public/images/logo.png).
                // Es el color de marca principal: botones, enlaces, casi todo lo interactivo.
                brand: {
                    50: '#edf8fc',
                    100: '#d7eef9',
                    200: '#b2e1f5',
                    300: '#7ecef0',
                    400: '#45baed',
                    500: '#13abec',
                    600: '#0e95cf',
                    700: '#0b7cad',
                    800: '#09648b',
                    900: '#094f6d',
                    950: '#072f40',
                },
                // Verde del mismo logo. Reservado para el estado "Hecha"/completado,
                // para no mezclarlo con el azul de las acciones normales.
                accent: {
                    50: '#f8fbe9',
                    100: '#eff6d5',
                    200: '#e2ecb6',
                    300: '#d0e18e',
                    400: '#bad161',
                    500: '#a9c341',
                    600: '#8ea630',
                    700: '#758924',
                    800: '#5e6e1c',
                    900: '#4a5714',
                    950: '#2a330b',
                },
            },
        },
    },

    plugins: [forms],
};
