<script setup lang="ts">
import { computed, onMounted } from 'vue'
import Icon from './Icon.vue'

interface Props {
  type?: 'success' | 'error' | 'info' | 'loading'
  message: string
  duration?: number
}

const props = withDefaults(defineProps<Props>(), {
  type: 'info',
  duration: 3000
})

const emit = defineEmits<{
  close: []
}>()

const toastClass = computed(() => `toast toast--${props.type}`)

const iconName = computed(() => {
  switch (props.type) {
    case 'success':
      return 'circle-check'
    case 'error':
      return 'alert-circle'
    case 'loading':
      return 'info-circle'
    default:
      return 'info-circle'
  }
})

onMounted(() => {
  if (props.duration && props.duration > 0 && props.type !== 'loading') {
    setTimeout(() => {
      emit('close')
    }, props.duration)
  }
})
</script>

<template>
  <div :class="toastClass" role="alert" @click="emit('close')">
    <Icon v-if="type !== 'loading'" :name="iconName" :size="20" />
    <div v-else class="toast__spinner"></div>
    <span>{{ message }}</span>
  </div>
</template>

<style scoped>
.toast {
  position: fixed;
  bottom: 24px;
  right: 24px;
  padding: 16px 20px;
  background: #fff;
  border-radius: 12px;
  box-shadow: 0 8px 24px rgba(0, 0, 0, 0.15);
  display: flex;
  align-items: center;
  gap: 12px;
  z-index: 9999;
  max-width: 400px;
  animation: toast-slide-in 0.3s cubic-bezier(0.25, 0.1, 0.25, 1);
}

.toast--success {
  border-left: 4px solid #10b981;
}

.toast--success i {
  color: #10b981;
}

.toast--error {
  border-left: 4px solid #ef4444;
}

.toast--error i {
  color: #ef4444;
}

.toast--info {
  border-left: 4px solid #3b82f6;
}

.toast--info i {
  color: #3b82f6;
}

.toast--loading {
  border-left: 4px solid #3b82f6;
}

.toast--loading .toast__spinner {
  width: 20px;
  height: 20px;
  border: 2px solid #e5e7eb;
  border-top-color: #3b82f6;
  border-radius: 50%;
  animation: spin 0.6s linear infinite;
  flex-shrink: 0;
}

.toast i,
.toast__spinner {
  font-size: 20px;
  flex-shrink: 0;
}

@keyframes spin {
  to {
    transform: rotate(360deg);
  }
}

.toast span {
  font-size: 14px;
  font-weight: 500;
  color: #1f2937;
}

.toast {
  cursor: pointer;
  pointer-events: auto;
}
</style>

