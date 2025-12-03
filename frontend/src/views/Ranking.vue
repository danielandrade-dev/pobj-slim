<script setup lang="ts">
import { ref, computed, onMounted, watch } from 'vue'
import { getRanking, type RankingItem, type RankingFilters } from '../services/rankingService'
import { useGlobalFilters } from '../composables/useGlobalFilters'
import { formatINT } from '../utils/formatUtils'
import SelectInput from '../components/SelectInput.vue'
import type { FilterOption } from '../types'

const { filterState, period } = useGlobalFilters()

const rankingData = ref<RankingItem[]>([])
const loading = ref(false)
const error = ref<string | null>(null)

const rankingFilters = computed<RankingFilters>(() => {
  const filters: RankingFilters = {}
  
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

const loadRanking = async () => {
  loading.value = true
  error.value = null

  try {
    const data = await getRanking(rankingFilters.value)
    if (data) {
      rankingData.value = data
    } else {
      error.value = 'Não foi possível carregar os dados de ranking'
      rankingData.value = []
    }
  } catch (err) {
    error.value = err instanceof Error ? err.message : 'Erro ao carregar ranking'
    rankingData.value = []
  } finally {
    loading.value = false
  }
}

onMounted(() => {
  loadRanking()
})

watch([filterState], () => {
  loadRanking()
}, { deep: true })

const RANKING_KEY_FIELDS: Record<string, string> = {
  segmento: 'segmento_id',
  diretoria: 'diretoria_id',
  gerencia: 'gerencia_id',
  agencia: 'agencia_id',
  gerenteGestao: 'gerente_gestao_id',
  gerente: 'gerente_id'
}

const RANKING_LABEL_FIELDS: Record<string, string> = {
  segmento: 'segmento',
  diretoria: 'diretoria_nome',
  gerencia: 'gerencia_nome',
  agencia: 'agencia_nome',
  gerenteGestao: 'gerente_gestao_nome',
  gerente: 'gerente_nome'
}

const isDefaultSelection = (val: string | null | undefined): boolean => {
  if (!val) return true
  const normalized = val.toLowerCase().trim()
  return normalized === 'todos' || normalized === 'todas' || normalized === ''
}

const getRankingSelectionForLevel = (level: string): string | null => {
  const fs = filterState.value
  switch (level) {
    case 'segmento':
      return fs.segmento || null
    case 'diretoria':
      return fs.diretoria || null
    case 'gerencia':
      return fs.gerencia || null
    case 'agencia':
      return fs.agencia || null
    case 'gerenteGestao':
      return fs.ggestao || null
    case 'gerente':
      return fs.gerente || null
    default:
      return null
  }
}

const levelNames: Record<string, string> = {
  segmento: 'Segmento',
  diretoria: 'Diretoria',
  gerencia: 'Regional',
  agencia: 'Agência',
  gerente: 'Gerente',
  gerenteGestao: 'Gerente de gestão'
}

// Nível selecionado manualmente pelo usuário
const selectedLevel = ref<string>('gerenteGestao')

// Opções de nível para o select
const levelOptions = computed<FilterOption[]>(() => [
  { id: 'segmento', nome: 'Segmento' },
  { id: 'diretoria', nome: 'Diretoria' },
  { id: 'gerencia', nome: 'Regional' },
  { id: 'agencia', nome: 'Agência' },
  { id: 'gerenteGestao', nome: 'Gerente de gestão' },
  { id: 'gerente', nome: 'Gerente' }
])

// Usa o nível selecionado manualmente pelo usuário
const rankingLevel = computed(() => {
  return selectedLevel.value || 'gerenteGestao'
})

const handleLevelChange = (value: string): void => {
  selectedLevel.value = value
}

const simplificarTexto = (text: string): string => {
  return text.toLowerCase()
    .normalize('NFD')
    .replace(/[\u0300-\u036f]/g, '')
    .replace(/\s+/g, '')
    .trim()
}

const matchesSelection = (filterValue: string, ...candidates: (string | null | undefined)[]): boolean => {
  if (isDefaultSelection(filterValue)) return true
  const normalizedFilter = filterValue.toLowerCase().trim()
  return candidates.some(candidate => {
    if (!candidate) return false
    return candidate.toString().toLowerCase().trim() === normalizedFilter ||
           simplificarTexto(candidate.toString()) === simplificarTexto(filterValue)
  })
}

const groupedRanking = computed(() => {
  const level = rankingLevel.value
  const keyField = RANKING_KEY_FIELDS[level] || 'gerente_gestao_id'
  const labelField = RANKING_LABEL_FIELDS[level] || 'gerente_gestao_nome'

  const selectionForLevel = getRankingSelectionForLevel(level)
  const hasSelectionForLevel = !!selectionForLevel && !isDefaultSelection(selectionForLevel)

  // Quando há um filtro aplicado, mostra ranking do mesmo nível (mesmo grupo)
  // Se não há filtro, mostra ranking do nível padrão
  let targetLevel = level
  let targetKeyField = keyField
  let targetLabelField = labelField

  // Se há seleção no nível atual, mantém o ranking no mesmo nível para mostrar o grupo
  // Não avança para o próximo nível
  if (hasSelectionForLevel) {
    // Mantém o mesmo nível para mostrar todos do grupo
    targetLevel = level
    targetKeyField = keyField
    targetLabelField = labelField
  }

  // Filtra os dados para pegar apenas os itens do mesmo grupo
  let filteredData = rankingData.value
  if (hasSelectionForLevel && selectionForLevel) {
    // Encontra o item selecionado para identificar o grupo
    const selectedItem = rankingData.value.find(item => {
      const itemKey = (item as unknown as Record<string, string>)[keyField]
      const itemLabel = (item as any)[labelField]
      return matchesSelection(selectionForLevel, itemKey, itemLabel)
    })

    if (selectedItem) {
      // Identifica o grupo baseado no nível pai
      const parentLevelHierarchy: Record<string, string> = {
        segmento: null as any,
        diretoria: 'segmento_id',
        gerencia: 'diretoria_id',
        agencia: 'gerencia_id',
        gerenteGestao: 'agencia_id',
        gerente: 'gerente_gestao_id'
      }

      const parentKeyField = parentLevelHierarchy[level]
      if (parentKeyField) {
        const parentKey = (selectedItem as any)[parentKeyField]
        // Filtra todos os itens que pertencem ao mesmo grupo (mesmo pai)
        filteredData = rankingData.value.filter(item => {
          const itemParentKey = (item as any)[parentKeyField]
          return itemParentKey === parentKey
        })
      } else {
        // Se não há nível pai (segmento), mostra todos
        filteredData = rankingData.value
      }
    }
  }

  // Agrupa por nível alvo
  const groups = new Map<string, {
    unidade: string
    label: string
    pontos: number
    count: number
  }>()

  filteredData.forEach(item => {
    const key = (item as any)[targetKeyField] || 'unknown'
    const label = (item as any)[targetLabelField] || key || '—'

    if (!groups.has(key)) {
      groups.set(key, {
        unidade: key,
        label,
        pontos: 0,
        count: 0
      })
    }

    const group = groups.get(key)!
    // Soma o realizado da f-pontos (usando pontos ou realizado_mensal como fallback)
    const pontos = item.pontos || item.realizado_mensal || 0
    group.pontos += pontos
    group.count += 1
  })

  return Array.from(groups.values())
    .sort((a, b) => b.pontos - a.pontos)
})

const formatPoints = (value: number | null | undefined): string => {
  if (value == null || isNaN(value)) return '—'
  return new Intl.NumberFormat('pt-BR', {
    minimumFractionDigits: 0,
    maximumFractionDigits: 0
  }).format(value)
}

const shouldMaskName = (item: any, index: number): boolean => {
  const level = rankingLevel.value
  const selectionForLevel = getRankingSelectionForLevel(level)
  const hasSelectionForLevel = !!selectionForLevel && !isDefaultSelection(selectionForLevel)

  // Se não há filtro aplicado, mascara todos exceto o primeiro
  if (!hasSelectionForLevel) {
    return index !== 0
  }

  // Se há filtro aplicado, mostra apenas o item que corresponde ao filtro
  if (hasSelectionForLevel && selectionForLevel) {
    const matches = matchesSelection(selectionForLevel, item.unidade, item.label)
    return !matches // Mascara se não corresponder ao filtro
  }

  return true
}
</script>

<template>
  <div class="ranking-wrapper">
    <div class="ranking-view">
        <template v-if="loading">
          <div class="ranking-content">
            <div class="card card--ranking">
              <header class="card__header rk-head">
                <div class="skeleton skeleton--title" style="height: 24px; width: 150px; margin-bottom: 8px;"></div>
                <div class="skeleton skeleton--subtitle" style="height: 16px; width: 300px;"></div>
              </header>
              <div class="rk-summary">
                <div class="rk-badges">
                  <div class="skeleton skeleton--badge" style="height: 32px; width: 120px; border-radius: 6px;"></div>
                  <div class="skeleton skeleton--badge" style="height: 32px; width: 140px; border-radius: 6px;"></div>
                  <div class="skeleton skeleton--badge" style="height: 32px; width: 180px; border-radius: 6px;"></div>
                </div>
              </div>
              <div class="ranking-table-wrapper">
                <table class="rk-table">
                  <thead>
                    <tr>
                      <th class="pos-col">#</th>
                      <th class="unit-col">Unidade</th>
                      <th class="points-col">Pontos</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr v-for="i in 10" :key="i" class="rk-row">
                      <td class="pos-col"><div class="skeleton skeleton--text" style="height: 16px; width: 20px; margin: 0 auto;"></div></td>
                      <td class="unit-col"><div class="skeleton skeleton--text" style="height: 16px; width: 80%;"></div></td>
                      <td class="points-col"><div class="skeleton skeleton--text" style="height: 16px; width: 60px; margin: 0 auto;"></div></td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>
        </div>
        </template>

        <!-- Conteúdo real -->
        <template v-else>
          <div v-if="error" class="error-state">
          <p>{{ error }}</p>
        </div>

        <div v-else-if="rankingData.length === 0" class="empty-state">
          <p v-if="filterState.ggestao">
            Sem dados de ranking disponíveis para o gerente de gestão selecionado.
          </p>
          <p v-else>
            Selecione um gerente de gestão nos filtros para visualizar o ranking.
          </p>
        </div>

        <div v-else class="ranking-content">
          <div class="card card--ranking">
            <header class="card__header rk-head">
              <div class="title-subtitle">
                <h3>Rankings</h3>
                <p class="muted">Compare diferentes visões respeitando os filtros aplicados.</p>
              </div>
            </header>

            <div class="rk-summary" id="rk-summary">
              <div class="rk-badges">
                <div class="rk-badge rk-badge--level">
                  <strong>Nível:</strong>
                  <SelectInput
                    id="rk-level-select"
                    :model-value="selectedLevel"
                    :options="levelOptions"
                    placeholder="Selecione o nível"
                    label="Nível de agrupamento"
                    @update:model-value="handleLevelChange"
                  />
                </div>
                <span class="rk-badge">
                  <strong>Quantidade de participantes:</strong> {{ formatINT(groupedRanking.length) }}
                </span>
              </div>
            </div>

            <div class="ranking-table-wrapper" id="rk-table">
              <table class="rk-table">
                <thead>
                  <tr>
                    <th class="pos-col">#</th>
                    <th class="unit-col">Unidade</th>
                    <th class="points-col">Pontos</th>
                  </tr>
                </thead>
                <tbody>
                  <tr
                    v-for="(item, index) in groupedRanking"
                    :key="`${item.unidade}-${index}`"
                    class="rk-row"
                    :class="{ 'rk-row--top': index === 0 }"
                  >
                    <td class="pos-col">{{ formatINT(index + 1) }}</td>
                    <td class="unit-col rk-name">
                      {{ shouldMaskName(item, index) ? '*****' : item.label }}
                    </td>
                    <td class="points-col">{{ formatPoints(item.pontos) }}</td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>
        </template>
      </div>
    </div>
</template>

<style scoped>
.ranking-wrapper {
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
  --text-muted: var(--brad-color-gray-dark, #999);

  min-height: 100vh;
  width: 100%;
  padding: 20px 0;
  color: var(--text);
  font-family: var(--brad-font-family);
  -webkit-font-smoothing: antialiased;
  -moz-osx-font-smoothing: grayscale;
  box-sizing: border-box;
}


.ranking-view {
  width: 100%;
  margin-top: 24px;
}

.loading-state,
.error-state,
.empty-state {
  padding: 48px 24px;
  text-align: center;
  color: var(--muted);
  background: var(--panel);
  border: 1px solid var(--stroke);
  border-radius: var(--radius);
  box-shadow: var(--shadow);
}

.error-state {
  color: var(--brand);
}

.ranking-content {
  display: flex;
  flex-direction: column;
  gap: 18px;
}

.card {
  background: var(--panel);
  border: 1px solid var(--stroke);
  border-radius: var(--radius);
  box-shadow: var(--shadow);
  padding: 16px;
  margin-bottom: 12px;
  padding-top: 12px;
  box-sizing: border-box;
  transition: all 0.3s cubic-bezier(0.25, 0.1, 0.25, 1);
}

.card--ranking {
  padding: 0;
}

.card__header {
  padding: 16px;
  border-bottom: 1px solid var(--stroke);
  display: flex;
  justify-content: space-between;
  align-items: flex-start;
  gap: 24px;
}

.rk-head {
  flex-wrap: wrap;
}

.title-subtitle h3 {
  margin: 0 0 4px 0;
  font-size: 20px;
  font-weight: var(--brad-font-weight-bold, 700);
  color: var(--text);
  line-height: 1.2;
}

.title-with-icon {
  display: flex;
  align-items: center;
  gap: 10px;
  margin-bottom: 4px;
}

.title-with-icon h3 {
  margin: 0;
}

.title-subtitle .muted {
  margin: 0;
  font-size: 14px;
  color: var(--muted);
  line-height: 1.4;
}

.rk-head__controls {
  display: flex;
  gap: 16px;
  align-items: flex-end;
}

.rk-control {
  display: flex;
  flex-direction: column;
  gap: 4px;
}

.rk-control label {
  font-size: 11px;
  text-transform: uppercase;
  letter-spacing: 0.5px;
  color: var(--muted);
  font-weight: var(--brad-font-weight-semibold, 600);
}

.input {
  padding: 8px 12px;
  border: 1px solid var(--stroke);
  border-radius: 8px;
  background: var(--panel);
  color: var(--text);
  font-size: 14px;
  font-family: inherit;
  box-sizing: border-box;
}

.input--sm {
  padding: 6px 10px;
  font-size: 13px;
}

.rk-summary {
  padding: 16px;
  border-bottom: 1px solid var(--stroke);
}

.rk-badges {
  display: flex;
  flex-wrap: wrap;
  gap: 12px;
}

.rk-badge {
  display: inline-flex;
  align-items: center;
  gap: 8px;
  padding: 8px 14px;
  background: var(--brad-color-primary-xlight, rgba(252, 231, 236, 0.8));
  border-radius: 8px;
  font-size: 13px;
  color: var(--brad-color-primary, #cc092f);
  box-sizing: border-box;
}

.rk-badge--primary {
  background: var(--brad-color-primary, #cc092f);
  color: var(--brad-color-on-bg-primary, #fff);
}

.rk-badge--level {
  display: inline-flex;
  align-items: center;
  gap: 8px;
  padding: 8px 14px;
  background: var(--brad-color-primary-xlight, rgba(252, 231, 236, 0.8));
  border-radius: 8px;
  font-size: 13px;
  color: var(--brad-color-primary, #cc092f);
  box-sizing: border-box;
}

.rk-badge--level strong {
  margin-right: 0;
  font-weight: var(--brad-font-weight-semibold, 600);
  white-space: nowrap;
}

.rk-badge--level :deep(.select) {
  min-width: 180px;
}

.rk-badge--level :deep(.select__trigger) {
  padding: 6px 10px;
  font-size: 13px;
  border: 1px solid var(--brad-color-primary, #cc092f);
  background: var(--panel, #fff);
  color: var(--text, #000);
}

.rk-badge strong {
  margin-right: 6px;
  font-weight: var(--brad-font-weight-semibold, 600);
}

.ranking-table-wrapper {
  overflow-x: auto;
  padding: 20px 24px;
  box-sizing: border-box;
}

.rk-table {
  width: 100%;
  border-collapse: separate;
  border-spacing: 0;
  font-size: var(--brad-font-size-base, 16px);
  table-layout: fixed;
}

.rk-table thead {
  background: var(--brad-color-gray-light, #ebebeb);
  border-bottom: 2px solid var(--stroke);
  position: sticky;
  top: 0;
  z-index: 1;
}

.rk-table th {
  padding: 14px 20px;
  text-align: left;
  font-weight: var(--brad-font-weight-semibold, 600);
  color: var(--text);
  font-size: 12px;
  text-transform: uppercase;
  letter-spacing: 0.5px;
  white-space: nowrap;
}

.rk-table td {
  padding: 16px 20px;
  border-bottom: 1px solid var(--stroke);
  color: var(--text);
  vertical-align: middle;
}

.rk-table tbody tr {
  transition: background-color 0.2s ease;
}

.rk-table tbody tr:hover {
  background: var(--brad-color-primary-xlight, rgba(252, 231, 236, 0.15));
}

.rk-table tbody tr:last-child td {
  border-bottom: none;
}

.rk-table tbody tr.rk-row--top {
  background: var(--brad-color-primary-xlight, rgba(252, 231, 236, 0.3));
}

.rk-table tbody tr.rk-row--top td {
  color: var(--brad-color-primary, #cc092f);
  font-weight: var(--brad-font-weight-semibold, 600);
}

.pos-col {
  width: 80px;
  min-width: 80px;
  max-width: 80px;
  text-align: center;
  font-weight: var(--brad-font-weight-semibold, 600);
  color: var(--text);
  font-size: 15px;
  position: relative;
}

.pos-medal {
  display: inline-flex;
  align-items: center;
  justify-content: center;
}

.pos-medal--gold :deep(svg) {
  color: #ffd700;
}

.pos-medal--silver :deep(svg) {
  color: #c0c0c0;
}

.pos-medal--bronze :deep(svg) {
  color: #cd7f32;
}

.unit-col {
  width: auto;
  min-width: 300px;
  font-weight: var(--brad-font-weight-medium, 500);
  color: var(--text);
  font-size: 15px;
  word-wrap: break-word;
  overflow-wrap: break-word;
}

.rk-name {
  color: var(--text);
}

.date-col {
  color: var(--text-muted);
  font-size: 13px;
}

.rank-col {
  text-align: center;
  font-weight: var(--brad-font-weight-semibold, 600);
  color: var(--brad-color-primary, #cc092f);
}

.points-col {
  width: 140px;
  min-width: 140px;
  max-width: 140px;
  text-align: right;
  font-weight: var(--brad-font-weight-semibold, 600);
  color: var(--text);
  font-variant-numeric: tabular-nums;
  font-size: 15px;
  padding-right: 24px;
}


@media (max-width: 768px) {
  .rk-badges {
    flex-direction: column;
  }

  .ranking-table-wrapper {
    padding: 12px 16px;
  }

  .rk-table {
    font-size: 14px;
    min-width: 500px;
  }

  .rk-table th {
    padding: 12px 16px;
    font-size: 11px;
  }

  .rk-table td {
    padding: 14px 16px;
    font-size: 14px;
  }

  .pos-col {
    width: 60px;
    min-width: 60px;
    max-width: 60px;
    font-size: 14px;
  }

  .unit-col {
    min-width: 200px;
    font-size: 14px;
  }

  .points-col {
    width: 120px;
    min-width: 120px;
    max-width: 120px;
    font-size: 14px;
    padding-right: 16px;
  }
}
</style>

