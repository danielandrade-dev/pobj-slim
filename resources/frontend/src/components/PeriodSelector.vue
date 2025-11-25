<script setup lang="ts">
import { ref, watch, onMounted, onUnmounted } from 'vue'
import { getCalendario, getDefaultPeriod, formatBRDate, type CalendarioItem } from '../services/calendarioService'

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
const showPopover = ref(false)
const calendarioData = ref<CalendarioItem[]>([])
const popoverRef = ref<HTMLElement | null>(null)
const buttonRef = ref<HTMLElement | null>(null)

// Sincroniza com prop externa
watch(() => props.modelValue, (newValue) => {
  if (newValue) {
    period.value = { ...newValue }
  }
}, { deep: true })

const loadCalendario = async (): Promise<void> => {
  try {
    const data = await getCalendario()
    if (data) {
      calendarioData.value = data
    }
  } catch (error) {
    console.error('Erro ao carregar calendário:', error)
  }
}

onMounted(() => {
  loadCalendario()
  if (!props.modelValue) {
    const defaultPeriod = getDefaultPeriod()
    period.value = defaultPeriod
    emit('update:modelValue', defaultPeriod)
  }
})

const openPopover = (): void => {
  showPopover.value = true
  // Posiciona o popover após renderizar
  setTimeout(() => {
    positionPopover()
  }, 0)
}

const positionPopover = (): void => {
  if (!popoverRef.value || !buttonRef.value) return

  const buttonRect = buttonRef.value.getBoundingClientRect()
  const popover = popoverRef.value
  const popoverWidth = popover.offsetWidth || 340
  const popoverHeight = popover.offsetHeight || 170
  const pad = 12
  const vw = window.innerWidth
  const vh = window.innerHeight

  let top = buttonRect.bottom + 8
  let left = buttonRect.right - popoverWidth

  if (top + popoverHeight + pad > vh) {
    top = Math.max(pad, buttonRect.top - popoverHeight - 8)
  }
  if (left < pad) left = pad
  if (left + popoverWidth + pad > vw) {
    left = Math.max(pad, vw - popoverWidth - pad)
  }

  popover.style.top = `${top}px`
  popover.style.left = `${left}px`
}

const closePopover = (): void => {
  showPopover.value = false
}

const savePeriod = (): void => {
  if (!period.value.start || !period.value.end) {
    alert('Período inválido.')
    return
  }

  if (new Date(period.value.start) > new Date(period.value.end)) {
    alert('Data inicial não pode ser maior que data final.')
    return
  }

  emit('update:modelValue', { ...period.value })
  closePopover()
}

const handleClickOutside = (event: MouseEvent): void => {
  const target = event.target as HTMLElement
  if (
    showPopover.value &&
    popoverRef.value &&
    !popoverRef.value.contains(target) &&
    buttonRef.value &&
    !buttonRef.value.contains(target)
  ) {
    closePopover()
  }
}

const handleEscape = (event: KeyboardEvent): void => {
  if (event.key === 'Escape' && showPopover.value) {
    closePopover()
  }
}

onMounted(() => {
  document.addEventListener('mousedown', handleClickOutside)
  document.addEventListener('keydown', handleEscape)
  window.addEventListener('resize', positionPopover)
})

onUnmounted(() => {
  document.removeEventListener('mousedown', handleClickOutside)
  document.removeEventListener('keydown', handleEscape)
  window.removeEventListener('resize', positionPopover)
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
        @click="openPopover"
      >
        <i class="ti ti-chevron-down"></i> Alterar data
      </button>
    </div>

    <Teleport to="body">
      <div v-if="showPopover" ref="popoverRef" class="date-popover" id="date-popover">
      <h4>Alterar data</h4>
      <div class="row">
        <input
          id="inp-start"
          v-model="period.start"
          type="date"
          aria-label="Data inicial"
        />
        <input
          id="inp-end"
          v-model="period.end"
          type="date"
          aria-label="Data final"
        />
      </div>
      <div class="actions">
        <button type="button" class="btn-sec" @click="closePopover">Cancelar</button>
        <button type="button" class="btn-pri" @click="savePeriod">Salvar</button>
      </div>
      </div>
    </Teleport>
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
}

.period-inline strong {
  color: var(--text, #0f1424);
  font-weight: 700;
}

.link-action {
  display: inline-flex;
  align-items: center;
  gap: 4px;
  background: transparent;
  border: none;
  color: var(--brand, #b30000);
  font-size: 13px;
  font-weight: 600;
  cursor: pointer;
  padding: 4px 8px;
  border-radius: 6px;
  transition: background 0.2s ease;
}

.link-action:hover {
  background: rgba(179, 0, 0, 0.08);
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
  font-weight: 700;
  color: var(--text, #0f1424);
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
  font-family: inherit;
  transition: border-color 0.2s ease, box-shadow 0.2s ease;
}

.date-popover input[type="date"]:focus {
  outline: none;
  border-color: var(--brand, #b30000);
  box-shadow: 0 0 0 3px rgba(179, 0, 0, 0.12);
}

.date-popover .actions {
  display: flex;
  gap: 8px;
  justify-content: flex-end;
}

.btn-sec,
.btn-pri {
  padding: 10px 16px;
  border-radius: 10px;
  font-size: 14px;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.2s ease;
  font-family: inherit;
  border: 1px solid var(--stroke, #e7eaf2);
}

.btn-sec {
  background: #fff;
  color: var(--text, #0f1424);
}

.btn-sec:hover {
  background: rgba(0, 0, 0, 0.04);
}

.btn-pri {
  background: linear-gradient(90deg, var(--brand, #b30000), var(--brand-dark, #8f0000));
  color: #fff;
  border-color: transparent;
  box-shadow: 0 4px 12px rgba(179, 0, 0, 0.25);
}

.btn-pri:hover {
  background: linear-gradient(90deg, var(--brand-dark, #8f0000), var(--brand, #b30000));
  box-shadow: 0 6px 16px rgba(179, 0, 0, 0.35);
  transform: translateY(-1px);
}
</style>

