<script setup lang="ts">
import ResumoView from './ResumoView.vue'
</script>

<template>
  <div class="home-wrapper">
    <ResumoView />
  </div>
</template>

<style scoped>
.home-wrapper {
  --brand: var(--brad-color-primary, #cc092f);
  --brand-dark: var(--brad-color-primary-dark, #9d0b21);
  --bg: var(--brad-color-neutral-0, #fff);
  --panel: var(--brad-color-neutral-0, #fff);
  --stroke: var(--brad-color-gray-light, #ebebeb);
  --text: var(--brad-color-neutral-100, #000);
  --muted: var(--brad-color-gray-dark, #999);
  --radius: 16px;
  --shadow: 0 12px 28px rgba(17, 23, 41, 0.08);

  min-height: 100vh;
  width: 100%;
  padding: 20px 0;
  color: var(--text);
  font-family: var(--brad-font-family);
  -webkit-font-smoothing: antialiased;
  -moz-osx-font-smoothing: grayscale;
  box-sizing: border-box;
}


</style>

