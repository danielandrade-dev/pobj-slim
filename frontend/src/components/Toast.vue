<script setup lang="ts">
import { computed, onMounted, h } from 'vue'
import Icon from './Icon.vue'

interface Props {
  type?: 'success' | 'error' | 'info'
  message: string
  duration?: number
  show?: boolean
}

const props = withDefaults(defineProps<Props>(), {
  type: 'info',
  duration: 3000,
  show: true
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
    default:
      return 'info-circle'
  }
})

onMounted(() => {
  if (props.duration > 0) {
    setTimeout(() => {
      emit('close')
    }, props.duration)
  }
})
</script>

<template>
  <Transition name="toast">
    <div v-if="show" :class="toastClass" role="alert">
      <Icon :name="iconName" :size="20" />
      <span>{{ message }}</span>
    </div>
  </Transition>
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

.toast i {
  font-size: 20px;
  flex-shrink: 0;
}

.toast span {
  font-size: 14px;
  font-weight: 500;
  color: #1f2937;
}

.toast-enter-active {
  transition: all 0.3s cubic-bezier(0.25, 0.1, 0.25, 1);
}

.toast-leave-active {
  transition: all 0.2s cubic-bezier(0.25, 0.1, 0.25, 1);
}

.toast-enter-from {
  opacity: 0;
  transform: translateX(100%);
}

.toast-leave-to {
  opacity: 0;
  transform: translateX(100%);
}

@keyframes toast-slide-in {
  from {
    opacity: 0;
    transform: translateX(100%);
  }
  to {
    opacity: 1;
    transform: translateX(0);
  }
}
</style>

