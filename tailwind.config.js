/** @type {import('tailwindcss').Config} */
export default {
  content: [
    "./resources/**/*.blade.php",
    "./resources/**/*.js",
    "./resources/**/*.vue",
  ],
  theme: {
    extend: {
      fontSize: {
        'xs': '0.618rem',
        'sm': '1rem',
        'md': '1.618rem',
        'lg': '2.618rem',
        'xl': '4.236rem',
        '2xl': '6.854rem',
      },
      colors: {
        golden: {
          light: '#f7f7f7', // L 98%
          primary: '#1e3a8a', // example
          secondary: '#122455', // divided by 1.618
          dark: '#1a1a1a',
        }
      }
    },
  },
  plugins: [
    require('daisyui'),
  ],
  daisyui: {
    themes: [
      {
        goldenlight: {
          "primary": "#3b82f6",
          "secondary": "#245098", // 3b82f6 darkened by 1.618 ratio roughly
          "accent": "#f6af3b",
          "neutral": "#1a1a1a",
          "base-100": "#f7f7f7",
          "base-content": "#1a1a1a",
        },
        goldendark: {
          "primary": "#60a5fa",
          "secondary": "#9bc3fb", // lightened by 1.618 ratio
          "accent": "#f8c575",
          "neutral": "#f7f7f7",
          "base-100": "#1a1a1a",
          "base-content": "#f7f7f7",
        },
      },
    ],
  },
}
