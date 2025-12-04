<script setup lang="ts">
import { computed } from 'vue'

const props = defineProps<{
  modelValue: string
}>()

const emit = defineEmits<{
  'update:modelValue': [value: string]
}>()

const searchValue = computed({
  get: () => props.modelValue,
  set: (value: string) => emit('update:modelValue', value)
})
</script>

<template>
  <div class="search-box">
    <input
      v-model="searchValue"
      type="text"
      placeholder="Contrato (Ex.: CT-AAAA-999999)"
      class="input input--search"
    />
  </div>
</template>

<style scoped>
.search-box {
  flex: 1;
  min-width: 200px;
  max-width: 400px;
}

.input--search {
  width: 100%;
  padding: 10px 12px;
  border: 1px solid var(--stroke, #e7eaf2);
  border-radius: 10px;
  background: var(--panel, #fff);
  color: var(--text, #0f1424);
  font-size: 14px;
  transition: all 0.2s ease;
}

.input--search:focus {
  outline: none;
  border-color: var(--brand, #cc092f);
  box-shadow: 0 0 0 3px rgba(204, 9, 47, 0.12);
}

@media (max-width: 900px) {
  .search-box {
    flex: 1 1 auto;
    min-width: 0;
  }
}
</style>
