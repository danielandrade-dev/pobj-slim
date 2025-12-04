<script setup lang="ts">
import { ref, computed, watch, onMounted } from 'vue'

interface ChartSeries {
  id: string
  label: string
  color: string
  values: (number | null)[]
}

interface ChartData {
  keys: string[]
  labels: string[]
  series: ChartSeries[]
}

const props = defineProps<{
  chartData: ChartData
}>()

const hasData = computed(() => props.chartData.series.length > 0)
const containerRef = ref<HTMLElement | null>(null)

const renderChart = () => {
  if (!containerRef.value || !hasData.value) return
  const container = containerRef.value
  
  const W = 900
  const H = 260
  const m = { t: 28, r: 36, b: 48, l: 64 }
  const iw = W - m.l - m.r
  const ih = H - m.t - m.b
  const n = props.chartData.labels.length
  
  const x = (idx: number) => {
    if (n <= 1) return m.l + iw / 2
    const step = iw / (n - 1)
    return m.l + step * idx
  }
  
  const values = props.chartData.series.flatMap(s => s.values.filter(v => v !== null && v !== undefined))
  const maxVal = values.length ? Math.max(...values) : 0
  const yMax = Math.max(120, Math.ceil((maxVal || 100) / 10) * 10)
  
  const y = (val: number) => {
    const clamped = Math.min(Math.max(val, 0), yMax)
    return m.t + ih - (clamped / yMax) * ih
  }
  
  const gridLines = []
  const steps = 5
  for (let k = 0; k <= steps; k++) {
    const val = (yMax / steps) * k
    gridLines.push({ y: y(val), label: `${Math.round(val)}%` })
  }
  
  const paths = props.chartData.series.map(series => {
    let d = ''
    let started = false
    series.values.forEach((value, idx) => {
      if (value === null || value === undefined) {
        started = false
        return
      }
      const cmd = started ? 'L' : 'M'
      d += `${cmd} ${x(idx)} ${y(value)} `
      started = true
    })
    return `<path class="exec-line" d="${d.trim()}" fill="none" stroke="${series.color}" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><title>${series.label}</title></path>`
  }).join('')
  
  const points = props.chartData.series.map(series => 
    series.values.map((value, idx) => {
      if (value === null || value === undefined) return ''
      const monthLabel = props.chartData.labels[idx] || String(idx + 1)
      const valueLabel = `${value.toFixed(1)}%`
      return `<circle class="exec-line__point" cx="${x(idx)}" cy="${y(value)}" r="3.4" fill="${series.color}" stroke="#fff" stroke-width="1.2"><title>${series.label} • ${monthLabel}: ${valueLabel}</title></circle>`
    }).join('')
  ).join('')
  
  const gridY = gridLines.map(line =>
    `<line x1="${m.l}" y1="${line.y}" x2="${W - m.r}" y2="${line.y}" stroke="#eef2f7"/>
     <text x="${m.l - 6}" y="${line.y + 3}" font-size="10" text-anchor="end" fill="#6b7280">${line.label}</text>`
  ).join('')
  
  const xlabels = props.chartData.labels.map((lab, idx) =>
    `<text x="${x(idx)}" y="${H - 10}" font-size="10" text-anchor="middle" fill="#6b7280">${lab}</text>`
  ).join('')
  
  container.innerHTML = `
    <svg class="exec-chart-svg" viewBox="0 0 ${W} ${H}" preserveAspectRatio="none" role="img" aria-label="Linhas mensais de atingimento por seção">
      <rect x="0" y="0" width="${W}" height="${H}" fill="white"/>
      ${gridY}
      ${paths}
      ${points}
      <line x1="${m.l}" y1="${H - m.b}" x2="${W - m.r}" y2="${H - m.b}" stroke="#e5e7eb"/>
      ${xlabels}
    </svg>`
}

watch(() => props.chartData, () => {
  renderChart()
}, { deep: true })

onMounted(() => {
  renderChart()
})
</script>

<template>
  <div class="exec-chart">
    <div class="exec-head">
      <h3 id="exec-chart-title">Evolução mensal por seção</h3>
      <div id="exec-chart-legend" class="chart-legend">
        <span 
          v-for="serie in chartData.series" 
          :key="serie.id"
          class="legend-item"
        >
          <span 
            class="legend-swatch legend-swatch--line" 
            :style="{ background: serie.color, borderColor: serie.color }"
          ></span>
          {{ serie.label }}
        </span>
      </div>
    </div>
    <div ref="containerRef" id="exec-chart" class="chart"></div>
  </div>
</template>

<style scoped>
.exec-chart {
  background: var(--panel);
  border: 1px solid var(--stroke);
  border-radius: var(--radius);
  box-shadow: var(--shadow);
  padding: 20px 20px 24px;
}

.exec-head {
  display: flex;
  align-items: flex-end;
  justify-content: space-between;
  gap: 12px;
  margin-bottom: 16px;
  flex-wrap: wrap;
}

.exec-head h3 {
  margin: 0;
  font-size: 20px;
  font-weight: 700;
  color: var(--text);
  line-height: 1.2;
}

.chart {
  width: 100%;
  overflow: hidden;
  padding: 20px;
  border-radius: 12px;
  background: #fff;
}

.chart svg {
  display: block;
  width: 100%;
  height: auto;
}

.chart-legend {
  display: flex;
  gap: 12px;
  flex-wrap: wrap;
  margin-top: 8px;
}

.legend-item {
  display: inline-flex;
  align-items: center;
  gap: 6px;
  color: var(--text);
  font-weight: 600;
  font-size: 13px;
}

.legend-swatch {
  display: inline-block;
  width: 14px;
  height: 6px;
  border-radius: 999px;
  background: #cbd5e1;
  border: 1px solid #94a3b8;
  position: relative;
}

.legend-swatch--line {
  background: transparent;
  border: none;
  height: 0;
  border-top: 2.5px solid;
  width: 18px;
  margin-top: 4px;
  border-radius: 0;
}
</style>

