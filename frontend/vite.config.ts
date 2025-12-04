import { defineConfig, loadEnv } from 'vite'
import vue from '@vitejs/plugin-vue'
import vueDevTools from 'vite-plugin-vue-devtools'
import tailwindcss from '@tailwindcss/vite'

export default defineConfig(({ mode }) => {
  const env = loadEnv(mode, process.cwd())

  return {
    base: '/',
    plugins: [
      vue(),
      vueDevTools(),
      tailwindcss(),
    ],

    build: {
      outDir: 'dist',
      emptyOutDir: true,
      rollupOptions: {
        output: {
          assetFileNames: 'assets/[name].[hash].[ext]',
          chunkFileNames: 'assets/[name].[hash].js',
          entryFileNames: 'assets/[name].[hash].js',
          manualChunks: (id) => {
            if (id.includes('node_modules')) {
              const match = id.match(/node_modules[\/\\](@[^\/\\]+[\/\\])?([^\/\\]+)/)
              if (match) {
                const packageName = match[2]
                const scope = match[1] ? match[1].replace(/[\/\\]/g, '') : ''
                const fullPackageName = scope ? `${scope}/${packageName}` : packageName
                
                if (fullPackageName === 'vue' || fullPackageName === 'vue-router') {
                  if (fullPackageName === 'vue-router') {
                    return 'vue-router'
                  }
                  return 'vue-core'
                }
                if (fullPackageName === '@tabler/icons-vue' || id.includes('@tabler/icons-vue')) {
                  return 'icons-vendor'
                }
                if (fullPackageName.startsWith('@vueuse')) {
                  return 'vueuse-vendor'
                }
                if (fullPackageName === 'motion' || fullPackageName === '@vueuse/motion') {
                  return 'motion-vendor'
                }
                if (fullPackageName === 'html2pdf.js' || 
                    fullPackageName === 'jspdf' || 
                    fullPackageName === 'html2canvas' ||
                    id.includes('html2pdf') ||
                    id.includes('jspdf') ||
                    id.includes('html2canvas')) {
                  return 'pdf-vendor'
                }
                if (fullPackageName === 'tailwindcss' || fullPackageName === '@tailwindcss/vite') {
                  return 'tailwind-vendor'
                }
              }
              
              return 'vendor'
            }
          }
        }
      },
      minify: 'esbuild',
      target: 'esnext',
      cssCodeSplit: true,
      sourcemap: false,
      chunkSizeWarningLimit: 500
    },

    resolve: {
      extensions: ['.mjs', '.js', '.mts', '.ts', '.jsx', '.tsx', '.json', '.css']
    },

    optimizeDeps: {
      exclude: ['@tabler/icons-webfont']
    },

    define: {
      __API__: JSON.stringify(env.VITE_API_URL)
    },

    server: {
      host: true,
      port: 5173,

      proxy: {
        '/api': {
          target: env.VITE_API_URL,
          changeOrigin: true,
          secure: false
        }
      }
    }
  }
})
