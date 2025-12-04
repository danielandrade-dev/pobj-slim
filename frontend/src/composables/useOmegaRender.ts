export function useOmegaRender(
  omega: any,
  filters: any,
  bulk: any
) {
  function renderUsers(root: HTMLElement) {
    const userSelect = root.querySelector('#omega-user-select') as HTMLSelectElement
    if (!userSelect) return

    const currentUsers = omega.users.value
    // Limpa opções existentes
    while (userSelect.firstChild) {
      userSelect.removeChild(userSelect.firstChild)
    }

    currentUsers.forEach((user: any) => {
      const option = document.createElement('option')
      option.value = user.id
      option.textContent = user.name
      if (user.id === omega.currentUserId.value) {
        option.selected = true
      }
      userSelect.appendChild(option)
    })

    // Usa onchange em vez de addEventListener
    userSelect.onchange = (e) => {
      const target = e.target as HTMLSelectElement
      omega.setCurrentUserId(target.value)
      renderProfile(root)
      renderTickets(root)
    }
  }

  function renderProfile(root: HTMLElement) {
    const currentUser = omega.currentUser.value
    if (!currentUser) return

    const avatarImg = root.querySelector('#omega-avatar') as HTMLImageElement
    const userName = root.querySelector('#omega-user-name')

    if (avatarImg) {
      if (currentUser.avatar) {
        avatarImg.src = currentUser.avatar
        avatarImg.removeAttribute('hidden')
      } else {
        avatarImg.hidden = true
      }
    }

    if (userName) {
      userName.textContent = currentUser.name
    }
  }

  function renderNavigation(root: HTMLElement) {
    const navElement = root.querySelector('#omega-nav')
    if (!navElement) return

    const currentUser = omega.currentUser.value
    if (!currentUser) return

    const navItems = omega.getNavItemsForRole(currentUser.role)

    // Limpa navegação existente
    while (navElement.firstChild) {
      navElement.removeChild(navElement.firstChild)
    }

    navItems.forEach((item: any) => {
      const navItem = document.createElement('button')
      navItem.className = 'omega-nav__item'
      navItem.type = 'button'
      navItem.setAttribute('data-omega-view', item.id)
      
      // Cria elementos em vez de innerHTML
      const icon = document.createElement('i')
      icon.className = item.icon
      const span = document.createElement('span')
      span.textContent = item.label
      navItem.appendChild(icon)
      navItem.appendChild(span)

      if (item.id === omega.currentView.value) {
        navItem.classList.add('omega-nav__item--active')
      }

      // Usa onclick em vez de addEventListener
      navItem.onclick = () => {
        omega.setCurrentView(item.id as any)
        renderTickets(root)
        // Atualiza estado ativo
        navElement.querySelectorAll('.omega-nav__item').forEach((el) => {
          el.classList.remove('omega-nav__item--active')
        })
        navItem.classList.add('omega-nav__item--active')
      }

      navElement.appendChild(navItem)
    })
  }

  function renderStructure(root: HTMLElement) {
    const structure = omega.structure.value
    if (!structure || structure.length === 0) return

    // Popula select de departamento (apenas o do filtro)
    const departmentSelect = root.querySelector('#omega-filter-department') as HTMLSelectElement
    if (departmentSelect) {
      const currentValue = departmentSelect.value

      // Remove listeners antigos para evitar duplicação
      const newSelect = departmentSelect.cloneNode(true) as HTMLSelectElement
      departmentSelect.parentNode?.replaceChild(newSelect, departmentSelect)

      // Agrupa por departamento (usando Map para evitar duplicatas)
      const departments = new Map<string, string>()
      structure.forEach((item: any) => {
        if (item.departamento && !departments.has(item.departamento)) {
          departments.set(item.departamento, item.departamento_id || item.departamento)
        }
      })

      // Limpa e adiciona opção padrão
      while (newSelect.firstChild) {
        newSelect.removeChild(newSelect.firstChild)
      }
      const defaultOption = document.createElement('option')
      defaultOption.value = ''
      defaultOption.textContent = 'Selecione...'
      newSelect.appendChild(defaultOption)
      
      departments.forEach((id, name) => {
        const option = document.createElement('option')
        option.value = id
        option.textContent = name
        if (id === currentValue) {
          option.selected = true
        }
        newSelect.appendChild(option)
      })

      // Usa onchange em vez de addEventListener
      newSelect.onchange = () => {
        renderTypeSelect(root, newSelect.value)
      }
    }

    // Popula select de tipo de chamado
    const deptSelect = root.querySelector('#omega-filter-department') as HTMLSelectElement
    renderTypeSelect(root, deptSelect?.value || '')

    // Renderiza status como checkboxes
    renderStatusOptions(root)

    // Popula select de prioridade
    renderPrioritySelect(root)
  }

  function renderTypeSelect(root: HTMLElement, departmentId: string = '') {
    const structure = omega.structure.value
    if (!structure || structure.length === 0) return

    const typeSelect = root.querySelector('#omega-filter-type') as HTMLSelectElement
    if (!typeSelect) return

    const currentValue = typeSelect.value

    // Filtra tipos baseado no departamento selecionado
    const types = new Set<string>()
    structure.forEach((item: any) => {
      if (item.tipo) {
        // Se há departamento selecionado, filtra por ele
        if (departmentId) {
          const itemDeptId = item.departamento_id || item.departamento
          if (itemDeptId === departmentId) {
            types.add(item.tipo)
          }
        } else {
          // Se não há departamento selecionado, mostra todos os tipos
          types.add(item.tipo)
        }
      }
    })

    // Limpa e adiciona opção padrão
    while (typeSelect.firstChild) {
      typeSelect.removeChild(typeSelect.firstChild)
    }
    const defaultOption = document.createElement('option')
    defaultOption.value = ''
    defaultOption.textContent = 'Todos os tipos'
    typeSelect.appendChild(defaultOption)
    
    Array.from(types).sort().forEach((tipo) => {
      const option = document.createElement('option')
      option.value = tipo
      option.textContent = tipo
      if (tipo === currentValue) {
        option.selected = true
      }
      typeSelect.appendChild(option)
    })
  }

  function renderStatusOptions(root: HTMLElement) {
    const statuses = omega.statuses.value
    if (!statuses || statuses.length === 0) return

    const statusHost = root.querySelector('#omega-filter-status')
    if (!statusHost) return

    const selectedStatuses = filters.filters.value.statuses || []

    // Limpa opções existentes
    while (statusHost.firstChild) {
      statusHost.removeChild(statusHost.firstChild)
    }
    
    statuses.forEach((status: any) => {
      const option = document.createElement('label')
      option.className = 'omega-filter-status__option'
      const isChecked = selectedStatuses.includes(status.id)
      option.setAttribute('data-checked', isChecked ? 'true' : 'false')

      const checkbox = document.createElement('input')
      checkbox.type = 'checkbox'
      checkbox.value = status.id
      checkbox.checked = isChecked

      const span = document.createElement('span')
      span.textContent = status.label

      option.appendChild(checkbox)
      option.appendChild(span)

      // Usa onchange em vez de addEventListener
      checkbox.onchange = () => {
        option.setAttribute('data-checked', checkbox.checked ? 'true' : 'false')
      }

      statusHost.appendChild(option)
    })
  }

  function renderPrioritySelect(root: HTMLElement) {
    const prioritySelect = root.querySelector('#omega-filter-priority') as HTMLSelectElement
    if (!prioritySelect) return

    const currentValue = prioritySelect.value
    const priorities = [
      { value: '', label: 'Todas' },
      { value: 'baixa', label: 'Baixa' },
      { value: 'media', label: 'Média' },
      { value: 'alta', label: 'Alta' },
      { value: 'critica', label: 'Crítica' }
    ]

    // Limpa opções existentes
    while (prioritySelect.firstChild) {
      prioritySelect.removeChild(prioritySelect.firstChild)
    }
    
    priorities.forEach((priority) => {
      const option = document.createElement('option')
      option.value = priority.value
      option.textContent = priority.label
      if (priority.value === currentValue) {
        option.selected = true
      }
      prioritySelect.appendChild(option)
    })
  }

  function openTicketDetails(ticketId: string) {
    // TODO: Implementar abertura do modal de detalhes
  }

  function renderTickets(root: HTMLElement) {
    const tbody = root.querySelector('#omega-ticket-rows')
    if (!tbody) {
      return
    }
    
    // Verifica se estamos usando componentes Vue (OmegaTable)
    const container = root.querySelector('.omega-table-container')
    if (container) {
      // Não manipula o DOM se estiver usando componentes Vue
      return
    }

    let tickets = omega.tickets.value
    const statuses = omega.statuses.value
    const currentUser = omega.currentUser.value

    if (!tickets || tickets.length === 0) {
      // Limpa tbody
      while (tbody.firstChild) {
        tbody.removeChild(tbody.firstChild)
      }
      
      // Cria linha de estado vazio usando createElement
      const row = document.createElement('tr')
      const cell = document.createElement('td')
      cell.colSpan = 12
      cell.className = 'omega-empty-state'
      
      const content = document.createElement('div')
      content.className = 'omega-empty-state__content'
      
      const icon = document.createElement('i')
      icon.className = 'ti ti-inbox'
      icon.style.cssText = 'font-size: 64px; color: var(--brad-color-gray, #c7c7c7); margin-bottom: 20px; opacity: 0.6;'
      
      const h3 = document.createElement('h3')
      h3.textContent = 'Nenhum chamado encontrado'
      
      const p = document.createElement('p')
      p.textContent = 'Não há chamados para exibir nesta visualização.'
      
      const btn = document.createElement('button')
      btn.className = 'omega-btn omega-btn--primary'
      btn.id = 'omega-new-ticket-empty'
      btn.style.cssText = 'margin-top: 20px;'
      
      const btnIcon = document.createElement('i')
      btnIcon.className = 'ti ti-plus'
      const btnSpan = document.createElement('span')
      btnSpan.textContent = 'Registrar novo chamado'
      btn.appendChild(btnIcon)
      btn.appendChild(btnSpan)
      
      // Usa onclick em vez de addEventListener
      btn.onclick = () => {
        const drawer = root.querySelector('#omega-drawer')
        if (drawer) {
          drawer.removeAttribute('hidden')
        }
      }
      
      content.appendChild(icon)
      content.appendChild(h3)
      content.appendChild(p)
      content.appendChild(btn)
      cell.appendChild(content)
      row.appendChild(cell)
      tbody.appendChild(row)

      return
    }

    // Filtra tickets baseado na view atual (ANTES de aplicar filtros)
    // Cada usuário vê apenas sua própria fila
    let filteredTickets = tickets
    const currentView = omega.currentView.value

    if (currentView === 'my' && currentUser) {
      // Usuário vê apenas seus próprios chamados
      filteredTickets = tickets.filter((t: any) => t.requesterId === currentUser.id)
    } else if (currentView === 'assigned' && currentUser) {
      // Analista/supervisor vê apenas chamados atribuídos a ele
      filteredTickets = tickets.filter((t: any) => t.ownerId === currentUser.id)
    } else if (currentView === 'queue' && currentUser) {
      // Filtra por filas do usuário - cada um vê apenas suas próprias filas
      if (currentUser.queues && currentUser.queues.length > 0) {
        filteredTickets = tickets.filter((t: any) =>
          currentUser.queues.includes(t.queue)
        )
      } else {
        // Se não tem filas definidas, não mostra nada (exceto admin)
        if (currentUser.role !== 'admin') {
          filteredTickets = []
        }
      }
    }

    // Aplica filtros avançados (DEPOIS de filtrar por view)
    filteredTickets = filters.applyFilters(filteredTickets)

    // Limpa tbody
    while (tbody.firstChild) {
      tbody.removeChild(tbody.firstChild)
    }

    filteredTickets.forEach((ticket: any, index: number) => {
      const row = document.createElement('tr')
      row.className = 'omega-table__row'
      row.setAttribute('data-ticket-id', ticket.id)
      row.style.opacity = '0'
      row.style.transform = 'translateY(10px)'
      row.style.transition = `opacity 0.3s ease ${index * 0.03}s, transform 0.3s ease ${index * 0.03}s`

      const status = statuses.find((s: any) => s.id === ticket.status) || statuses[0] || { id: 'unknown', label: 'Desconhecido', tone: 'neutral' as const }
      const priorityMeta = omega.getPriorityMeta(ticket.priority)
      
      // Determina se deve mostrar prioridade e atendente baseado no role
      const canSelect = currentUser && ['analista', 'supervisor', 'admin'].includes(currentUser.role)
      const showPriority = currentUser && ['analista', 'supervisor', 'admin'].includes(currentUser.role)
      const showOwner = currentUser && ['analista', 'supervisor', 'admin'].includes(currentUser.role)
      const ownerName = ticket.ownerId 
        ? (omega.users.value.find((u: any) => u.id === ticket.ownerId)?.name || '—')
        : 'Sem responsável'

      const openedDate = new Date(ticket.opened)
      const updatedDate = new Date(ticket.updated)

      const isSelected = bulk.selectedTicketIds.value.has(ticket.id)

      // Monta HTML da linha baseado nas permissões
      const selectCell = canSelect ? `
        <td class="col-select">
          <input type="checkbox" data-omega-select value="${ticket.id}" ${isSelected ? 'checked' : ''} aria-label="Selecionar chamado ${ticket.id}"/>
        </td>
      ` : ''
      
      const priorityCell = showPriority ? `
        <td data-priority="${ticket.priority}">
          <span class="omega-badge omega-badge--${priorityMeta.tone}">
            <i class="${priorityMeta.icon}"></i>
            ${priorityMeta.label}
          </span>
        </td>
      ` : ''
      
      const ownerCell = showOwner ? `
        <td>${ownerName}</td>
      ` : ''

      row.innerHTML = `
        ${selectCell}
        <td>${ticket.id}</td>
        <td>
          <div class="omega-table__preview">
            <strong>${ticket.subject || 'Sem assunto'}</strong>
            <small>${ticket.requesterName}</small>
          </div>
        </td>
        <td>${ticket.queue || '—'}</td>
        <td>${ticket.category || '—'}</td>
        <td>${ticket.requesterName || '—'}</td>
        ${priorityCell}
        ${ownerCell}
        <td>${ticket.product || '—'}</td>
        <td>${ticket.queue || '—'}</td>
        <td>${openedDate.toLocaleDateString('pt-BR')}</td>
        <td>${updatedDate.toLocaleDateString('pt-BR')}</td>
        <td class="col-status">
          <span class="omega-badge omega-badge--${status.tone}">
            ${status.label}
          </span>
        </td>
      `

      // Adiciona listener para checkbox de seleção
      const checkbox = row.querySelector('input[data-omega-select]') as HTMLInputElement
      if (checkbox) {
        checkbox.addEventListener('click', (e) => {
          e.stopPropagation()
          if (checkbox.checked) {
            bulk.selectedTicketIds.value.add(ticket.id)
          } else {
            bulk.selectedTicketIds.value.delete(ticket.id)
          }
          bulk.renderBulkPanel(root)
        })
      }

      // Adiciona listener para abrir detalhes do ticket
      row.addEventListener('click', (e) => {
        if ((e.target as HTMLElement).tagName === 'INPUT' || (e.target as HTMLElement).closest('input')) {
          return
        }
        // Adiciona efeito de ripple
        const ripple = document.createElement('span')
        const rect = row.getBoundingClientRect()
        const x = (e as MouseEvent).clientX - rect.left
        const y = (e as MouseEvent).clientY - rect.top
        ripple.style.left = `${x}px`
        ripple.style.top = `${y}px`
        ripple.className = 'omega-ripple'
        row.appendChild(ripple)

        setTimeout(() => {
          ripple.remove()
          openTicketDetails(ticket.id)
        }, 300)
      })

      // Adiciona hover effect
      row.addEventListener('mouseenter', () => {
        row.style.transform = 'translateX(4px)'
      })
      row.addEventListener('mouseleave', () => {
        row.style.transform = 'translateX(0)'
      })

      tbody.appendChild(row)

      // Anima entrada
      setTimeout(() => {
        row.style.opacity = '1'
        row.style.transform = 'translateY(0)'
      }, 10)
    })

    // Atualiza resumo
    const summary = root.querySelector('#omega-summary')
    if (summary) {
      summary.textContent = `${filteredTickets.length} chamado${filteredTickets.length !== 1 ? 's' : ''} encontrado${filteredTickets.length !== 1 ? 's' : ''}`
    }

    // Mostra footer da tabela
    const tableFooter = root.querySelector('.omega-table-footer')
    if (tableFooter) {
      tableFooter.removeAttribute('hidden')
    }

    const tableRange = root.querySelector('#omega-table-range')
    if (tableRange && filteredTickets.length > 0) {
      tableRange.textContent = `1-${filteredTickets.length} de ${filteredTickets.length}`
    }
  }

  function renderOmegaData(rootRef?: HTMLElement | null) {
    // Usa ref se fornecido, senão tenta encontrar no DOM (compatibilidade)
    let rootElement: HTMLElement | null = rootRef || null
    
    if (!rootElement) {
      rootElement = document.querySelector('#omega-page') as HTMLElement || 
                   document.querySelector('#omega-modal') as HTMLElement
    }
    
    const modalElement = rootElement?.id === 'omega-modal' ? rootElement : null
    if (!rootElement || (modalElement && modalElement.hidden)) {
      console.warn('⚠️ Modal não encontrado ou está oculto')
      return
    }

    // Verifica se estamos usando componentes Vue (OmegaTable)
    const omegaTableComponent = rootElement.querySelector('.omega-table-container')
    const isUsingVueComponents = !!omegaTableComponent

    // Renderiza usuários no select (ainda necessário para o header)
    renderUsers(rootElement)

    // Renderiza tickets na tabela APENAS se não estiver usando componentes Vue
    if (!isUsingVueComponents) {
      renderTickets(rootElement)
    }

    // Renderiza perfil do usuário atual
    renderProfile(rootElement)

    // Renderiza navegação
    renderNavigation(rootElement)

    // Renderiza estrutura (departamentos e tipos) nos selects
    renderStructure(rootElement)

    // Renderiza select de prioridade
    renderPrioritySelect(rootElement)

    // Renderiza painel bulk APENAS se não estiver usando componentes Vue
    if (!isUsingVueComponents) {
      bulk.renderBulkPanel(rootElement)
    }

    // Atualiza estado do botão de filtros (se existir no DOM)
    const filterToggle = rootElement.querySelector('#omega-filters-toggle')
    if (filterToggle) {
      filterToggle.setAttribute('data-active', filters.hasActiveFilters() ? 'true' : 'false')
    }

  }

  return {
    renderUsers,
    renderProfile,
    renderNavigation,
    renderStructure,
    renderTickets,
    renderOmegaData,
    renderTypeSelect,
    renderStatusOptions,
    renderPrioritySelect,
    openTicketDetails
  }
}

