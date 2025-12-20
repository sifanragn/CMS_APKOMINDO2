// tailwind.config.js
module.exports = {
  content: [
    "./resources/**/*.blade.php",
    "./resources/**/*.js",
    "./resources/**/*.vue",
  ],
  theme: {
    extend: {
      
      // Pastikan warna-warna ini ada atau tidak di-override
      colors: {
        purple: {
          600: '#9333ea', // Contoh nilai hex untuk purple-600
          700: '#7e22ce',
        },
        rose: {
          600: '#e11d48', // Contoh nilai hex untuk rose-600
        },
        amber: {
          500: '#f59e0b', // Contoh nilai hex untuk amber-500
          600: '#d97706',
          700: '#b45309',
        },
        orange: {
          500: '#f97316', // Contoh nilai hex untuk orange-500
        },
        // ... warna lain yang Anda gunakan
      }
    },
  },
  plugins: [
    require('@tailwindcss/typography'),
],

}
