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
            // Separa node_modules em chunks menores e mais específicos
            if (id.includes('node_modules')) {
              // Extrai o nome do pacote do caminho
              const match = id.match(/node_modules[\/\\](@[^\/\\]+[\/\\])?([^\/\\]+)/)
              if (match) {
                const packageName = match[2]
                const scope = match[1] ? match[1].replace(/[\/\\]/g, '') : ''
                const fullPackageName = scope ? `${scope}/${packageName}` : packageName
                
                // Vue e Vue Router (core framework - sempre necessário)
                if (fullPackageName === 'vue' || fullPackageName === 'vue-router') {
                  if (fullPackageName === 'vue-router') {
                    return 'vue-router'
                  }
                  return 'vue-core'
                }
                
                // Tabler Icons (biblioteca grande de ícones)
                if (fullPackageName === '@tabler/icons-vue' || id.includes('@tabler/icons-vue')) {
                  return 'icons-vendor'
                }
                
                // VueUse (utilities - pode ser grande)
                if (fullPackageName.startsWith('@vueuse')) {
                  return 'vueuse-vendor'
                }
                
                // Motion/Animation libraries
                if (fullPackageName === 'motion' || fullPackageName === '@vueuse/motion') {
                  return 'motion-vendor'
                }
                
                // PDF generation (carregado sob demanda)
                // Agrupa html2pdf.js e todas suas dependências no mesmo chunk
                if (fullPackageName === 'html2pdf.js' || 
                    fullPackageName === 'jspdf' || 
                    fullPackageName === 'html2canvas' ||
                    id.includes('html2pdf') ||
                    id.includes('jspdf') ||
                    id.includes('html2canvas')) {
                  return 'pdf-vendor'
                }
                
                // Tailwind CSS
                if (fullPackageName === 'tailwindcss' || fullPackageName === '@tailwindcss/vite') {
                  return 'tailwind-vendor'
                }
                
                // Separar dependências grandes individualmente
                // Se o pacote for grande, separa em seu próprio chunk
                // Caso contrário, agrupa em vendor
              }
              
              // Fallback: agrupa outras dependências menores
              return 'vendor'
            }
          }
        }
      },
      // Otimizações de tamanho
      minify: 'esbuild',
      target: 'esnext',
      cssCodeSplit: true,
      sourcemap: false,
      // Avisa se algum chunk exceder 500KB
      chunkSizeWarningLimit: 500
    },

    resolve: {
      // Garante que arquivos CSS sejam resolvidos corretamente
      extensions: ['.mjs', '.js', '.mts', '.ts', '.jsx', '.tsx', '.json', '.css']
    },

    optimizeDeps: {
      // Exclui pacotes que só contêm CSS da otimização de dependências
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
