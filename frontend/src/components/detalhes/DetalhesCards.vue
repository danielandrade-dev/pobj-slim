<script setup lang="ts">
import type { DetalhesItem } from '../../api/modules/pobj.api'
import { formatCurrency, formatINT, formatDate } from '../../utils/formatUtils'

interface ContratoCard {
  id: string
  contratoId: string
  items: DetalhesItem[]
  summary: {
    valor_realizado: number
    valor_meta: number
    atingimento_p: number
    pontos: number
    peso: number
  }
  detail: {
    canal_venda?: string
    tipo_venda?: string
    gerente?: string
    gerente_gestao?: string
    modalidade_pagamento?: string
    dt_vencimento?: string
    dt_cancelamento?: string
    motivo_cancelamento?: string
  }
}

defineProps<{
  contratos: ContratoCard[]
}>()
</script>

<template>
  <div class="contratos-grid">
    <div
      v-for="contrato in contratos"
      :key="contrato.id"
      class="contrato-card"
    >
      <div class="contrato-card__header">
        <h4 class="contrato-card__title">{{ contrato.contratoId }}</h4>
        <div
          class="contrato-card__badge"
          :class="{
            'is-success': contrato.summary.atingimento_p >= 100,
            'is-warning': contrato.summary.atingimento_p >= 50 && contrato.summary.atingimento_p < 100,
            'is-danger': contrato.summary.atingimento_p < 50
          }"
        >
          {{ contrato.summary.atingimento_p.toFixed(1) }}%
        </div>
      </div>

      <div class="contrato-card__body">
        <div class="contrato-card__info">
          <div class="contrato-card__info-item">
            <span class="contrato-card__label">Gerente:</span>
            <span class="contrato-card__value">{{ contrato.detail?.gerente || '—' }}</span>
          </div>
          <div v-if="contrato.detail?.gerente_gestao" class="contrato-card__info-item">
            <span class="contrato-card__label">Gerente de gestão:</span>
            <span class="contrato-card__value">{{ contrato.detail.gerente_gestao }}</span>
          </div>
          <div class="contrato-card__info-item">
            <span class="contrato-card__label">Canal:</span>
            <span class="contrato-card__value">{{ contrato.detail?.canal_venda || '—' }}</span>
          </div>
          <div class="contrato-card__info-item">
            <span class="contrato-card__label">Tipo:</span>
            <span class="contrato-card__value">{{ contrato.detail?.tipo_venda || '—' }}</span>
          </div>
          <div class="contrato-card__info-item">
            <span class="contrato-card__label">Modalidade:</span>
            <span class="contrato-card__value">{{ contrato.detail?.modalidade_pagamento || '—' }}</span>
          </div>
        </div>

        <div class="contrato-card__metrics">
          <div class="contrato-card__metric">
            <span class="contrato-card__metric-label">Realizado</span>
            <span class="contrato-card__metric-value">{{ formatCurrency(contrato.summary.valor_realizado) }}</span>
          </div>
          <div class="contrato-card__metric">
            <span class="contrato-card__metric-label">Meta</span>
            <span class="contrato-card__metric-value">{{ formatCurrency(contrato.summary.valor_meta) }}</span>
          </div>
          <div class="contrato-card__metric">
            <span class="contrato-card__metric-label">Pontos</span>
            <span class="contrato-card__metric-value">{{ formatINT(contrato.summary.pontos) }}</span>
          </div>
          <div class="contrato-card__metric">
            <span class="contrato-card__metric-label">Peso</span>
            <span class="contrato-card__metric-value">{{ formatINT(contrato.summary.peso) }}</span>
          </div>
        </div>

        <div v-if="contrato.detail?.dt_vencimento" class="contrato-card__dates">
          <div class="contrato-card__date-item">
            <span class="contrato-card__date-label">Vencimento:</span>
            <span class="contrato-card__date-value">{{ formatDate(contrato.detail.dt_vencimento) }}</span>
          </div>
          <div v-if="contrato.detail.dt_cancelamento" class="contrato-card__date-item">
            <span class="contrato-card__date-label">Cancelamento:</span>
            <span class="contrato-card__date-value">{{ formatDate(contrato.detail.dt_cancelamento) }}</span>
          </div>
        </div>

        <div v-if="contrato.detail?.motivo_cancelamento" class="contrato-card__cancelamento">
          <span class="contrato-card__cancelamento-label">Motivo:</span>
          <span class="contrato-card__cancelamento-value">{{ contrato.detail.motivo_cancelamento }}</span>
        </div>
      </div>
    </div>
  </div>
</template>

<style scoped>
.contratos-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
  gap: 16px;
  margin-top: 16px;
}

.contrato-card {
  background: var(--panel, #fff);
  border: 1px solid var(--stroke, #e7eaf2);
  border-radius: 12px;
  padding: 16px;
  transition: all 0.2s ease;
}

.contrato-card:hover {
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
  transform: translateY(-2px);
}

.contrato-card__header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 12px;
  padding-bottom: 12px;
  border-bottom: 1px solid var(--stroke, #e7eaf2);
}

.contrato-card__title {
  font-size: 16px;
  font-weight: 700;
  color: var(--text, #0f1424);
  margin: 0;
}

.contrato-card__badge {
  padding: 4px 12px;
  border-radius: 6px;
  font-size: 12px;
  font-weight: 700;
}

.contrato-card__badge.is-success {
  background: rgba(16, 185, 129, 0.1);
  color: #10b981;
}

.contrato-card__badge.is-warning {
  background: rgba(249, 115, 22, 0.1);
  color: #f97316;
}

.contrato-card__badge.is-danger {
  background: rgba(239, 68, 68, 0.1);
  color: #ef4444;
}

.contrato-card__body {
  display: flex;
  flex-direction: column;
  gap: 12px;
}

.contrato-card__info {
  display: flex;
  flex-direction: column;
  gap: 8px;
}

.contrato-card__info-item {
  display: flex;
  justify-content: space-between;
  font-size: 13px;
}

.contrato-card__label {
  color: var(--muted, #6b7280);
  font-weight: 500;
}

.contrato-card__value {
  color: var(--text, #0f1424);
  font-weight: 600;
}

.contrato-card__metrics {
  display: grid;
  grid-template-columns: repeat(2, 1fr);
  gap: 12px;
  padding: 12px;
  background: var(--bg, #f6f7fc);
  border-radius: 8px;
}

.contrato-card__metric {
  display: flex;
  flex-direction: column;
  gap: 4px;
}

.contrato-card__metric-label {
  font-size: 11px;
  color: var(--muted, #6b7280);
  font-weight: 500;
  text-transform: uppercase;
  letter-spacing: 0.05em;
}

.contrato-card__metric-value {
  font-size: 16px;
  font-weight: 700;
  color: var(--text, #0f1424);
}

.contrato-card__dates {
  display: flex;
  flex-direction: column;
  gap: 6px;
  font-size: 12px;
}

.contrato-card__date-item {
  display: flex;
  justify-content: space-between;
}

.contrato-card__date-label {
  color: var(--muted, #6b7280);
}

.contrato-card__date-value {
  color: var(--text, #0f1424);
  font-weight: 600;
}

.contrato-card__cancelamento {
  padding: 8px;
  background: rgba(239, 68, 68, 0.05);
  border-left: 3px solid #ef4444;
  border-radius: 4px;
  font-size: 12px;
}

.contrato-card__cancelamento-label {
  color: var(--muted, #6b7280);
  font-weight: 600;
  margin-right: 6px;
}

.contrato-card__cancelamento-value {
  color: #dc2626;
}
</style>
