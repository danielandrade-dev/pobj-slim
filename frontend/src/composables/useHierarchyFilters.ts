import { ref, computed, type Ref } from 'vue'
import type { InitData } from '../api/modules/pobj.api'
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

function normalizeId(value: unknown): string {
  return value == null || value === '' ? '' : String(value).trim()
}

function formatIdNome(id: string, nome: string): string {
  if (!id && !nome) return ''
  if (!id) return nome
  if (!nome || nome === id) return id
  return `${id} - ${nome}`
}

function formatFuncionalNome(funcional: string, nome: string): string {
  if (!funcional && !nome) return ''
  if (!funcional) return nome
  if (!nome || nome === funcional) return funcional
  return `${funcional} - ${nome}`
}

function getFieldValue(item: Record<string, unknown>, ...keys: string[]): unknown {
  for (const key of keys) {
    if (item[key] !== undefined && item[key] !== null) {
      return item[key]
    }
  }
  return undefined
}

function normalizeOption(item: Record<string, unknown>, type: string): FilterOption {
  const nome = String(item.nome ?? item.label ?? '').trim()
  const isGestorType = type === 'ggestao' || type === 'gerente'
  
  let id = ''
  let funcional: string | undefined

  if (isGestorType) {
    funcional = normalizeId(item.funcional ?? '')
    id = normalizeId(item.id ?? '')
  } else {
    id = normalizeId(
      getFieldValue(item, 'id', 'codigo', 'id_diretoria', 'id_regional', 'id_agencia') ?? ''
    )
    funcional = item.funcional ? normalizeId(item.funcional) : undefined
  }

  const displayNome = isGestorType
    ? formatFuncionalNome(funcional || id, nome)
    : formatIdNome(id, nome)

  return {
    id,
    nome: displayNome,
    id_segmento: normalizeId(getFieldValue(item, 'segmento_id', 'id_segmento', 'idSegmento')),
    id_diretoria: normalizeId(getFieldValue(item, 'diretoria_id', 'id_diretoria', 'idDiretoria')),
    id_regional: normalizeId(getFieldValue(item, 'regional_id', 'id_regional', 'idRegional', 'gerencia_id', 'gerenciaId')),
    id_agencia: normalizeId(getFieldValue(item, 'agencia_id', 'id_agencia', 'idAgencia')),
    id_gestor: normalizeId(getFieldValue(item, 'id_gestor', 'idGestor', 'gerente_gestao_id', 'gerenteGestaoId')),
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

  const maps = computed(() => {
    if (!normalized.value) return null
    
    function createIndex(arr: FilterOption[]): Record<string, FilterOption> {
      return Object.fromEntries(arr.map(item => [normalizeId(item.id), item]))
    }
    
    return {
      segmento: createIndex(normalized.value.segmentos),
      diretoria: createIndex(normalized.value.diretorias),
      gerencia: createIndex(normalized.value.regionais),
      agencia: createIndex(normalized.value.agencias),
      ggestao: createIndex(normalized.value.ggestoes),
      gerente: createIndex(normalized.value.gerentes)
    }
  })

  const parentKeyByChild: Record<keyof HierarchySelection, string | null> = {
    segmento: null,
    diretoria: 'id_segmento',
    gerencia: 'id_diretoria',
    agencia: 'id_regional',
    ggestao: 'id_agencia',
    gerente: 'id_gestor'
  }

  const parentFieldByChild: Record<keyof HierarchySelection, keyof HierarchySelection | null> = {
    segmento: null,
    diretoria: 'segmento',
    gerencia: 'diretoria',
    agencia: 'gerencia',
    ggestao: 'agencia',
    gerente: 'ggestao'
  }

  function autoFillParent(childField: keyof HierarchySelection, childValue: string): void {
    if (!childValue) return
    
    const parentField = parentFieldByChild[childField]
    const parentKey = parentKeyByChild[childField]
    if (!parentField || !parentKey) return
    
    const childMeta = maps.value?.[childField]?.[normalizeId(childValue)]
    if (!childMeta) return

    const parentId = normalizeId((childMeta as Record<string, unknown>)[parentKey] ?? '')
    if (!parentId) return

    const parentMeta = maps.value?.[parentField]?.[parentId]
    if (!parentMeta) return

    state[parentField].value = parentMeta.id
    autoFillParent(parentField, parentMeta.id)
  }

  const stateOrder: (keyof HierarchySelection)[] = ['segmento', 'diretoria', 'gerencia', 'agencia', 'ggestao', 'gerente']

  function clearChildrenFrom(field: keyof HierarchySelection): void {
    let clearing = false
    for (const k of stateOrder) {
      if (clearing) state[k].value = ''
      if (k === field) clearing = true
    }
  }

  function onChange(field: keyof HierarchySelection, value: string): void {
    state[field].value = normalizeId(value)
    clearChildrenFrom(field)
    autoFillParent(field, state[field].value)
  }

  const segmentos = computed(() => normalized.value?.segmentos ?? [])
  const diretorias = computed(() => normalized.value?.diretorias ?? [])
  const regionais = computed(() => normalized.value?.regionais ?? [])
  const agencias = computed(() => normalized.value?.agencias ?? [])
  const gerentesGestao = computed(() => normalized.value?.ggestoes ?? [])
  const gerentes = computed(() => normalized.value?.gerentes ?? [])

  function clearAll(): void {
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
