<script setup lang="ts">
import Icon from '../Icon.vue'

defineEmits<{
  expandAll: []
  collapseAll: []
  openColumnDesigner: []
}>()
</script>

<template>
  <div class="table-toolbar-wrapper">
    <div class="table-toolbar">
      <button type="button" class="table-toolbar__btn" @click="$emit('expandAll')">
        <span class="table-toolbar__icon"><Icon name="chevrons-down" :size="16" /></span>
        <span class="table-toolbar__text">Expandir tudo</span>
      </button>
      <button type="button" class="table-toolbar__btn" @click="$emit('collapseAll')">
        <span class="table-toolbar__icon"><Icon name="chevrons-up" :size="16" /></span>
        <span class="table-toolbar__text">Recolher tudo</span>
      </button>
      <button
        type="button"
        class="table-toolbar__btn detail-view-manage"
        title="Personalizar colunas da tabela"
        @click="$emit('openColumnDesigner')"
      >
        <span class="table-toolbar__icon"><Icon name="columns" :size="16" /></span>
        <span class="table-toolbar__text">Personalizar colunas</span>
      </button>
    </div>
  </div>
</template>

<style scoped>
.table-toolbar-wrapper {
  margin-bottom: 16px;
}

.table-toolbar {
  display: flex;
  gap: 8px;
  flex-wrap: wrap;
}

.table-toolbar__btn {
  display: flex;
  align-items: center;
  gap: 6px;
  padding: 8px 12px;
  border: 1px solid var(--stroke, #e7eaf2);
  border-radius: 8px;
  background: var(--panel, #fff);
  color: var(--text, #0f1424);
  font-size: 13px;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.2s ease;
}

.table-toolbar__btn:hover {
  background: rgba(0, 0, 0, 0.04);
  border-color: rgba(0, 0, 0, 0.12);
}

.table-toolbar__icon {
  display: flex;
  align-items: center;
}

.detail-view-manage i {
  margin-right: 6px;
}

@media (max-width: 900px) {
  .table-toolbar {
    justify-content: stretch;
  }

  .table-toolbar__btn {
    flex: 1 1 calc(50% - 12px);
    min-width: 0;
  }
}
</style>
