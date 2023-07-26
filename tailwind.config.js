const defaultTheme = require('tailwindcss/defaultTheme');
/** @type {import('tailwindcss').Config} */
module.exports = {
    content: [
        './resources/views/**/*.blade.php',
        './resources/js/**/*.vue',
        './packages/**/*.vue',
    ],

    theme: {
        extend: {
            colors: {
            },
            fontFamily: {
                // sans: ['Arial', ...defaultTheme.fontFamily.sans],
            },
        },
    },

    plugins: [
        require('@tailwindcss/forms'),
    ],
};
