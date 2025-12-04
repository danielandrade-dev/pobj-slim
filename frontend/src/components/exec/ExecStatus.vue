<script setup lang="ts">
import { formatBRLReadable } from '../../utils/formatUtils'

interface StatusItem {
  key: string
  label: string
  p_mens: number
  gap?: number
}

interface StatusItemLonge {
  key: string
  label: string
  gap: number
}

interface Status {
  hit: StatusItem[]
  quase: StatusItem[]
  longe: StatusItemLonge[]
}

const props = defineProps<{
  status: Status
}>()
</script>

<template>
  <div class="exec-panel">
    <div class="exec-h">
      <h3 id="exec-status-title">Status das Regionais</h3>
    </div>
    <div class="exec-status">
      <div class="status-section">
        <h4>Hit (≥100%)</h4>
        <div id="exec-status-hit" class="list-mini">
          <div 
            v-for="item in status.hit" 
            :key="item.key"
            class="list-mini__row"
          >
            <div class="list-mini__name">{{ item.label }}</div>
            <div class="list-mini__val">
              <span class="att-badge att-ok">{{ item.p_mens.toFixed(1) }}%</span>
            </div>
          </div>
        </div>
      </div>
      <div class="status-section">
        <h4>Quase (90-99%)</h4>
        <div id="exec-status-quase" class="list-mini">
          <div 
            v-for="item in status.quase" 
            :key="item.key"
            class="list-mini__row"
          >
            <div class="list-mini__name">{{ item.label }}</div>
            <div class="list-mini__val">
              <span class="att-badge att-warn">{{ item.p_mens.toFixed(1) }}%</span>
            </div>
          </div>
        </div>
      </div>
      <div class="status-section">
        <h4>Longe (maior defasagem)</h4>
        <div id="exec-status-longe" class="list-mini">
          <div 
            v-for="item in status.longe" 
            :key="item.key"
            class="list-mini__row"
          >
            <div class="list-mini__name">{{ item.label }}</div>
            <div class="list-mini__val">
              <span class="def-badge def-neg">{{ formatBRLReadable(item.gap || 0) }}</span>
            </div>
          </div>
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

.exec-status {
  display: flex;
  flex-direction: column;
  gap: 20px;
}

.status-section h4 {
  margin: 0 0 12px 0;
  font-size: 14px;
  font-weight: 600;
  color: var(--muted);
  text-transform: uppercase;
  letter-spacing: 0.5px;
}

.list-mini {
  display: flex;
  flex-direction: column;
  gap: 8px;
}

.list-mini__row {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 12px;
  border-radius: 10px;
  transition: background 0.2s ease, transform 0.15s ease;
  cursor: pointer;
}

.list-mini__row:hover {
  background: var(--bg);
  transform: translateX(2px);
}

.list-mini__name {
  font-size: 13px;
  font-weight: 500;
  color: var(--text);
  flex: 1;
  min-width: 0;
  overflow: hidden;
  text-overflow: ellipsis;
  white-space: nowrap;
}

.list-mini__val {
  flex-shrink: 0;
}

.att-badge {
  display: inline-flex;
  align-items: center;
  padding: 4px 10px;
  border-radius: 6px;
  font-size: 12px;
  font-weight: 700;
}

.att-badge.att-ok {
  background: rgba(187, 247, 208, 0.3);
  color: #065f46;
}

.att-badge.att-warn {
  background: rgba(254, 215, 170, 0.3);
  color: #92400e;
}

.def-badge {
  display: inline-flex;
  align-items: center;
  padding: 4px 10px;
  border-radius: 6px;
  font-size: 12px;
  font-weight: 700;
}

.def-badge.def-neg {
  background: rgba(254, 202, 202, 0.3);
  color: #991b1b;
}
</style>

