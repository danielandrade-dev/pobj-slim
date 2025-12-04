<script setup lang="ts">
export type TableView = {
  id: string
  label: string
  key: string
}

const props = defineProps<{
  views: TableView[]
  activeView: string
}>()

const emit = defineEmits<{
  viewChange: [viewId: string]
}>()

const handleChipClick = (viewId: string) => {
  if (viewId !== props.activeView) {
    emit('viewChange', viewId)
  }
}
</script>

<template>
  <div class="chipbar">
    <button
      v-for="view in views"
      :key="view.id"
      type="button"
      class="chip"
      :class="{ 'is-active': view.id === activeView }"
      :data-view="view.id"
      @click="handleChipClick(view.id)"
    >
      {{ view.label }}
    </button>
  </div>
</template>

<style scoped>
.chipbar {
  display: flex;
  flex-wrap: wrap;
  gap: 8px;
  margin: 0;
}

.chip {
  user-select: none;
  cursor: pointer;
  padding: 8px 14px;
  border-radius: 999px;
  border: 1px solid var(--stroke, #e7eaf2);
  background: var(--panel, #ffffff);
  color: var(--text, #0f1424);
  font-weight: 700;
  font-size: 13px;
  transition: all 0.15s ease;
  box-sizing: border-box;
}

.chip:hover {
  box-shadow: var(--shadow, 0 12px 28px rgba(17, 23, 41, 0.08));
  transform: translateY(-1px);
  border-color: #cbd5e1;
}

.chip.is-active {
  background: rgba(179, 0, 0, 0.12);
  border-color: var(--brand, #b30000);
  color: var(--brand, #b30000);
  box-shadow: 0 2px 8px rgba(179, 0, 0, 0.15);
}
</style>

