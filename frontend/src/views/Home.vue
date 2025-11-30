<script setup lang="ts">
import Filters from '../components/Filters.vue'
import TabsNavigation from '../components/TabsNavigation.vue'
import ResumoView from './ResumoView.vue'
</script>

<template>
  <div class="home-wrapper">
    <div class="container">
      <Filters />
      <TabsNavigation />

      <!-- Visão de resumo com opção de cards ou tabela clássica -->
      <ResumoView />
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
  color: var(--text);
  font-family: var(--brad-font-family, inherit);
  -webkit-font-smoothing: antialiased;
  -moz-osx-font-smoothing: grayscale;
}

.container {
  max-width: min(1600px, 96vw);
  margin: 18px auto;
  padding: 0 16px;
}

</style>

