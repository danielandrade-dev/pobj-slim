<script setup lang="ts">
import { computed } from 'vue'
import { formatINT, formatCurrency, formatDate, formatBRL, formatBRLReadable, formatIntReadable } from '../utils/formatUtils'

export interface TreeNode {
  id: string
  label: string
  level: 'segmento' | 'diretoria' | 'regional' | 'agencia' | 'gerente' | 'familia' | 'indicador' | 'subindicador' | 'contrato'
  children: TreeNode[]
  data: any[]
  summary: {
    quantidade: number
    valor_realizado: number
    valor_meta: number
    atingimento_v: number
    atingimento_p: number
    meta_diaria: number
    referencia_hoje: number
    pontos: number
    meta_diaria_necessaria: number
    peso: number
    projecao: number
    data: string
  }
  detail?: {
    canal_venda?: string
    tipo_venda?: string
    gerente?: string
    modalidade_pagamento?: string
    dt_vencimento?: string
    dt_cancelamento?: string
    motivo_cancelamento?: string
  }
}

interface Props {
  node: TreeNode
  level: number
  expanded: boolean
  expandedRows?: Set<string>
  detailOpen?: boolean
  activeColumns?: string[]
}

const props = withDefaults(defineProps<Props>(), {
  expandedRows: () => new Set<string>(),
  detailOpen: false,
  activeColumns: () => []
})
const emit = defineEmits<{
  toggle: [id: string]
}>()

const hasChildren = computed(() => props.node.children.length > 0)

const atingimento = computed(() => {
  return props.node.summary.atingimento_p || 0
})

const atingimentoClass = computed(() => {
  const at = atingimento.value
  if (at >= 100) return 'text-success'
  if (at >= 80) return 'text-warning'
  return 'text-danger'
})

const atingimentoVClass = computed(() => {
  const { valor_realizado, valor_meta } = props.node.summary
  const diff = valor_realizado - valor_meta
  if (diff >= 0) return 'att-ok'
  return 'att-low'
})

// Função para renderizar o valor formatado (exibição)
function getColumnValue(columnId: string) {
  const summary = props.node.summary
  switch (columnId) {
    case 'quantidade':
      return formatIntReadable(summary.quantidade)
    case 'realizado':
      return formatBRLReadable(summary.valor_realizado)
    case 'meta':
      return formatBRLReadable(summary.valor_meta)
    case 'atingimento_v':
      return formatBRLReadable(summary.atingimento_v)
    case 'atingimento_p':
      return `${formatIntReadable(summary.atingimento_p)}%`
    case 'meta_diaria':
      return formatBRLReadable(summary.meta_diaria)
    case 'referencia_hoje':
      return formatBRLReadable(summary.referencia_hoje)
    case 'pontos':
      return `${formatIntReadable(summary.pontos)} pts`
    case 'meta_diaria_necessaria':
      return formatBRLReadable(summary.meta_diaria_necessaria)
    case 'peso':
      return `${formatIntReadable(summary.peso)} pts`
    case 'projecao':
      return formatBRLReadable(summary.projecao)
    case 'data':
      return formatDate(summary.data)
    default:
      return '—'
  }
}

// Função para obter o valor completo (tooltip)
function getColumnTooltip(columnId: string) {
  const summary = props.node.summary
  switch (columnId) {
    case 'quantidade':
      return formatINT(summary.quantidade)
    case 'realizado':
      return formatBRL(summary.valor_realizado)
    case 'meta':
      return formatBRL(summary.valor_meta)
    case 'atingimento_v':
      return formatBRL(summary.atingimento_v)
    case 'atingimento_p':
      return `${formatINT(summary.atingimento_p)}%`
    case 'meta_diaria':
      return formatBRL(summary.meta_diaria)
    case 'referencia_hoje':
      return formatBRL(summary.referencia_hoje)
    case 'pontos':
      return `${formatINT(summary.pontos)} pts`
    case 'meta_diaria_necessaria':
      return formatBRL(summary.meta_diaria_necessaria)
    case 'peso':
      return `${formatINT(summary.peso)} pts`
    case 'projecao':
      return formatBRL(summary.projecao)
    case 'data':
      return formatDate(summary.data)
    default:
      return ''
  }
}

const hasDetail = computed(() => {
  return props.node.level === 'contrato' && props.node.detail
})

const showDetail = computed(() => {
  return props.detailOpen && hasDetail.value
})
</script>

<template>
  <tr :class="['tree-row', 'lvl-' + level, { 'tree-row--expanded': expanded }]">
    <td>
      <div class="tree-cell">
        <button
          v-if="hasChildren || hasDetail"
          type="button"
          class="toggle"
          :class="{ 'is-expanded': expanded || showDetail }"
          @click="emit('toggle', node.id)"
        >
          <i :class="(expanded || showDetail) ? 'ti ti-chevron-down' : 'ti ti-chevron-right'"></i>
        </button>
        <span v-else class="toggle toggle--placeholder" aria-hidden="true"></span>
        <span class="label-strong">{{ node.label || '—' }}</span>
      </div>
    </td>
    <template v-for="columnId in activeColumns" :key="columnId">
      <td v-if="columnId === 'atingimento_p'" class="col-number">
        <span :class="atingimentoClass" :title="getColumnTooltip(columnId)">
          {{ getColumnValue(columnId) }}
        </span>
      </td>
      <td v-else-if="columnId === 'atingimento_v'" class="col-number">
        <span :class="atingimentoVClass" :title="getColumnTooltip(columnId)">{{ getColumnValue(columnId) }}</span>
      </td>
      <td v-else :class="columnId === 'data' ? 'col-number col-date' : 'col-number'">
        <span v-if="columnId !== 'data'" :title="getColumnTooltip(columnId)">{{ getColumnValue(columnId) }}</span>
        <span v-else>{{ getColumnValue(columnId) }}</span>
      </td>
    </template>
  </tr>
  
  <!-- Linha de detalhes do contrato -->
  <tr v-if="showDetail" class="tree-row tree-detail-row">
    <td :colspan="activeColumns.length + 1" class="tree-detail-cell">
      <div class="tree-detail-wrapper">
        <div class="tree-detail">
          <div v-if="node.detail?.canal_venda" class="tree-chip">
            <strong>Canal:</strong> {{ node.detail.canal_venda }}
          </div>
          <div v-if="node.detail?.tipo_venda" class="tree-chip">
            <strong>Tipo:</strong> {{ node.detail.tipo_venda }}
          </div>
          <div v-if="node.detail?.gerente" class="tree-chip">
            <strong>Gerente:</strong> {{ node.detail.gerente }}
          </div>
          <div v-if="node.detail?.modalidade_pagamento" class="tree-chip">
            <strong>Pagamento:</strong> {{ node.detail.modalidade_pagamento }}
          </div>
          <div v-if="node.detail?.dt_vencimento" class="tree-chip">
            <strong>Vencimento:</strong> {{ formatDate(node.detail.dt_vencimento) }}
          </div>
          <div v-if="node.detail?.dt_cancelamento" class="tree-chip">
            <strong>Cancelamento:</strong> {{ formatDate(node.detail.dt_cancelamento) }}
          </div>
          <div v-if="node.detail?.motivo_cancelamento" class="tree-chip">
            <strong>Motivo:</strong> {{ node.detail.motivo_cancelamento }}
          </div>
        </div>
      </div>
    </td>
  </tr>
  
  <template v-if="expanded && hasChildren">
      <TreeTableRow
        v-for="child in node.children"
        :key="child.id"
        :node="child"
        :level="level + 1"
        :expanded="expandedRows?.has(child.id) || false"
        :expanded-rows="expandedRows"
        :detail-open="false"
        :active-columns="activeColumns"
        @toggle="emit('toggle', $event)"
      />
  </template>
</template>

<style scoped>
.tree-row {
  transition: background 0.15s ease;
}

.tree-row:hover {
  background: #fcfdff;
}

.tree-row--expanded {
  background: #f9fafb;
}

.toggle {
  flex: 0 0 28px;
  width: 28px;
  height: 26px;
  min-width: 28px;
  display: grid;
  place-items: center;
  border: 1px solid #e5e7eb;
  border-radius: 8px;
  background: #fff;
  cursor: pointer;
  margin-right: 4px;
  transition: all 0.15s ease;
  color: #475569;
  box-sizing: border-box;
  flex-shrink: 0;
}

.toggle--placeholder {
  visibility: hidden;
  pointer-events: none;
}

.toggle:hover {
  box-shadow: 0 4px 10px rgba(17, 23, 41, 0.08);
  transform: translateY(-1px);
  border-color: #1d4ed8;
}

.toggle.is-expanded {
  background: rgba(199, 210, 254, 0.35);
  border-color: #1d4ed8;
  color: #1d4ed8;
}

.toggle[disabled] {
  opacity: 0.45;
  cursor: default;
}

.toggle i {
  font-size: 16px;
  line-height: 1;
}

.label-strong {
  font-weight: 800;
  color: #111827;
  line-height: 1.25;
  font-size: 13px;
}

.text-success {
  color: var(--omega-success, #16a34a);
  font-weight: 800;
}

.text-warning {
  color: var(--omega-warning, #f97316);
  font-weight: 800;
}

.text-danger {
  color: var(--omega-danger, #dc2626);
  font-weight: 800;
}

.tree-detail-row td {
  background: #f9fafb;
  padding: 14px 18px;
  border-bottom: 1px solid #e5e7eb;
}

.tree-detail-row:last-of-type td {
  border-bottom: none;
}

.tree-detail-wrapper {
  background: #ffffff;
  border: 1px solid #e2e8f0;
  border-radius: 12px;
  padding: 12px;
  box-shadow: inset 0 0 0 1px rgba(255, 255, 255, 0.4);
}

.tree-detail {
  display: flex;
  flex-wrap: wrap;
  gap: 6px;
  margin-top: 4px;
}

.tree-chip {
  display: inline-flex;
  align-items: center;
  gap: 4px;
  padding: 4px 10px;
  border-radius: 999px;
  background: #eef2ff;
  border: 1px solid #dbeafe;
  font-size: 11px;
  font-weight: 700;
  color: #475569;
  white-space: nowrap;
}

.tree-chip strong {
  font-weight: 800;
  color: #1e293b;
}

/* Cores condicionais para Atingimento */
.att-ok {
  background: #dcfce7;
  color: #166534;
  padding: 4px 8px;
  border-radius: 999px;
  display: inline-block;
  font-weight: 800;
  text-align: center;
}

.att-low {
  background: #fee2e2;
  color: #991b1b;
  padding: 4px 8px;
  border-radius: 999px;
  display: inline-block;
  font-weight: 800;
  text-align: center;
}

.att-warn {
  background: #fef3c7;
  color: #92400e;
  padding: 4px 8px;
  border-radius: 999px;
  display: inline-block;
  font-weight: 800;
  text-align: center;
}
</style>

