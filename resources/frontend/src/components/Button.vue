<script setup lang="ts">
interface Props {
  variant?: 'primary' | 'secondary' | 'link' | 'info'
  type?: 'button' | 'submit' | 'reset'
  disabled?: boolean
  icon?: string
}

withDefaults(defineProps<Props>(), {
  variant: 'secondary',
  type: 'button',
  disabled: false,
  icon: undefined
})

defineSlots<{
  default(): unknown
}>()
</script>

<template>
  <button
    :type="type"
    :disabled="disabled"
    :class="['btn', `btn--${variant}`]"
    v-bind="$attrs"
  >
    <i v-if="icon" :class="icon" aria-hidden="true"></i>
    <slot />
  </button>
</template>

<style scoped>
.btn {
  --brand: #b30000;
  --brand-dark: #8f0000;
  --stroke: #e7eaf2;
  --text: #0f1424;

  display: inline-flex;
  align-items: center;
  gap: 6px;
  padding: 8px 12px;
  border: 1px solid var(--stroke);
  border-radius: 10px;
  background: #fff;
  color: var(--text);
  font-size: 13px;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.2s ease;
  font-family: inherit;
}

.btn:disabled {
  opacity: 0.5;
  cursor: not-allowed;
}

.btn--primary {
  background: linear-gradient(90deg, var(--brand), var(--brand-dark));
  color: #fff;
  border-color: transparent;
  box-shadow: 0 4px 12px rgba(179, 0, 0, 0.25);
}

.btn--primary:hover:not(:disabled) {
  background: linear-gradient(90deg, var(--brand-dark), var(--brand));
  box-shadow: 0 6px 16px rgba(179, 0, 0, 0.35);
  transform: translateY(-1px);
}

.btn--secondary:hover:not(:disabled) {
  background: rgba(0, 0, 0, 0.04);
  border-color: rgba(0, 0, 0, 0.12);
}

.btn--link {
  background: transparent;
  border: none;
  color: var(--brand);
  padding: 8px 12px;
  box-shadow: none;
}

.btn--link:hover:not(:disabled) {
  background: rgba(179, 0, 0, 0.08);
  transform: none;
}

.btn--info {
  color: #246bfd;
}

.btn--info:hover:not(:disabled) {
  background: rgba(36, 107, 253, 0.08);
  color: #246bfd;
}
</style>

