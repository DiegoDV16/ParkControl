/** @type {import('tailwindcss').Config} */
export default {
  content: ['./index.html', './src/**/*.{js,ts,jsx,tsx}'],
  theme: {
    extend: {
      colors: {
        pc: {
          bg: '#050e1a',
          panel: '#0a1c2e',
          panel2: '#0d2438',
          border: '#14344c',
          mint: '#3ee0a1',
          mint2: '#2bc98c',
          text: '#e6f3f8',
          muted: '#8fb2c6',
          danger: '#ff6b6b',
          warn: '#f5b34a',
        },
      },
      fontFamily: {
        sans: ['"Segoe UI"', 'system-ui', 'sans-serif'],
        mono: ['Consolas', '"Courier New"', 'monospace'],
      },
      boxShadow: {
        glow: '0 0 0 1px rgba(62,224,161,.22), 0 18px 50px rgba(0,0,0,.45)',
      },
    },
  },
  plugins: [],
}
