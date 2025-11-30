import { ref, computed, type Ref } from 'vue'
import type { InitData } from '../services/initService'
import type { FilterOption, HierarchySelection } from '../types'

// eslint-disable-next-line @typescript-eslint/no-explicit-any
const normalizeId = (val: any): string => {
  if (val == null || val === '') return ''
  return String(val).trim()
}

const formatIdNomeLabel = (id: string, nome: string): string => {
  if (!id && !nome) return ''
  if (!id) return nome
  if (!nome || nome === id) return id
  return `${id} - ${nome}`
}

const formatFuncionalNomeLabel = (funcional: string, nome: string): string => {
  if (!funcional && !nome) return ''
  if (!funcional) return nome
  if (!nome || nome === funcional) return funcional
  return `${funcional} - ${nome}`
}

// eslint-disable-next-line @typescript-eslint/no-explicit-any
const normalizeOption = (item: any, fieldType?: 'segmento' | 'diretoria' | 'regional' | 'agencia' | 'ggestao' | 'gerente'): FilterOption => {
  let id: string
  let funcional: string | undefined
  let idOriginal: string | undefined

  if (fieldType === 'ggestao' || fieldType === 'gerente') {
    funcional = normalizeId(item.funcional || '')
    idOriginal = normalizeId(item.id || '')
    id = funcional || idOriginal
  } else {
    id = normalizeId(
      item.id || item.codigo || item.id_diretoria || item.id_regional ||
      item.id_agencia || ''
    )
    funcional = item.funcional ? normalizeId(item.funcional) : undefined
    idOriginal = id
  }

  const nomeRaw = item.nome || item.label || ''
  const nome = String(nomeRaw).trim()

  let label = nome || id
  if (fieldType === 'ggestao' || fieldType === 'gerente') {
    label = formatFuncionalNomeLabel(funcional || id, nome)
  } else if (fieldType === 'segmento' || fieldType === 'diretoria' || fieldType === 'regional' || fieldType === 'agencia') {
    label = formatIdNomeLabel(id, nome)
  } else {
    label = item.label || formatIdNomeLabel(id, nome)
  }

  const idSegmento = item.segmento_id ?? item.id_segmento ?? item.idSegmento
  const idDiretoria = item.diretoria_id ?? item.id_diretoria ?? item.idDiretoria
  const idRegional = item.regional_id ?? item.id_regional ?? item.idRegional ?? item.gerencia_id ?? item.gerenciaId
  const idAgencia = item.agencia_id ?? item.id_agencia ?? item.idAgencia
  const idGestor = item.id_gestor ?? item.idGestor ?? item.gerente_gestao_id ?? item.gerenteGestaoId

  return {
    id,
    nome: label,
    id_segmento: idSegmento ? normalizeId(idSegmento) : undefined,
    id_diretoria: idDiretoria ? normalizeId(idDiretoria) : undefined,
    id_regional: idRegional ? normalizeId(idRegional) : undefined,
    id_agencia: idAgencia ? normalizeId(idAgencia) : undefined,
    id_gestor: idGestor ? normalizeId(idGestor) : undefined,
    funcional,
  }
}

export function useHierarchyFilters(estruturaData: Ref<InitData | null>) {
  const segmento = ref('')
  const diretoria = ref('')
  const gerencia = ref('')
  const agencia = ref('')
  const ggestao = ref('')
  const gerente = ref('')

  const allSegmentos = computed(() => {
    if (!estruturaData.value) return []
    return estruturaData.value.segmentos.map(item => normalizeOption(item, 'segmento')).filter(opt => opt.id)
  })

  const allDiretorias = computed(() => {
    if (!estruturaData.value) return []
    return estruturaData.value.diretorias.map(item => normalizeOption(item, 'diretoria')).filter(opt => opt.id)
  })

  const allRegionais = computed(() => {
    if (!estruturaData.value) return []
    return estruturaData.value.regionais.map(item => normalizeOption(item, 'regional')).filter(opt => opt.id)
  })

  const allAgencias = computed(() => {
    if (!estruturaData.value) return []
    return estruturaData.value.agencias.map(item => normalizeOption(item, 'agencia')).filter(opt => opt.id)
  })

  const allGerentesGestao = computed(() => {
    if (!estruturaData.value) return []
    return estruturaData.value.gerentes_gestao.map(item => normalizeOption(item, 'ggestao')).filter(opt => opt.id)
  })

  const allGerentes = computed(() => {
    if (!estruturaData.value) return []
    return estruturaData.value.gerentes.map(item => normalizeOption(item, 'gerente')).filter(opt => opt.id)
  })

  const segmentos = computed(() => allSegmentos.value)

  const diretorias = computed(() => {
    if (!segmento.value) return allDiretorias.value
    const segmentoMeta = findItemMeta(segmento.value, allSegmentos.value)
    if (!segmentoMeta) return []
    const segmentoIdNumero = normalizeId(segmentoMeta.id_segmento || segmentoMeta.id)
    return allDiretorias.value.filter(opt => {
      const optSegmentoId = normalizeId(opt.id_segmento || '')
      return optSegmentoId === segmentoIdNumero
    })
  })

  const regionais = computed(() => {
    if (!diretoria.value) return allRegionais.value
    const diretoriaMeta = findItemMeta(diretoria.value, allDiretorias.value)
    if (!diretoriaMeta) return []
    const diretoriaIdNumero = normalizeId(diretoriaMeta.id_diretoria || diretoriaMeta.id)
    return allRegionais.value.filter(opt => {
      const optDiretoriaId = normalizeId(opt.id_diretoria || '')
      return optDiretoriaId === diretoriaIdNumero
    })
  })

  const agencias = computed(() => {
    if (!gerencia.value) return allAgencias.value
    const gerenciaMeta = findItemMeta(gerencia.value, allRegionais.value)
    if (!gerenciaMeta) return []
    const gerenciaIdNumero = normalizeId(gerenciaMeta.id_regional || gerenciaMeta.id)
    return allAgencias.value.filter(opt => {
      const optRegionalId = normalizeId(opt.id_regional || '')
      return optRegionalId === gerenciaIdNumero
    })
  })

  const gerentesGestao = computed(() => {
    let filtered = allGerentesGestao.value

    if (agencia.value) {
      const agenciaMeta = findItemMeta(agencia.value, allAgencias.value)
      if (agenciaMeta) {
        const agenciaIdNumero = normalizeId(agenciaMeta.id_agencia || agenciaMeta.id)
        filtered = filtered.filter(opt => {
          const optAgenciaId = normalizeId(opt.id_agencia || '')
          return optAgenciaId === agenciaIdNumero
        })
      } else {
        filtered = []
      }
    }

    if (gerente.value) {
      const gerenteMeta = findItemMeta(gerente.value, allGerentes.value)
      if (gerenteMeta?.id_gestor) {
        const gestorId = normalizeId(gerenteMeta.id_gestor)
        filtered = filtered.filter(opt => {
          const optIdOriginal = normalizeId(opt.id_gestor || '')
          return optIdOriginal === gestorId
        })
      } else {
        filtered = []
      }
    }

    return filtered
  })

  const gerentes = computed(() => {
    let filtered = allGerentes.value

    if (ggestao.value) {
      const ggestaoMeta = findItemMeta(ggestao.value, allGerentesGestao.value)
      if (!ggestaoMeta) return filtered

      const ggestaoIdOriginal = normalizeId(ggestaoMeta.id_gestor || '')

      filtered = filtered.filter(opt => {
        if (!opt.id_gestor) return false
        const gestorId = normalizeId(opt.id_gestor)
        return gestorId === ggestaoIdOriginal
      })
    }

    return filtered
  })

  const findItemMeta = (id: string, items: FilterOption[]): FilterOption | null => {
    return items.find(item => normalizeId(item.id) === normalizeId(id)) || null
  }

  const adjustSelection = (changedField: keyof HierarchySelection, value: string) => {
    const normalizedValue = normalizeId(value)

    if (changedField === 'segmento') {
      diretoria.value = ''
      gerencia.value = ''
      agencia.value = ''
      ggestao.value = ''
      gerente.value = ''
    } else if (changedField === 'diretoria') {
      gerencia.value = ''
      agencia.value = ''
      ggestao.value = ''
      gerente.value = ''
      if (normalizedValue) {
        const diretoriaMeta = findItemMeta(normalizedValue, allDiretorias.value)
        if (diretoriaMeta?.id_segmento) {
          const segmentoMatch = allSegmentos.value.find(s => 
            normalizeId(s.id_segmento || s.id) === normalizeId(diretoriaMeta.id_segmento)
          )
          if (segmentoMatch) {
            segmento.value = normalizeId(segmentoMatch.id)
          }
        }
      }
    } else if (changedField === 'gerencia') {
      agencia.value = ''
      ggestao.value = ''
      gerente.value = ''
      if (normalizedValue) {
        const gerenciaMeta = findItemMeta(normalizedValue, allRegionais.value)
        if (gerenciaMeta?.id_diretoria) {
          const diretoriaMatch = allDiretorias.value.find(d => 
            normalizeId(d.id_diretoria || d.id) === normalizeId(gerenciaMeta.id_diretoria)
          )
          if (diretoriaMatch) {
            diretoria.value = normalizeId(diretoriaMatch.id)
            if (diretoriaMatch.id_segmento) {
              const segmentoMatch = allSegmentos.value.find(s => 
                normalizeId(s.id_segmento || s.id) === normalizeId(diretoriaMatch.id_segmento)
              )
              if (segmentoMatch) {
                segmento.value = normalizeId(segmentoMatch.id)
              }
            }
          }
        }
      }
    } else if (changedField === 'agencia') {
      ggestao.value = ''
      gerente.value = ''
      if (normalizedValue) {
        const agenciaMeta = findItemMeta(normalizedValue, allAgencias.value)
        if (agenciaMeta) {
          if (agenciaMeta.id_regional) {
            const gerenciaMatch = allRegionais.value.find(r => 
              normalizeId(r.id_regional || r.id) === normalizeId(agenciaMeta.id_regional)
            )
            if (gerenciaMatch) {
              gerencia.value = normalizeId(gerenciaMatch.id)
              if (gerenciaMatch.id_diretoria) {
                const diretoriaMatch = allDiretorias.value.find(d => 
                  normalizeId(d.id_diretoria || d.id) === normalizeId(gerenciaMatch.id_diretoria)
                )
                if (diretoriaMatch) {
                  diretoria.value = normalizeId(diretoriaMatch.id)
                  if (diretoriaMatch.id_segmento) {
                    const segmentoMatch = allSegmentos.value.find(s => 
                      normalizeId(s.id_segmento || s.id) === normalizeId(diretoriaMatch.id_segmento)
                    )
                    if (segmentoMatch) {
                      segmento.value = normalizeId(segmentoMatch.id)
                    }
                  }
                }
              }
            }
          }
        }
      }
    } else if (changedField === 'ggestao') {
      gerente.value = ''
      if (normalizedValue) {
        const ggestaoMeta = findItemMeta(normalizedValue, allGerentesGestao.value)
        if (ggestaoMeta?.id_agencia) {
          const agenciaMatch = allAgencias.value.find(a => 
            normalizeId(a.id_agencia || a.id) === normalizeId(ggestaoMeta.id_agencia)
          )
          if (agenciaMatch) {
            agencia.value = normalizeId(agenciaMatch.id)
            if (agenciaMatch.id_regional) {
              const gerenciaMatch = allRegionais.value.find(r => 
                normalizeId(r.id_regional || r.id) === normalizeId(agenciaMatch.id_regional)
              )
              if (gerenciaMatch) {
                gerencia.value = normalizeId(gerenciaMatch.id)
                if (gerenciaMatch.id_diretoria) {
                  const diretoriaMatch = allDiretorias.value.find(d => 
                    normalizeId(d.id_diretoria || d.id) === normalizeId(gerenciaMatch.id_diretoria)
                  )
                  if (diretoriaMatch) {
                    diretoria.value = normalizeId(diretoriaMatch.id)
                    if (diretoriaMatch.id_segmento) {
                      const segmentoMatch = allSegmentos.value.find(s => 
                        normalizeId(s.id_segmento || s.id) === normalizeId(diretoriaMatch.id_segmento)
                      )
                      if (segmentoMatch) {
                        segmento.value = normalizeId(segmentoMatch.id)
                      }
                    }
                  }
                }
              }
            }
          }
        }
      }
    } else if (changedField === 'gerente') {
      if (normalizedValue) {
        const gerenteMeta = findItemMeta(normalizedValue, allGerentes.value)
        if (gerenteMeta?.id_gestor) {
          const gestorId = normalizeId(gerenteMeta.id_gestor)
          const ggestaoMatch = allGerentesGestao.value.find(gg => {
            const ggIdOriginal = gg.id_gestor || gg.id
            const ggIdOriginalNormalized = normalizeId(ggIdOriginal)
            return ggIdOriginalNormalized === gestorId
          })
          if (ggestaoMatch) {
            ggestao.value = normalizeId(ggestaoMatch.id)
          }

          if (ggestao.value) {
            const ggestaoMeta = findItemMeta(ggestao.value, allGerentesGestao.value)
            if (ggestaoMeta?.id_agencia) {
              const agenciaMatch = allAgencias.value.find(a => 
                normalizeId(a.id_agencia || a.id) === normalizeId(ggestaoMeta.id_agencia)
              )
              if (agenciaMatch) {
                agencia.value = normalizeId(agenciaMatch.id)
                if (agenciaMatch.id_regional) {
                  const gerenciaMatch = allRegionais.value.find(r => 
                    normalizeId(r.id_regional || r.id) === normalizeId(agenciaMatch.id_regional)
                  )
                  if (gerenciaMatch) {
                    gerencia.value = normalizeId(gerenciaMatch.id)
                    if (gerenciaMatch.id_diretoria) {
                      const diretoriaMatch = allDiretorias.value.find(d => 
                        normalizeId(d.id_diretoria || d.id) === normalizeId(gerenciaMatch.id_diretoria)
                      )
                      if (diretoriaMatch) {
                        diretoria.value = normalizeId(diretoriaMatch.id)
                        if (diretoriaMatch.id_segmento) {
                          const segmentoMatch = allSegmentos.value.find(s => 
                            normalizeId(s.id_segmento || s.id) === normalizeId(diretoriaMatch.id_segmento)
                          )
                          if (segmentoMatch) {
                            segmento.value = normalizeId(segmentoMatch.id)
                          }
                        }
                      }
                    }
                  }
                }
              }
            }
          }
        }
      }
    }
  }

  const handleSegmentoChange = (value: string) => {
    segmento.value = value
    adjustSelection('segmento', value)
  }

  const handleDiretoriaChange = (value: string) => {
    diretoria.value = value
    adjustSelection('diretoria', value)
  }

  const handleGerenciaChange = (value: string) => {
    gerencia.value = value
    adjustSelection('gerencia', value)
  }

  const handleAgenciaChange = (value: string) => {
    agencia.value = value
    adjustSelection('agencia', value)
  }

  const handleGerenteGestaoChange = (value: string) => {
    ggestao.value = value
    adjustSelection('ggestao', value)
  }

  const handleGerenteChange = (value: string) => {
    gerente.value = value
    adjustSelection('gerente', value)
  }

  const clearAll = () => {
    segmento.value = ''
    diretoria.value = ''
    gerencia.value = ''
    agencia.value = ''
    ggestao.value = ''
    gerente.value = ''
  }

  return {
    segmento,
    diretoria,
    gerencia,
    agencia,
    ggestao,
    gerente,
    segmentos,
    diretorias,
    regionais,
    agencias,
    gerentesGestao,
    gerentes,
    handleSegmentoChange,
    handleDiretoriaChange,
    handleGerenciaChange,
    handleAgenciaChange,
    handleGerenteGestaoChange,
    handleGerenteChange,
    clearAll
  }
}

