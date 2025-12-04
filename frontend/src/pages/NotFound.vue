<script setup lang="ts">
import Icon from '../components/Icon.vue'

const goBack = (): void => {
  window.history.back()
}
</script>

<template>
  <div class="error-container">
    <div class="error-card">
      <div class="error-icon">
        <Icon name="alert-circle" :size="64" />
      </div>
      <h1 class="error-code">404</h1>
      <h2 class="error-title">Página não encontrada</h2>
      <p class="error-message">
        A página que você está procurando não existe ou foi movida.
        Verifique o endereço e tente novamente.
      </p>
      <div class="error-actions">
        <router-link to="/" class="btn btn-primary">
          <Icon name="home" :size="16" />
          Voltar para o início
        </router-link>
        <button @click="goBack" class="btn btn-secondary">
          <Icon name="arrow-left" :size="16" />
          Voltar
        </button>
      </div>
    </div>
  </div>
</template>

<style scoped>
.error-container {
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
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 20px;
  color: var(--text);
  font-family: var(--brad-font-family, inherit);
  -webkit-font-smoothing: antialiased;
  -moz-osx-font-smoothing: grayscale;
}

.error-card {
  background: var(--panel);
  border-radius: var(--radius);
  box-shadow: var(--shadow);
  padding: 48px 32px;
  border: 1px solid var(--stroke);
  max-width: 600px;
  width: 100%;
  text-align: center;
}

.error-code {
  font-size: 120px;
  font-weight: 800;
  line-height: 1;
  background: linear-gradient(90deg, #cc092f 40%, #b81570 90%);
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
  background-clip: text;
  margin: 0 0 16px;
}

.error-title {
  font-size: 28px;
  font-weight: 700;
  color: var(--text);
  margin: 0 0 12px;
}

.error-message {
  font-size: 16px;
  color: var(--muted);
  margin: 0 0 32px;
  line-height: 1.6;
}

.error-icon {
  font-size: 64px;
  color: var(--brand);
  margin-bottom: 24px;
  opacity: 0.8;
}

.error-actions {
  display: flex;
  gap: 12px;
  justify-content: center;
  flex-wrap: wrap;
}

.btn {
  display: inline-flex;
  align-items: center;
  gap: 8px;
  padding: 12px 24px;
  border-radius: 12px;
  font-weight: 600;
  font-size: 15px;
  text-decoration: none;
  transition: all 0.2s ease;
  border: none;
  cursor: pointer;
  font-family: inherit;
}

.btn-primary {
  background: linear-gradient(90deg, #cc092f 40%, #b81570 90%);
  color: #fff;
  box-shadow: 0 6px 16px rgba(204, 9, 47, 0.25);
}

.btn-primary:hover {
  background: linear-gradient(90deg, #b81570 40%, #cc092f 90%);
  box-shadow: 0 8px 20px rgba(204, 9, 47, 0.35);
  transform: translateY(-1px);
}

.btn-secondary {
  background: rgba(255, 255, 255, 0.14);
  color: var(--text);
  border: 1px solid var(--stroke);
}

.btn-secondary:hover {
  background: rgba(255, 255, 255, 0.22);
  border-color: rgba(0, 0, 0, 0.15);
}

@media (max-width: 640px) {
  .error-code {
    font-size: 80px;
  }

  .error-title {
    font-size: 24px;
  }

  .error-card {
    padding: 32px 24px;
  }

  .error-actions {
    flex-direction: column;
  }

  .btn {
    width: 100%;
    justify-content: center;
  }
}
</style>
