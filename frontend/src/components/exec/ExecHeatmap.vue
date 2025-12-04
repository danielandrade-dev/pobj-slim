<script setup lang="ts">
import { ref } from 'vue'

interface HeatmapSection {
  id: string
  label: string
}

interface HeatmapUnit {
  value: string
  label: string
}

interface HeatmapData {
  [key: string]: {
    real: number
    meta: number
  }
}

interface Heatmap {
  units: HeatmapUnit[]
  sections: HeatmapSection[]
  data: HeatmapData
}

const props = defineProps<{
  heatmap: Heatmap
}>()

const heatmapMode = ref<'secoes' | 'meta'>('secoes')

const getHeatmapCellClass = (pct: number | null): string => {
  if (pct === null) return 'hm-empty'
  if (pct < 50) return 'hm-bad'
  if (pct < 100) return 'hm-warn'
  return 'hm-ok'
}

const getHeatmapValue = (unit: string, section: string): { pct: number | null; text: string } => {
  const key = `${unit}|${section}`
  const bucket = props.heatmap.data[key]
  if (!bucket) return { pct: null, text: '—' }
  
  if (bucket.meta > 0) {
    const pct = (bucket.real / bucket.meta) * 100
    return { pct, text: `${Math.round(pct)}%` }
  }
  
  if (bucket.real > 0) {
    return { pct: null, text: '—' }
  }
  
  return { pct: null, text: '—' }
}
</script>

<template>
  <div class="exec-panel">
    <div class="exec-h">
      <h3 id="exec-heatmap-title">Heatmap — Regionais × Seções</h3>
      <div id="exec-heatmap-toggle" class="seg-mini segmented">
        <button 
          class="seg-btn" 
          :class="{ 'is-active': heatmapMode === 'secoes' }"
          data-hm="secoes"
          @click="heatmapMode = 'secoes'"
        >
          Seções
        </button>
        <button 
          class="seg-btn" 
          :class="{ 'is-active': heatmapMode === 'meta' }"
          data-hm="meta"
          @click="heatmapMode = 'meta'"
        >
          Variação Meta
        </button>
      </div>
    </div>
    <div id="exec-heatmap" class="exec-heatmap">
      <div class="hm-row hm-head" :style="`--hm-cols: ${heatmap.sections.length}; --hm-first: 240px; --hm-cell: 136px`">
        <div class="hm-cell hm-corner">Regional \ Família</div>
        <div 
          v-for="section in heatmap.sections" 
          :key="section.id"
          class="hm-cell hm-col"
          :title="section.label"
        >
          {{ section.label }}
        </div>
      </div>
      <div 
        v-for="unit in heatmap.units" 
        :key="unit.value"
        class="hm-row"
        :style="`--hm-cols: ${heatmap.sections.length}; --hm-first: 240px; --hm-cell: 136px`"
      >
        <div class="hm-cell hm-rowh" :title="unit.label">{{ unit.label }}</div>
        <div 
          v-for="section in heatmap.sections" 
          :key="section.id"
          class="hm-cell hm-val"
          :class="getHeatmapCellClass(getHeatmapValue(unit.value, section.id).pct)"
          :title="`${unit.label} × ${section.label}: ${getHeatmapValue(unit.value, section.id).text}`"
        >
          {{ getHeatmapValue(unit.value, section.id).text }}
        </div>
      </div>
    </div>
  </div>
</template>

<style scoped>
.exec-panel {
  background: var(--panel);
  border: 1px solid var(--stroke);
  border-radius: var(--radius);
  box-shadow: var(--shadow);
  padding: 20px;
  transition: transform 0.18s ease, box-shadow 0.18s ease;
}

.exec-panel:hover {
  box-shadow: 0 16px 36px rgba(0, 0, 0, 0.1);
}

.exec-h {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 10px;
  margin-bottom: 16px;
  flex-wrap: wrap;
}

.exec-h h3 {
  margin: 0;
  font-size: 20px;
  font-weight: 700;
  color: var(--text);
  line-height: 1.2;
}

.exec-heatmap {
  overflow-x: auto;
}

.hm-row {
  display: grid;
  grid-template-columns: var(--hm-first) repeat(var(--hm-cols), var(--hm-cell));
  gap: 1px;
  background: var(--stroke);
}

.hm-row.hm-head {
  background: var(--bg);
  border-bottom: 2px solid var(--stroke);
}

.hm-cell {
  padding: 10px 12px;
  background: var(--panel);
  font-size: 12px;
  font-weight: 600;
  color: var(--text);
  text-align: center;
  display: flex;
  align-items: center;
  justify-content: center;
}

.hm-cell.hm-corner {
  text-align: left;
  justify-content: flex-start;
  font-weight: 700;
  color: var(--text);
}

.hm-cell.hm-col {
  font-weight: 700;
  color: var(--muted);
  text-transform: uppercase;
  letter-spacing: 0.5px;
  font-size: 11px;
}

.hm-cell.hm-rowh {
  text-align: left;
  justify-content: flex-start;
  font-weight: 500;
  color: var(--text);
}

.hm-cell.hm-val {
  cursor: pointer;
  transition: background 0.2s ease;
}

.hm-cell.hm-val:hover {
  background: var(--bg);
}

.hm-cell.hm-ok {
  background: rgba(187, 247, 208, 0.4);
  color: #065f46;
  font-weight: 700;
}

.hm-cell.hm-warn {
  background: rgba(254, 215, 170, 0.4);
  color: #92400e;
  font-weight: 700;
}

.hm-cell.hm-bad {
  background: rgba(254, 202, 202, 0.4);
  color: #991b1b;
  font-weight: 700;
}

.hm-cell.hm-empty {
  background: var(--bg);
  color: var(--muted);
}

.seg-mini.segmented {
  padding: 2px;
  border-radius: 8px;
  background: var(--bg);
  display: inline-flex;
  gap: 2px;
}

.seg-mini .seg-btn {
  padding: 6px 12px;
  font-size: 12px;
  font-weight: 600;
  border: none;
  background: transparent;
  color: var(--muted);
  border-radius: 6px;
  cursor: pointer;
  transition: all 0.2s ease;
}

.seg-mini .seg-btn.is-active {
  background: var(--panel);
  color: var(--text);
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
}
</style>

