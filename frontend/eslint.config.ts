import { globalIgnores } from 'eslint/config'
import { defineConfigWithVueTs, vueTsConfigs } from '@vue/eslint-config-typescript'
import pluginVue from 'eslint-plugin-vue'
import skipFormatting from '@vue/eslint-config-prettier/skip-formatting'

// To allow more languages other than `ts` in `.vue` files, uncomment the following lines:
// import { configureVueProject } from '@vue/eslint-config-typescript'
// configureVueProject({ scriptLangs: ['ts', 'tsx'] })
// More info at https://github.com/vuejs/eslint-config-typescript/#advanced-setup

export default defineConfigWithVueTs(
  {
    name: 'app/files-to-lint',
    files: ['**/*.{ts,mts,tsx,vue}'],
  },

  globalIgnores(['**/dist/**', '**/dist-ssr/**', '**/coverage/**', '**/public/legacy/**', '**/env.d.ts']),

  pluginVue.configs['flat/essential'],
  vueTsConfigs.recommended,
  skipFormatting,
  
  {
    rules: {
      // Permite nomes de componentes de uma palavra (Button, Header, Footer, etc)
      'vue/multi-word-component-names': ['error', {
        ignores: ['Button', 'Header', 'Footer', 'Filters', 'Toast', 'Home', 'Ranking', 'Detalhes', 'Campanhas', 'Simuladores']
      }],
      // Permite variáveis não utilizadas que começam com underscore
      '@typescript-eslint/no-unused-vars': ['error', {
        argsIgnorePattern: '^_',
        varsIgnorePattern: '^_'
      }]
    }
  },
  {
    files: ['**/views/Campanhas.vue', '**/views/Detalhes.vue', '**/views/Ranking.vue', '**/views/Simuladores.vue'],
    rules: {
      // Desabilita verificação de any para arquivos de views que usam dados dinâmicos da API
      '@typescript-eslint/no-explicit-any': 'off'
    }
  }
)
