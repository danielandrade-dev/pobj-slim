<script setup lang="ts">
import { ref } from 'vue'
import Filters from '../components/Filters.vue'
import TabsNavigation from '../components/TabsNavigation.vue'
import ResumoModeToggle from '../components/ResumoModeToggle.vue'
import ResumoKPI from '../components/ResumoKPI.vue'
import ProdutosCards from '../components/ProdutosCards.vue'
import ResumoLegacy from '../components/ResumoLegacy.vue'

const resumoMode = ref<'cards' | 'legacy'>('cards')
</script>

<template>
  <div class="home-wrapper">
    <div class="container">
      <Filters />
      <TabsNavigation />
      
      <!-- Visão de resumo com opção de cards ou tabela clássica -->
      <section id="view-cards" class="view-section">
        <ResumoModeToggle v-model="resumoMode" />
        
        <div id="resumo-summary" class="resumo-summary">
          <ResumoKPI />
        </div>
        
        <div 
          id="resumo-cards" 
          class="resumo-mode__view"
          :class="{ hidden: resumoMode !== 'cards' }"
        >
          <ProdutosCards />
        </div>

        <div 
          id="resumo-legacy" 
          class="resumo-mode__view"
          :class="{ hidden: resumoMode !== 'legacy' }"
          aria-live="polite"
        >
          <ResumoLegacy />
        </div>
      </section>
    </div>
  </div>
</template>

<style scoped>
.home-wrapper {
  --brand: #b30000;
  --brand-dark: #8f0000;
  --bg: #f6f7fc;
  --panel: #ffffff;
  --stroke: #e7eaf2;
  --text: #0f1424;
  --muted: #6b7280;
  --radius: 16px;
  --shadow: 0 12px 28px rgba(17, 23, 41, 0.08);

  min-height: 100vh;
  width: 100%;
  padding: 20px 0;
  background-color: var(--bg);
  background-image:
    url("data:image/svg+xml,%3Csvg%20xmlns%3D%27http%3A//www.w3.org/2000/svg%27%20viewBox%3D%270%200%20320%20320%27%3E%3Ctext%20x%3D%2750%25%27%20y%3D%2750%25%27%20fill%3D%27rgba%2815%2C20%2C36%2C0.08%29%27%20font-size%3D%2720%27%20font-family%3D%27Plus%20Jakarta%20Sans%2C%20sans-serif%27%20text-anchor%3D%27middle%27%20dominant-baseline%3D%27middle%27%20transform%3D%27rotate%28-30%20160%20160%29%27%3EX%20Burguer%20%E2%80%A2%20Funcional%201234567%3C/text%3E%3C/svg%3E"),
    radial-gradient(1200px 720px at 95% -30%, #dfe8ff 0%, transparent 60%),
    radial-gradient(1100px 720px at -25% -10%, #ffe6ea 0%, transparent 55%);
  background-repeat: repeat, no-repeat, no-repeat;
  background-size: 320px 320px, auto, auto;
  background-position: center center, 95% -30%, -25% -10%;
  color: var(--text);
  font-family: "Plus Jakarta Sans", Inter, system-ui, Segoe UI, Roboto, Arial, sans-serif;
  -webkit-font-smoothing: antialiased;
  -moz-osx-font-smoothing: grayscale;
}

.container {
  max-width: min(1600px, 96vw);
  margin: 18px auto;
  padding: 0 16px;
}

.view-section {
  margin-top: 24px;
}

.resumo-summary {
  display: flex;
  flex-direction: column;
  gap: 18px;
  margin-bottom: 18px;
}

.kpi-summary {
  display: flex;
  flex-wrap: wrap;
  gap: 12px;
}

.resumo-mode__view {
  width: 100%;
}

.resumo-mode__view.hidden {
  display: none;
}

.cards-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
  gap: 16px;
}

@media (max-width: 768px) {
  .cards-grid {
    grid-template-columns: 1fr;
  }
  
  .resumo-summary {
    margin: 0 0 18px;
  }
  
  .kpi-summary {
    flex-wrap: nowrap;
    overflow-x: auto;
  }
}
</style>

