import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
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
            colors: {
                brand: {
                    50: '#eefcf9',
                    100: '#d4f7ef',
                    200: '#adeedf',
                    300: '#7ce0cb',
                    400: '#45c9ae',
                    500: '#22ad94',
                    600: '#158c78',
                    700: '#147063',
                    800: '#145951',
                    900: '#134a44',
                    950: '#062b28',
                },
            },
        },
    },

    plugins: [forms],
};
