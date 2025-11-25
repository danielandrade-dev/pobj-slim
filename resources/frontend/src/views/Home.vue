<script setup lang="ts">
import { ref } from 'vue'
import Filters from '../components/Filters.vue'
import TabsNavigation from '../components/TabsNavigation.vue'
import ResumoModeToggle from '../components/ResumoModeToggle.vue'
import ResumoKPI from '../components/ResumoKPI.vue'
import ProdutosCards from '../components/ProdutosCards.vue'

const resumoMode = ref<'cards' | 'legacy'>('cards')
</script>

<template>
  <div class="container">
    <Filters />
    <TabsNavigation />
    
    <!-- Visão de resumo com opção de cards ou tabela clássica -->
    <section id="view-cards" class="view-section">
      <div id="resumo-summary" class="resumo-summary">
        <ResumoKPI />
      </div>
      
      <ResumoModeToggle v-model="resumoMode" />
      
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
      ></div>
    </section>
  </div>
</template>

<style scoped>
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

