import { fileURLToPath, URL } from 'node:url'

import { defineConfig } from 'vite'
import vue from '@vitejs/plugin-vue'
import ui from '@nuxt/ui/vite'
import uiThemeConfig from "./theme-config"

export default defineConfig({
  plugins: [
    vue(),
    ui(uiThemeConfig)
  ],
  resolve: {
    alias: {
      '@': fileURLToPath(new URL('./src', import.meta.url))
    },
  },
  build: {
    rollupOptions: {
      input: fileURLToPath(new URL('./src/main.js', import.meta.url))
    }
  }
})
