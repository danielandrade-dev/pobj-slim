<script setup lang="ts">
import { computed } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import type { ViewType, TabConfig } from '../types'

const router = useRouter()
const route = useRoute()

const views: TabConfig[] = [
  { id: 'cards', label: 'Resumo', icon: 'ti ti-dashboard', ariaLabel: 'Resumo', path: '/' },
  { id: 'table', label: 'Detalhamento', icon: 'ti ti-list-tree', ariaLabel: 'Detalhamento', path: '/detalhes' },
  { id: 'ranking', label: 'Rankings', icon: 'ti ti-trophy', ariaLabel: 'Rankings', path: '/ranking' },
  { id: 'exec', label: 'Visão executiva', icon: 'ti ti-chart-line', ariaLabel: 'Visão executiva', path: '/exec' },
  { id: 'simuladores', label: 'Simuladores', icon: 'ti ti-calculator', ariaLabel: 'Simuladores', path: '/simuladores' },
  { id: 'campanhas', label: 'Campanhas', icon: 'ti ti-speakerphone', ariaLabel: 'Campanhas', path: '/campanhas' }
]

const activeView = computed<ViewType>(() => {
  const currentPath = route.path
  const currentView = views.find(v => v.path === currentPath)
  return currentView?.id || 'cards'
})

const handleTabClick = (viewId: ViewType): void => {
  const view = views.find(v => v.id === viewId)
  if (view && view.path) {
    router.push(view.path)
  }
}
</script>

<template>
  <section class="tabs" aria-label="Navegação principal">
    <button
      v-for="view in views"
      :key="view.id"
      class="tab"
      :class="{ 'is-active': activeView === view.id }"
      :data-view="view.id"
      :aria-label="view.ariaLabel"
      @click="handleTabClick(view.id)"
    >
      <span class="tab-icon">
        <i :class="view.icon" aria-hidden="true"></i>
      </span>
      <span class="tab-label">{{ view.label }}</span>
    </button>
    <div class="tabs__aside">
      <small class="muted">
        <span id="lbl-atualizacao"></span>
      </small>
    </div>
  </section>
</template>

<style scoped>
.tabs {
  display: flex;
  align-items: center;
  gap: 8px;
  margin: 10px 0 12px;
  border-bottom: 1px solid var(--stroke, #e7eaf2);
}

.tab {
  background: transparent;
  border: none;
  cursor: pointer;
  padding: 10px 14px;
  border-radius: 10px 10px 0 0;
  font-weight: 700;
  color: #6b7280;
  border-bottom: 3px solid transparent;
  display: inline-flex;
  align-items: center;
  gap: 8px;
  transition: color 0.18s ease, border-color 0.18s ease;
}

.tab .tab-icon {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  width: 30px;
  height: 30px;
  border-radius: 8px;
  background: rgba(179, 0, 0, 0.12);
  color: var(--brand, #b30000);
  transition: background 0.18s ease, color 0.18s ease;
  flex-shrink: 0;
}

.tab .tab-icon i {
  font-size: 14px;
  line-height: 1;
}

.tab .tab-label {
  font-size: 14px;
}

.tab.is-active {
  color: var(--brand, #b30000);
  border-bottom-color: var(--brand, #b30000);
}

.tab.is-active .tab-icon {
  background: var(--brand, #b30000);
  color: #fff;
}

.tabs__aside {
  margin-left: auto;
  color: var(--muted, #6b7280);
}

@media (max-width: 900px) {
  .tabs {
    border-bottom: none;
    display: grid;
    grid-template-columns: repeat(3, minmax(0, 1fr));
    gap: 18px 14px;
    margin: 18px 0 12px;
    justify-items: center;
  }

  .tab {
    border: none;
    border-radius: 16px;
    background: transparent;
    box-shadow: none;
    align-items: center;
    justify-content: center;
    flex-direction: column;
    gap: 10px;
    padding: 0;
  }

  .tab .tab-icon {
    width: 60px;
    height: 60px;
    border-radius: 20px;
    background: #fff;
    box-shadow: 0 12px 24px rgba(15, 23, 42, 0.12);
    color: var(--brand, #b30000);
  }

  .tab .tab-icon i {
    font-size: 24px;
  }

  .tab .tab-label {
    font-size: 13px;
    font-weight: 700;
    color: #0f172a;
    text-align: center;
    white-space: normal;
  }

  .tab.is-active .tab-icon {
    background: var(--brand, #b30000);
    color: #fff;
    box-shadow: 0 16px 32px rgba(179, 0, 0, 0.22);
  }

  .tab.is-active .tab-label {
    color: var(--brand, #b30000);
  }

  .tabs__aside {
    grid-column: 1 / -1;
    justify-self: end;
  }
}

@media (max-width: 640px) {
  .tabs {
    grid-template-columns: repeat(3, minmax(0, 1fr));
    gap: 16px 10px;
    margin: 16px 0 20px;
    padding: 0;
  }

  .tab .tab-icon {
    width: 54px;
    height: 54px;
    border-radius: 18px;
  }

  .tab .tab-label {
    font-size: 12.5px;
  }

  .tabs__aside {
    grid-column: 1 / -1;
    justify-self: center;
    font-size: 12px;
  }
}
</style>

