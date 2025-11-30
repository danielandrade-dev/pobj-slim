export function useOmegaRender(
  omega: any,
  filters: any,
  bulk: any
) {
  function renderUsers(root: HTMLElement) {
    const userSelect = root.querySelector('#omega-user-select') as HTMLSelectElement
    if (!userSelect) return

    const currentUsers = omega.users.value
    userSelect.innerHTML = ''

    currentUsers.forEach((user) => {
      const option = document.createElement('option')
      option.value = user.id
      option.textContent = user.name
      if (user.id === omega.currentUserId.value) {
        option.selected = true
      }
      userSelect.appendChild(option)
    })

    // Adiciona listener para mudança de usuário
    userSelect.addEventListener('change', (e) => {
      const target = e.target as HTMLSelectElement
      omega.setCurrentUserId(target.value)
      renderProfile(root)
      renderTickets(root)
    })
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

    navElement.innerHTML = ''

    navItems.forEach((item) => {
      const navItem = document.createElement('button')
      navItem.className = 'omega-nav__item'
      navItem.type = 'button'
      navItem.setAttribute('data-omega-view', item.id)
      navItem.innerHTML = `
        <i class="${item.icon}"></i>
        <span>${item.label}</span>
      `

      if (item.id === omega.currentView.value) {
        navItem.classList.add('omega-nav__item--active')
      }

      navItem.addEventListener('click', () => {
        omega.setCurrentView(item.id as any)
        renderTickets(root)
        // Atualiza estado ativo
        navElement.querySelectorAll('.omega-nav__item').forEach((el) => {
          el.classList.remove('omega-nav__item--active')
        })
        navItem.classList.add('omega-nav__item--active')
      })

      navElement.appendChild(navItem)
    })
  }

  function renderStructure(root: HTMLElement) {
    const structure = omega.structure.value
    if (!structure || structure.length === 0) return

    // Popula selects de departamento
    const departmentSelects = root.querySelectorAll('[id*="department"], [id*="filter-department"]')
    departmentSelects.forEach((select) => {
      const selectEl = select as HTMLSelectElement
      const currentValue = selectEl.value

      // Agrupa por departamento
      const departments = new Map<string, string>()
      structure.forEach((item) => {
        if (!departments.has(item.departamento)) {
          departments.set(item.departamento, item.departamento_id || item.departamento)
        }
      })

      selectEl.innerHTML = '<option value="">Selecione...</option>'
      departments.forEach((id, name) => {
        const option = document.createElement('option')
        option.value = id
        option.textContent = name
        if (id === currentValue) {
          option.selected = true
        }
        selectEl.appendChild(option)
      })
    })
  }

  function openTicketDetails(ticketId: string) {
    console.log('🔍 Abrindo detalhes do ticket:', ticketId)
    // TODO: Implementar abertura do modal de detalhes
  }

  function renderTickets(root: HTMLElement) {
    const tbody = root.querySelector('#omega-ticket-rows')
    if (!tbody) {
      console.warn('⚠️ Tbody #omega-ticket-rows não encontrado')
      return
    }

    let tickets = omega.tickets.value
    const statuses = omega.statuses.value
    const currentUser = omega.currentUser.value

    if (!tickets || tickets.length === 0) {
      tbody.innerHTML = `
        <tr>
          <td colspan="12" class="omega-empty-state">
            <div class="omega-empty-state__content">
              <i class="ti ti-inbox" style="font-size: 64px; color: var(--brad-color-gray, #c7c7c7); margin-bottom: 20px; opacity: 0.6;"></i>
              <h3>Nenhum chamado encontrado</h3>
              <p>Não há chamados para exibir nesta visualização.</p>
              <button class="omega-btn omega-btn--primary" id="omega-new-ticket-empty" style="margin-top: 20px;">
                <i class="ti ti-plus"></i>
                <span>Registrar novo chamado</span>
              </button>
            </div>
          </td>
        </tr>
      `

      // Adiciona listener ao botão de novo ticket no estado vazio
      const newTicketBtn = tbody.querySelector('#omega-new-ticket-empty')
      if (newTicketBtn) {
        newTicketBtn.addEventListener('click', () => {
          const drawer = root.querySelector('#omega-drawer')
          if (drawer) {
            drawer.removeAttribute('hidden')
          }
        })
      }

      return
    }

    // Filtra tickets baseado na view atual (ANTES de aplicar filtros)
    let filteredTickets = tickets
    const currentView = omega.currentView.value

    if (currentView === 'my' && currentUser) {
      filteredTickets = tickets.filter((t) => t.requesterId === currentUser.id)
    } else if (currentView === 'assigned' && currentUser) {
      filteredTickets = tickets.filter((t) => t.ownerId === currentUser.id)
    } else if (currentView === 'queue' && currentUser) {
      filteredTickets = tickets.filter((t) =>
        currentUser.queues.includes(t.queue)
      )
    }

    // Aplica filtros avançados (DEPOIS de filtrar por view)
    filteredTickets = filters.applyFilters(filteredTickets)

    tbody.innerHTML = ''

    filteredTickets.forEach((ticket, index) => {
      const row = document.createElement('tr')
      row.className = 'omega-table__row'
      row.setAttribute('data-ticket-id', ticket.id)
      row.style.opacity = '0'
      row.style.transform = 'translateY(10px)'
      row.style.transition = `opacity 0.3s ease ${index * 0.03}s, transform 0.3s ease ${index * 0.03}s`

      const status = statuses.find((s) => s.id === ticket.status) || statuses[0] || { id: 'unknown', label: 'Desconhecido', tone: 'neutral' as const }
      const priorityMeta = omega.getPriorityMeta(ticket.priority)

      const openedDate = new Date(ticket.opened)
      const updatedDate = new Date(ticket.updated)

      const isSelected = bulk.selectedTicketIds.value.has(ticket.id)

      row.innerHTML = `
        <td class="col-select">
          <input type="checkbox" data-omega-select value="${ticket.id}" ${isSelected ? 'checked' : ''} aria-label="Selecionar chamado ${ticket.id}"/>
        </td>
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
        <td data-priority="${ticket.priority}">
          <span class="omega-badge omega-badge--${priorityMeta.tone}">
            <i class="${priorityMeta.icon}"></i>
            ${priorityMeta.label}
          </span>
        </td>
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

  function renderOmegaData() {
    const modalElement = document.getElementById('omega-modal')
    if (!modalElement || modalElement.hidden) {
      console.warn('⚠️ Modal não encontrado ou está oculto')
      return
    }

    console.log('🎨 Renderizando dados do Omega no template...')

    // Renderiza usuários no select
    renderUsers(modalElement)

    // Renderiza tickets na tabela
    renderTickets(modalElement)

    // Renderiza perfil do usuário atual
    renderProfile(modalElement)

    // Renderiza navegação
    renderNavigation(modalElement)

    // Renderiza estrutura (departamentos e tipos) nos selects
    renderStructure(modalElement)

    // Renderiza painel bulk
    bulk.renderBulkPanel(modalElement)

    // Atualiza estado do botão de filtros
    const filterToggle = modalElement.querySelector('#omega-filters-toggle')
    if (filterToggle) {
      filterToggle.setAttribute('data-active', filters.hasActiveFilters() ? 'true' : 'false')
    }

    console.log('✅ Renderização concluída')
  }

  return {
    renderUsers,
    renderProfile,
    renderNavigation,
    renderStructure,
    renderTickets,
    renderOmegaData,
    openTicketDetails
  }
}

