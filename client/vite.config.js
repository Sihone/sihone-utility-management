import { defineConfig } from 'vite'
import vue from '@vitejs/plugin-vue'
import vuetify from 'vite-plugin-vuetify'

// https://vite.dev/config/
export default defineConfig({
  plugins: [
    vue(),
    vuetify({ autoImport: true }), // <-- ADD vuetify here
  ],
  build: {
    outDir: '../public/client', // <-- Build Vue into Laravel's public/client folder
    emptyOutDir: true,
  },
  server: {
    proxy: {
      '/api': 'http://localhost:8000', // Proxy API requests to Laravel during dev
    },
  },
})
