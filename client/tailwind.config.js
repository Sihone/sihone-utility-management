/** @type {import('tailwindcss').Config} */
import colors from 'tailwindcss/colors'

export default {
  content: [
    "./index.html",
    "./src/**/*.{vue,js,ts,jsx,tsx}",
  ],
  theme: {
    extend: {
      colors: {
        ...colors, // bring back all Tailwind colors (gray, blue, red, etc.)
      }
    },
  },
  plugins: [],
}
