import { ref, computed, type Ref } from 'vue'
import type { InitData } from '../services/initService'
import type { FilterOption, HierarchySelection } from '../types'

// Tipo que aceita InitData com arrays mutáveis ou readonly
type InitDataReadonly = {
  readonly segmentos: readonly any[]
  readonly diretorias: readonly any[]
  readonly regionais: readonly any[]
  readonly agencias: readonly any[]
  readonly gerentes_gestao: readonly any[]
  readonly gerentes: readonly any[]
  readonly familias: readonly any[]
  readonly indicadores: readonly any[]
  readonly subindicadores: readonly any[]
  readonly status_indicadores: readonly any[]
}

type InitDataCompatible = InitData | InitDataReadonly

const normalizeId = (v: any): string =>
  v == null || v === '' ? '' : String(v).trim()

const formatIdNome = (id: string, nome: string) =>
  !id && !nome ? '' : !id ? nome : !nome || nome === id ? id : `${id} - ${nome}`

const formatFuncionalNome = (funcional: string, nome: string) =>
  !funcional && !nome
    ? ''
    : !funcional
    ? nome
    : !nome || nome === funcional
    ? funcional
    : `${funcional} - ${nome}`

const normalizeOption = (item: any, type: string): FilterOption => {
  const nome = String(item.nome ?? item.label ?? '').trim()
  let id = ''
  let funcional: string | undefined

  if (type === 'ggestao' || type === 'gerente') {
    funcional = normalizeId(item.funcional ?? '')
    id = normalizeId(item.id ?? '')
  } else {
    id = normalizeId(
      item.id ??
      item.codigo ??
      item.id_diretoria ??
      item.id_regional ??
      item.id_agencia ??
      ''
    )
    funcional = item.funcional ? normalizeId(item.funcional) : undefined
  }

  return {
    id,
    nome: (type === 'ggestao' || type === 'gerente') ? formatFuncionalNome(funcional || id, nome) : formatIdNome(id, nome),
    id_segmento: normalizeId(item.segmento_id ?? item.id_segmento ?? item.idSegmento),
    id_diretoria: normalizeId(item.diretoria_id ?? item.id_diretoria ?? item.idDiretoria),
    id_regional: normalizeId(item.regional_id ?? item.id_regional ?? item.idRegional ?? item.gerencia_id ?? item.gerenciaId),
    id_agencia: normalizeId(item.agencia_id ?? item.id_agencia ?? item.idAgencia),
    id_gestor: normalizeId(item.id_gestor ?? item.idGestor ?? item.gerente_gestao_id ?? item.gerenteGestaoId),
    funcional
  }
}

export function useHierarchyFilters(estruturaData: Ref<InitDataCompatible | null> | { readonly value: InitDataCompatible | null }) {
  const state = {
    segmento: ref(''),
    diretoria: ref(''),
    gerencia: ref(''), // "gerencia" equivale a "regional" no JSON
    agencia: ref(''),
    ggestao: ref(''),
    gerente: ref('')
  }

  // Normaliza tudo uma vez
  const normalized = computed(() => {
    if (!estruturaData.value) return null
    return {
      segmentos: (estruturaData.value.segmentos ?? []).map(i => normalizeOption(i, 'segmento')),
      diretorias: (estruturaData.value.diretorias ?? []).map(i => normalizeOption(i, 'diretoria')),
      regionais: (estruturaData.value.regionais ?? []).map(i => normalizeOption(i, 'regional')),
      agencias: (estruturaData.value.agencias ?? []).map(i => normalizeOption(i, 'agencia')),
      ggestoes: (estruturaData.value.gerentes_gestao ?? []).map(i => normalizeOption(i, 'ggestao')),
      gerentes: (estruturaData.value.gerentes ?? []).map(i => normalizeOption(i, 'gerente'))
    }
  })

  // Maps O(1)
  const maps = computed(() => {
    if (!normalized.value) return null
    const idx = (arr: any[]) => Object.fromEntries((arr ?? []).map(i => [normalizeId(i.id), i]))
    return {
      segmento: idx(normalized.value.segmentos),
      diretoria: idx(normalized.value.diretorias),
      gerencia: idx(normalized.value.regionais),
      agencia: idx(normalized.value.agencias),
      ggestao: idx(normalized.value.ggestoes),
      gerente: idx(normalized.value.gerentes)
    }
  })

  // Parent lookup (qual campo no CHILD aponta para o PAI)
  const parentKeyByChild: Record<keyof HierarchySelection, string | null> = {
    segmento: null,
    diretoria: 'id_segmento', // diretoria -> segmento
    gerencia: 'id_diretoria', // regional -> diretoria
    agencia: 'id_regional',    // agencia -> regional (gerencia)
    ggestao: 'id_agencia',     // ggestao -> agencia
    gerente: 'id_gestor'       // gerente -> ggestao (id_gestor)
  }

  // Parent field name (qual é o campo pai no state)
  const parentFieldByChild: Record<keyof HierarchySelection, keyof HierarchySelection | null> = {
    segmento: null,
    diretoria: 'segmento',
    gerencia: 'diretoria', // regional -> diretoria
    agencia: 'gerencia',   // agencia -> gerencia (regional)
    ggestao: 'agencia',
    gerente: 'ggestao'
  }

  function autoFillParent(childField: keyof HierarchySelection, childValue: string) {
    if (!childValue) return
    const parentField = parentFieldByChild[childField]
    const parentKey = parentKeyByChild[childField]
    if (!parentField || !parentKey) return
    const childMeta = maps.value?.[childField]?.[normalizeId(childValue)]
    if (!childMeta) return

    // O childMeta tem, por exemplo, childMeta['id_regional'] -> id do parent
    const parentId = normalizeId((childMeta as any)[parentKey] ?? '')
    if (!parentId) return

    // parent está indexado por id
    const parentMeta = maps.value?.[parentField]?.[parentId]
    if (!parentMeta) return

    // seta o parent no state (usa o id da lista normalizada)
    state[parentField].value = parentMeta.id

    console.debug('[hierarchy] autoFillParent:', { childField, childValue, parentField, parentId })

    // sobe recursivamente
    autoFillParent(parentField, parentMeta.id)
  }

  // limpa filhos a partir do field (ordem explícita)
  const stateOrder: (keyof HierarchySelection)[] = ['segmento', 'diretoria', 'gerencia', 'agencia', 'ggestao', 'gerente']

  function clearChildrenFrom(field: keyof HierarchySelection) {
    let clearing = false
    for (const k of stateOrder) {
      if (clearing) state[k].value = ''
      if (k === field) clearing = true
    }
  }

  function onChange(field: keyof HierarchySelection, value: string) {
    state[field].value = normalizeId(value)
    clearChildrenFrom(field)
    // tenta preencher pais se possível
    autoFillParent(field, state[field].value)
  }

  // helpers para filtros
  const filterBy = (arr: FilterOption[] | undefined, key: string, value: string) =>
    (!arr) ? [] : (!value ? arr : arr.filter(i => normalizeId((i as any)[key]) === normalizeId(value)))

  const segmentos = computed(() => normalized.value?.segmentos ?? [])
  const diretorias = computed(() =>
    filterBy(normalized.value?.diretorias, 'id_segmento', state.segmento.value)
  )
  const regionais = computed(() =>
    filterBy(normalized.value?.regionais, 'id_diretoria', state.diretoria.value)
  )
  const agencias = computed(() =>
    filterBy(normalized.value?.agencias, 'id_regional', state.gerencia.value)
  )
  const gerentesGestao = computed(() =>
    filterBy(normalized.value?.ggestoes, 'id_agencia', state.agencia.value)
  )
  const gerentes = computed(() =>
    filterBy(normalized.value?.gerentes, 'id_gestor', state.ggestao.value)
  )

  const clearAll = () => {
    for (const k of stateOrder) state[k].value = ''
  }

  return {
    ...state,
    segmentos,
    diretorias,
    regionais,
    agencias,
    gerentesGestao,
    gerentes,
    handleSegmentoChange: (v: string) => onChange('segmento', v),
    handleDiretoriaChange: (v: string) => onChange('diretoria', v),
    handleGerenciaChange: (v: string) => onChange('gerencia', v),
    handleAgenciaChange: (v: string) => onChange('agencia', v),
    handleGerenteGestaoChange: (v: string) => onChange('ggestao', v),
    handleGerenteChange: (v: string) => onChange('gerente', v),
    clearAll
  }
}
