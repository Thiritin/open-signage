import defaultTheme from 'tailwindcss/defaultTheme';
import colors from 'tailwindcss/colors';

/** @type {import('tailwindcss').Config} */
export default {
    mode: 'jit',
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './resources/js/**/*.vue',
    ],

    theme: {
        extend: {
            colors: {
                primary: {
                    DEFAULT: '#1C253C',
                    50: '#B0BCDA',
                    100: '#A2B0D4',
                    200: '#8698C7',
                    300: '#6A81BA',
                    400: '#506AAB',
                    500: '#43588F',
                    600: '#364774',
                    700: '#293658',
                    800: '#1C253C',
                    900: '#0A0D16',
                    950: '#010203'
                },
                secondary: '#feff99',
                accent: '#6dd2d1',
                danger: colors.rose,
                success: colors.green,
                warning: colors.yellow,
            },
        },
    },
};
