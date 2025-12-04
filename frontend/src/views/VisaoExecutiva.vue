<script setup lang="ts">
import { ref, computed, watch, onMounted } from 'vue'
import { useGlobalFilters } from '../composables/useGlobalFilters'
import { usePDFExport } from '../composables/usePDFExport'
import { formatDate } from '../utils/formatUtils'
import { getExecData, type ExecFilters, type ExecData } from '../services/execService'
import ExecChart from '../components/exec/ExecChart.vue'
import ExecKPIs from '../components/exec/ExecKPIs.vue'
import ExecRankings from '../components/exec/ExecRankings.vue'
import ExecStatus from '../components/exec/ExecStatus.vue'
import ExecHeatmap from '../components/exec/ExecHeatmap.vue'

const { filterState, period } = useGlobalFilters()

const execData = ref<ExecData | null>(null)
const loading = ref(false)
const error = ref<string | null>(null)

const kpis = computed(() => execData.value?.kpis || {
  real_mens: 0,
  meta_mens: 0,
  real_acum: 0,
  meta_acum: 0
})

const ranking = computed(() => execData.value?.ranking || [])

const status = computed(() => execData.value?.status || {
  hit: [],
  quase: [],
  longe: []
})

const chartData = computed(() => execData.value?.chart || {
  keys: [],
  labels: [],
  series: []
})

const heatmap = computed(() => execData.value?.heatmap || {
  units: [],
  sections: [],
  data: {}
})


const execFilters = computed<ExecFilters>(() => {
  const filters: ExecFilters = {}
  
  if (filterState.value.segmento && filterState.value.segmento.toLowerCase() !== 'todos') {
    filters.segmento = filterState.value.segmento
  }
  if (filterState.value.diretoria && filterState.value.diretoria.toLowerCase() !== 'todas') {
    filters.diretoria = filterState.value.diretoria
  }
  if (filterState.value.gerencia && filterState.value.gerencia.toLowerCase() !== 'todas') {
    filters.regional = filterState.value.gerencia
  }
  if (filterState.value.agencia && filterState.value.agencia.toLowerCase() !== 'todas') {
    filters.agencia = filterState.value.agencia
  }
  if (filterState.value.ggestao && filterState.value.ggestao.toLowerCase() !== 'todos') {
    filters.gerenteGestao = filterState.value.ggestao
  }
  if (filterState.value.gerente && filterState.value.gerente.toLowerCase() !== 'todos') {
    filters.gerente = filterState.value.gerente
  }
  if (period.value?.start) {
    filters.dataInicio = period.value.start
  }
  if (period.value?.end) {
    filters.dataFim = period.value.end
  }
  
  return filters
})


const topRanking = computed(() => {
  return [...ranking.value]
    .sort((a, b) => b.p_mens - a.p_mens)
    .slice(0, 5)
})

const bottomRanking = computed(() => {
  return [...ranking.value]
    .sort((a, b) => a.p_mens - b.p_mens)
    .slice(0, 5)
    .reverse()
})

const loadData = async () => {
  loading.value = true
  error.value = null
  
  try {
    const data = await getExecData(execFilters.value)
    if (data) {
      execData.value = data
    } else {
      error.value = 'Erro ao carregar dados executivos'
    }
  } catch (err) {
    error.value = err instanceof Error ? err.message : 'Erro desconhecido'
    console.error('Erro ao carregar dados executivos:', err)
  } finally {
    loading.value = false
  }
}

watch([execFilters, period], () => {
  loadData()
}, { deep: true })

onMounted(() => {
  loadData()
})

const contexto = computed(() => {
  const f = filterState.value
  const foco =
    f.gerente && f.gerente !== 'Todos' ? `Gerente: ${f.gerente}` :
    f.ggestao && f.ggestao !== 'Todos' ? `GG: ${f.ggestao}` :
    f.agencia && f.agencia !== 'Todas' ? `Agência: ${f.agencia}` :
    f.gerencia && f.gerencia !== 'Todas' ? `GR: ${f.gerencia}` :
    f.diretoria && f.diretoria !== 'Todas' ? `Diretoria: ${f.diretoria}` :
    'Todas as Diretorias'
  
  return `${foco} · Período: ${formatDate(period.value.start)} a ${formatDate(period.value.end)}`
})

const { exportToPDF } = usePDFExport()

const downloadPDF = async () => {
  const button = document.querySelector('.btn-download-pdf') as HTMLElement
  
  try {
    if (button) {
      button.style.opacity = '0.6'
      button.style.pointerEvents = 'none'
    }

    await exportToPDF('view-exec')

    if (button) {
      button.style.opacity = '1'
      button.style.pointerEvents = 'auto'
    }
  } catch (error) {
    console.error('Erro ao gerar PDF:', error)
    alert('Erro ao gerar PDF. Tente novamente.')
    
    if (button) {
      button.style.opacity = '1'
      button.style.pointerEvents = 'auto'
    }
  }
}
</script>

<template>
  <div class="exec-wrapper">
    <div id="view-exec" class="exec-view">
        <!-- Skeleton Loading -->
        <template v-if="loading">
          <div class="skeleton skeleton--context" style="height: 24px; width: 200px; margin-bottom: 24px; border-radius: 6px;"></div>
          <div class="exec-kpis">
            <div class="skeleton skeleton--kpi-card" style="height: 140px; border-radius: 12px;"></div>
            <div class="skeleton skeleton--kpi-card" style="height: 140px; border-radius: 12px;"></div>
            <div class="skeleton skeleton--kpi-card" style="height: 140px; border-radius: 12px;"></div>
          </div>
          <div class="exec-chart" style="margin-top: 24px;">
            <div class="skeleton skeleton--chart-title" style="height: 24px; width: 250px; margin-bottom: 16px; border-radius: 6px;"></div>
            <div class="skeleton skeleton--chart" style="height: 300px; width: 100%; border-radius: 12px;"></div>
          </div>
          <div class="exec-panels" style="margin-top: 24px; display: grid; grid-template-columns: 1fr 1fr; gap: 24px;">
            <div class="skeleton skeleton--panel" style="height: 400px; border-radius: 12px;"></div>
            <div class="skeleton skeleton--panel" style="height: 400px; border-radius: 12px;"></div>
          </div>
        </template>

        <!-- Conteúdo real -->
        <template v-else>
          <!-- Contexto e Botão PDF -->
          <div class="exec-header">
            <div id="exec-context" class="exec-context">
              <strong>{{ contexto }}</strong>
            </div>
            <button class="btn-download-pdf" @click="downloadPDF" title="Baixar como PDF">
              <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                <polyline points="7 10 12 15 17 10"></polyline>
                <line x1="12" y1="15" x2="12" y2="3"></line>
              </svg>
              <span>Baixar PDF</span>
            </button>
          </div>

          <!-- KPIs -->
          <ExecKPIs :kpis="kpis" />

          <!-- Gráfico -->
          <ExecChart :chart-data="chartData" />

          <!-- Rankings e Status -->
          <div class="exec-panels">
            <ExecRankings :top-ranking="topRanking" :bottom-ranking="bottomRanking" />
            <ExecStatus :status="status" />
          </div>

          <!-- Heatmap -->
          <ExecHeatmap :heatmap="heatmap" />
        </template>
      </div>
    </div>
</template>

<style scoped>
.exec-wrapper {
  --brand: var(--brad-color-primary, #cc092f);
  --brand-dark: var(--brad-color-primary-dark, #9d0b21);
  --info: var(--brad-color-accent, #517bc5);
  --bg: var(--brad-color-neutral-0, #fff);
  --panel: var(--brad-color-neutral-0, #fff);
  --stroke: var(--brad-color-gray-light, #ebebeb);
  --text: var(--brad-color-neutral-100, #000);
  --muted: var(--brad-color-gray-dark, #999);
  --radius: 16px;
  --shadow: 0 12px 28px rgba(17, 23, 41, 0.08);
  --ring: 0 0 0 3px rgba(204, 9, 47, 0.12);

  min-height: 100vh;
  width: 100%;
  padding: 20px 0;
  color: var(--text);
  font-family: var(--brad-font-family);
  -webkit-font-smoothing: antialiased;
  -moz-osx-font-smoothing: grayscale;
  box-sizing: border-box;
}

.exec-view {
  margin-top: 24px;
  display: flex;
  flex-direction: column;
  gap: 18px;
}

.exec-context {
  padding: 14px 18px;
  background: var(--panel);
  border: 1px solid var(--stroke);
  border-radius: var(--radius);
  box-shadow: var(--shadow);
  font-size: 14px;
  color: var(--text);
  line-height: 1.5;
}

.exec-context strong {
  color: var(--text);
  font-weight: var(--brad-font-weight-semibold, 600);
}

.exec-panels {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(420px, 1fr));
  gap: 18px;
}

/* Skeleton Loading */
.skeleton {
  background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
  background-size: 200% 100%;
  animation: skeleton-loading 1.5s ease-in-out infinite;
  border-radius: 8px;
}

@keyframes skeleton-loading {
  0% {
    background-position: 200% 0;
  }
  100% {
    background-position: -200% 0;
  }
}

.exec-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 16px;
  margin-bottom: 0;
  flex-wrap: wrap;
}

.btn-download-pdf {
  display: inline-flex;
  align-items: center;
  gap: 8px;
  padding: 10px 18px;
  background: var(--brand);
  color: white;
  border: none;
  border-radius: var(--radius);
  font-size: 14px;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.2s ease;
  box-shadow: var(--shadow);
  white-space: nowrap;
}

.btn-download-pdf:hover {
  background: var(--brand-dark);
  transform: translateY(-1px);
  box-shadow: 0 16px 36px rgba(204, 9, 47, 0.2);
}

.btn-download-pdf:active {
  transform: translateY(0);
}

.btn-download-pdf svg {
  flex-shrink: 0;
}

@media (max-width: 768px) {
  .exec-panels {
    grid-template-columns: 1fr;
  }

  .exec-header {
    flex-direction: column;
    align-items: stretch;
  }

  .btn-download-pdf {
    width: 100%;
    justify-content: center;
  }
}

</style>

