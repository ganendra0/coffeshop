// tailwind.config.js
/** @type {import('tailwindcss').Config} */
export default {
  content: [
    "./resources/**/*.blade.php",
    "./resources/**/*.js",
    "./resources/**/*.vue",
  ],
  theme: {
    extend: {
      colors: {
        'primary': '#c49b63', // Warna utama dari template Anda
        'darken': '#151111',
      },
      fontFamily: {
        'poppins': ['Poppins', 'sans-serif'],
        'josefin': ['"Josefin Sans"', 'sans-serif'],
        'great-vibes': ['"Great Vibes"', 'cursive'],
      }
    },
  },
  plugins: [],
}