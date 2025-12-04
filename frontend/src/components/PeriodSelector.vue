<script setup lang="ts">
import { ref, watch, onMounted, onUnmounted, nextTick } from 'vue'
import Icon from './Icon.vue'
import { getDefaultPeriod, formatBRDate } from '../services/calendarioService'
import { useCalendarioCache } from '../composables/useCalendarioCache'

interface Props {
  modelValue?: { start: string; end: string }
}

const props = withDefaults(defineProps<Props>(), {
  modelValue: undefined
})

const emit = defineEmits<{
  'update:modelValue': [value: { start: string; end: string }]
}>()

const period = ref<{ start: string; end: string }>(props.modelValue || getDefaultPeriod())
const { loadCalendario } = useCalendarioCache()
const buttonRef = ref<HTMLElement | null>(null)

watch(() => props.modelValue, (newValue) => {
  if (newValue) {
    period.value = { ...newValue }
  }
}, { deep: true })

onMounted(async () => {
  await loadCalendario()
  if (!props.modelValue) {
    const defaultPeriod = getDefaultPeriod()
    period.value = defaultPeriod
    emit('update:modelValue', defaultPeriod)
  }
})

const isPopoverOpen = ref(false)
const localStart = ref(period.value.start)
const localEnd = ref(period.value.end)
const popoverRef = ref<HTMLElement | null>(null)
const startInputRef = ref<HTMLInputElement | null>(null)
const endInputRef = ref<HTMLInputElement | null>(null)

const closeDatePopover = (): void => {
  isPopoverOpen.value = false
}

const openDatePopover = (): void => {
  localStart.value = period.value.start
  localEnd.value = period.value.end
  isPopoverOpen.value = true
  nextTick(() => {
    positionPopover()
    startInputRef.value?.focus()
  })
}

const positionPopover = () => {
  if (!buttonRef.value || !popoverRef.value) return
  
  const anchor = buttonRef.value
  const pop = popoverRef.value
  const w = pop.offsetWidth || 340
  const h = pop.offsetHeight || 170
  const r = anchor.getBoundingClientRect()
  const pad = 12
  const vw = window.innerWidth
  const vh = window.innerHeight

  let top = r.bottom + 8
  let left = r.right - w
  if (top + h + pad > vh) top = Math.max(pad, r.top - h - 8)
  if (left < pad) left = pad
  if (left + w + pad > vw) left = Math.max(pad, vw - w - pad)

  pop.style.top = `${top}px`
  pop.style.left = `${left}px`
}

const handleOutsideClick = (ev: MouseEvent): void => {
  const target = ev.target as HTMLElement
  if (
    !popoverRef.value ||
    !buttonRef.value ||
    popoverRef.value.contains(target) ||
    buttonRef.value.contains(target) ||
    target.closest('.date-popover')
  ) return
  closeDatePopover()
}

const handleEscape = (ev: KeyboardEvent): void => {
  if (ev.key === 'Escape' && isPopoverOpen.value) {
    closeDatePopover()
  }
}

const saveDatePopover = (): void => {
  if (!localStart.value || !localEnd.value || new Date(localStart.value) > new Date(localEnd.value)) {
    alert('Período inválido.')
    return
  }

  period.value = { start: localStart.value, end: localEnd.value }
  emit('update:modelValue', { start: localStart.value, end: localEnd.value })
  closeDatePopover()
}

watch(() => isPopoverOpen.value, (open) => {
  if (open) {
    nextTick(() => {
      setTimeout(() => {
        document.addEventListener('click', handleOutsideClick, { once: true, capture: false })
        document.addEventListener('keydown', handleEscape, { once: true })
      }, 100)
    })
  }
})

onUnmounted(() => {
  document.removeEventListener('click', handleOutsideClick)
  document.removeEventListener('keydown', handleEscape)
})
</script>

<template>
  <div class="period-selector">
    <div class="period-inline">
      <span class="txt">
        De
        <strong>{{ formatBRDate(period.start) }}</strong>
        até
        <strong>{{ formatBRDate(period.end) }}</strong>
      </span>
      <button
        ref="buttonRef"
        id="btn-alterar-data"
        type="button"
        class="link-action"
        @click="openDatePopover"
      >
        <Icon name="chevron-down" :size="16" /> Alterar data
      </button>
      
      <Teleport to="body">
        <div
          v-if="isPopoverOpen"
          ref="popoverRef"
          class="date-popover"
          style="position: fixed; visibility: visible;"
        >
          <h4>Alterar data</h4>
          <div class="row" style="margin-bottom:8px">
            <input
              ref="startInputRef"
              v-model="localStart"
              type="date"
              aria-label="Data inicial"
            />
            <input
              ref="endInputRef"
              v-model="localEnd"
              type="date"
              aria-label="Data final"
            />
          </div>
          <div class="actions">
            <button type="button" class="btn-sec" @click="closeDatePopover">Cancelar</button>
            <button type="button" class="btn-pri" @click="saveDatePopover">Salvar</button>
          </div>
        </div>
      </Teleport>
    </div>
  </div>
</template>

<style scoped>
.period-selector {
  position: relative;
}

.period-inline {
  display: flex;
  align-items: center;
  gap: 8px;
  flex-wrap: wrap;
}

.period-inline .txt {
  font-size: 13px;
  color: var(--muted, #6b7280);
  font-family: var(--brad-font-family, inherit);
}

.period-inline strong {
  color: var(--text, #0f1424);
  font-weight: var(--brad-font-weight-bold, 700);
  font-family: var(--brad-font-family, inherit);
}

.period-selector .link-action {
  display: inline-flex;
  align-items: center;
  gap: 6px;
  background: var(--panel, #fff);
  border: 1px solid var(--stroke, #e7eaf2);
  color: var(--brand, #cc092f);
  font-size: 13px;
  font-weight: var(--brad-font-weight-bold, 700);
  cursor: pointer;
  padding: 8px 14px;
  border-radius: 10px;
  transition: all 0.2s ease;
  box-sizing: border-box;
  font-family: var(--brad-font-family, inherit);
  outline: none;
}

.period-selector .link-action:hover:not(:disabled) {
  background: var(--brand-xlight, rgba(204, 9, 47, 0.08));
  border-color: var(--brand, #cc092f);
  transform: translateY(-1px);
  box-shadow: 0 4px 12px rgba(204, 9, 47, 0.15);
}

.period-selector .link-action:disabled {
  opacity: 0.5;
  cursor: not-allowed;
}
</style>

<style>
.link-action {
  display: inline-flex !important;
  align-items: center;
  gap: 6px;
  background: var(--panel, #fff) !important;
  border: 1px solid var(--stroke, #e7eaf2) !important;
  color: var(--brand, #cc092f) !important;
  font-size: 13px !important;
  font-weight: var(--brad-font-weight-bold, 700) !important;
  cursor: pointer;
  padding: 8px 14px !important;
  border-radius: 10px !important;
  transition: all 0.2s ease;
  box-sizing: border-box;
  font-family: var(--brad-font-family, inherit) !important;
  outline: none;
  text-decoration: none;
}

.link-action:hover:not(:disabled) {
  background: var(--brand-xlight, rgba(204, 9, 47, 0.08)) !important;
  border-color: var(--brand, #cc092f) !important;
  transform: translateY(-1px);
  box-shadow: 0 4px 12px rgba(204, 9, 47, 0.15);
}

.link-action:disabled {
  opacity: 0.5;
  cursor: not-allowed;
}

.date-popover {
  position: fixed;
  background: #fff;
  border-radius: 14px;
  box-shadow: 0 18px 38px rgba(15, 20, 36, 0.18);
  border: 1px solid rgba(15, 23, 42, 0.12);
  padding: 20px;
  z-index: 1500;
  min-width: 320px;
  max-width: 400px;
}

.date-popover h4 {
  margin: 0 0 16px;
  font-size: 16px;
  font-weight: var(--brad-font-weight-bold, 700);
  color: var(--text, #0f1424);
  font-family: var(--brad-font-family, inherit);
}

.date-popover .row {
  display: flex;
  gap: 12px;
  margin-bottom: 16px;
}

.date-popover input[type="date"] {
  flex: 1;
  padding: 10px 12px;
  border: 1px solid var(--stroke, #e7eaf2);
  border-radius: 10px;
  font-size: 14px;
  font-family: var(--brad-font-family, inherit);
  transition: border-color 0.2s ease, box-shadow 0.2s ease;
  background: var(--panel, #fff);
  color: var(--text, #0f1424);
}

.date-popover input[type="date"]:focus {
  outline: none;
  border-color: var(--brand, #cc092f);
  box-shadow: 0 0 0 3px var(--brand-xlight, rgba(204, 9, 47, 0.12));
}

.date-popover .actions {
  display: flex;
  gap: 8px;
  justify-content: flex-end;
}

.date-popover .btn-sec,
.date-popover .btn-pri {
  padding: 10px 16px;
  border-radius: 10px;
  font-size: 14px;
  font-weight: var(--brad-font-weight-semibold, 600);
  cursor: pointer;
  transition: all 0.2s ease;
  font-family: var(--brad-font-family, inherit);
  border: 1px solid var(--stroke, #e7eaf2);
}

.date-popover .btn-sec {
  background: var(--panel, #fff);
  color: var(--text, #0f1424);
}

.date-popover .btn-sec:hover {
  background: rgba(0, 0, 0, 0.04);
}

.date-popover .btn-pri {
  background: linear-gradient(90deg, #cc092f 40%, #b81570 90%);
  color: #fff;
  border-color: transparent;
  box-shadow: 0 4px 12px rgba(204, 9, 47, 0.25);
}

.date-popover .btn-pri:hover {
  background: linear-gradient(90deg, #b81570 40%, #cc092f 90%);
  box-shadow: 0 6px 16px rgba(204, 9, 47, 0.35);
  transform: translateY(-1px);
}
</style>
