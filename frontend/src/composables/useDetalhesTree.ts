import { computed, type Ref } from 'vue'
import type { DetalhesItem } from '../api/modules/pobj.api'
import type { TreeNode } from '../components/TreeTableRow.vue'

const LEVEL_HIERARCHY: Record<string, string[]> = {
  diretoria: ['diretoria', 'regional', 'agencia', 'gerente', 'familia', 'indicador', 'subindicador', 'contrato'],
  gerencia: ['regional', 'agencia', 'gerente', 'familia', 'indicador', 'subindicador', 'contrato'],
  agencia: ['agencia', 'gerente', 'familia', 'indicador', 'subindicador', 'contrato'],
  gGestao: ['gGestao', 'gerente', 'familia', 'indicador', 'subindicador', 'contrato'],
  gerente: ['gerente', 'familia', 'indicador', 'subindicador', 'contrato'],
  secao: ['familia', 'indicador', 'subindicador', 'contrato'],
  familia: ['indicador', 'subindicador', 'contrato'],
  prodsub: ['subindicador', 'contrato'],
  contrato: ['contrato']
}

function calculateSummary(items: DetalhesItem[]) {
  const valor_realizado = items.reduce((sum, item) => sum + (item.valor_realizado || 0), 0)
  const valor_meta = items.reduce((sum, item) => sum + (item.valor_meta || item.meta_mensal || 0), 0)
  const pontos = items.reduce((sum, item) => sum + (item.peso || 0), 0)
  const peso = items.reduce((sum, item) => sum + (item.peso || 0), 0)

  const diasTotais = 30
  const diasDecorridos = new Date().getDate()
  const diasRestantes = Math.max(1, diasTotais - diasDecorridos)

  const meta_diaria = diasTotais > 0 ? (valor_meta / diasTotais) : 0
  const referencia_hoje = diasDecorridos > 0 ? Math.min(valor_meta, meta_diaria * diasDecorridos) : 0
  const meta_diaria_necessaria = diasRestantes > 0 ? Math.max(0, (valor_meta - valor_realizado) / diasRestantes) : 0
  const projecao = diasDecorridos > 0 ? (valor_realizado / Math.max(diasDecorridos, 1)) * diasTotais : valor_realizado

  const atingimento_v = valor_realizado - valor_meta
  const atingimento_p = valor_meta > 0 ? (valor_realizado / valor_meta) * 100 : 0

  const firstItem = items.length > 0 ? items[0] : null
  const data = firstItem ? (firstItem.data || firstItem.competencia || '') : ''

  return {
    valor_realizado,
    valor_meta,
    atingimento_v,
    atingimento_p,
    meta_diaria,
    referencia_hoje,
    pontos,
    meta_diaria_necessaria,
    peso,
    projecao,
    data
  }
}

function recalculateSummaryFromChildren(node: TreeNode): void {
  node.children.forEach(child => recalculateSummaryFromChildren(child))

  if (node.children.length > 0) {
    const pontosFromChildren = node.children.reduce((sum, child) => sum + (child.summary.pontos || 0), 0)
    const pesoFromChildren = node.children.reduce((sum, child) => sum + (child.summary.peso || 0), 0)
    node.summary.pontos = pontosFromChildren
    node.summary.peso = pesoFromChildren
  }
}

function getLabelForLevel(level: string, item: DetalhesItem): string {
  switch (level) {
    case 'diretoria':
      return item.diretoria_nome || 'Sem diretoria'
    case 'regional':
      return item.gerencia_nome || 'Sem regional'
    case 'agencia':
      return item.agencia_nome || 'Sem agência'
    case 'gGestao':
      return item.gerente_gestao_nome || 'Sem gerente de gestão'
    case 'gerente':
      return item.gerente_nome || 'Sem gerente'
    case 'familia':
      return item.familia_nome || 'Sem família'
    case 'indicador':
      return item.ds_indicador || 'Sem indicador'
    case 'subindicador':
      return item.subindicador || 'Sem subindicador'
    case 'contrato':
      return item.id_contrato || item.registro_id || 'Sem contrato'
    default:
      return 'Sem label'
  }
}

function getKeyForLevel(level: string, item: DetalhesItem): string {
  switch (level) {
    case 'diretoria':
      return item.diretoria_id || 'sem-diretoria'
    case 'regional':
      return item.gerencia_id || 'sem-regional'
    case 'agencia':
      return item.agencia_id || 'sem-agencia'
    case 'gGestao':
      return item.gerente_gestao_id || 'sem-gerente-gestao'
    case 'gerente':
      return item.gerente_id || 'sem-gerente'
    case 'familia':
      return item.familia_id || 'sem-familia'
    case 'indicador':
      return item.id_indicador || 'sem-indicador'
    case 'subindicador':
      return item.id_subindicador || 'sem-subindicador'
    case 'contrato':
      return item.id_contrato || item.registro_id || 'sem-contrato'
    default:
      return 'unknown'
  }
}

function buildTreeHierarchy(items: DetalhesItem[], hierarchy: string[], level: number): TreeNode[] {
  if (level >= hierarchy.length || items.length === 0) return []

  const currentLevel = hierarchy[level]
  const nextLevel = hierarchy[level + 1]
  const groups = new Map<string, DetalhesItem[]>()

  items.forEach(item => {
    const key = getKeyForLevel(currentLevel, item)
    if (!groups.has(key)) {
      groups.set(key, [])
    }
    groups.get(key)!.push(item)
  })

  const nodes: TreeNode[] = []

  groups.forEach((groupItems, key) => {
    const firstItem = groupItems[0]
    if (!firstItem) return

    const label = getLabelForLevel(currentLevel, firstItem)
    const node: TreeNode = {
      id: `${currentLevel}-${key}`,
      label,
      level: currentLevel as never,
      children: nextLevel ? buildTreeHierarchy(groupItems, hierarchy, level + 1) : [],
      data: groupItems,
      summary: calculateSummary(groupItems)
    }

    if (currentLevel === 'contrato') {
      node.detail = {
        canal_venda: firstItem.canal_venda || undefined,
        tipo_venda: firstItem.tipo_venda || undefined,
        gerente: firstItem.gerente_nome || undefined,
        modalidade_pagamento: firstItem.modalidade_pagamento || undefined,
        dt_vencimento: firstItem.dt_vencimento || undefined,
        dt_cancelamento: firstItem.dt_cancelamento || undefined,
        motivo_cancelamento: firstItem.motivo_cancelamento || undefined
      }
    }

    nodes.push(node)
  })

  nodes.forEach(node => recalculateSummaryFromChildren(node))

  return nodes
}

export function useDetalhesTree(
  detalhesData: Ref<DetalhesItem[]>,
  tableView: Ref<string>,
  searchTerm: Ref<string>
) {
  const treeData = computed(() => {
    if (!detalhesData.value.length || searchTerm.value.trim()) return []

    const hierarchy: string[] = (LEVEL_HIERARCHY[tableView.value] as string[]) || LEVEL_HIERARCHY.diretoria

    if (tableView.value === 'contrato') {
      const contratos = new Map<string, DetalhesItem[]>()
      detalhesData.value.forEach(item => {
        const key = item.id_contrato || item.registro_id || 'sem-contrato'
        if (!contratos.has(key)) {
          contratos.set(key, [])
        }
        contratos.get(key)!.push(item)
      })

      const result: TreeNode[] = []
      contratos.forEach((items, contratoId) => {
        const firstItem = items[0]
        if (!firstItem) return
        result.push({
          id: `contrato-${contratoId}`,
          label: contratoId,
          level: 'contrato' as const,
          children: [],
          data: items,
          summary: calculateSummary(items),
          detail: {
            canal_venda: firstItem.canal_venda,
            tipo_venda: firstItem.tipo_venda,
            gerente: firstItem.gerente_nome,
            gerente_gestao: firstItem.gerente_gestao_nome,
            modalidade_pagamento: firstItem.modalidade_pagamento,
            dt_vencimento: firstItem.dt_vencimento,
            dt_cancelamento: firstItem.dt_cancelamento,
            motivo_cancelamento: firstItem.motivo_cancelamento
          }
        })
      })
      return result
    }

    const hierarchyArray: string[] = hierarchy || LEVEL_HIERARCHY.diretoria
    return buildTreeHierarchy(detalhesData.value, hierarchyArray, 0)
  })

  return { treeData, calculateSummary }
}

export { calculateSummary }
