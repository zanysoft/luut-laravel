import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './resources/js/**/*.jsx',
    ],
    theme: {
        container: {
            center: true,
            padding: {
                DEFAULT: "1rem",
                sm: "0rem",
                lg: "4rem",
                xl: "5rem",
                "2xl": "6rem",
            },
        },
        extend: {
            fontFamily: {
                montserrat: ["Montserrat"],
                lato: ["Lato"],
                garamond: ["Garamond"],
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                error: "#ff3b30",
                background: "var(--background)",
                foreground: "var(--foreground)",
            },
        },
    },

    plugins: [forms],
};
