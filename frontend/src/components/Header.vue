<script setup lang="ts">
import { ref } from 'vue'

const userMenuOpen = ref(false)
const submenuOpen = ref<string | null>(null)

const toggleUserMenu = (): void => {
  userMenuOpen.value = !userMenuOpen.value
}

const toggleSubmenu = (submenu: string): void => {
  submenuOpen.value = submenuOpen.value === submenu ? null : submenu
}

const openOmegaModal = (): void => {
  if (typeof window === 'undefined') return
  // eslint-disable-next-line @typescript-eslint/no-explicit-any
  const globalAny = window as any
  const opener =
    globalAny.__openOmegaFromVue ||
    globalAny.openOmegaModule ||
    globalAny.openOmega ||
    globalAny.launchOmegaStandalone

  if (typeof opener === 'function') {
    opener()
  } else {
    console.warn('Módulo Omega não está disponível.')
  }
}

const handleMenuAction = async (action: string): Promise<void> => {
  if (action === 'omega') {
    openOmegaModal()
    userMenuOpen.value = false
    return
  }

  if (action === 'logout') {
    console.log('Logout')
  }

  console.log('Menu action:', action)
  userMenuOpen.value = false
}
</script>

<template>
  <header class="topbar" role="banner">
    <nav id="main-navigation" class="topbar__left" role="navigation" aria-label="Navegação principal">
      <a href="/" class="logo" aria-label="Bradesco - Página inicial">
        <span class="logo__mark" aria-hidden="true"></span>
        <span class="logo__text">Bradesco</span>
      </a>
    </nav>

    <nav class="topbar__right" role="navigation" aria-label="Menu do usuário">
      <div class="userbox">
        <button
          class="userbox__trigger"
          id="btn-user-menu"
          type="button"
          aria-haspopup="true"
          :aria-expanded="userMenuOpen"
          aria-controls="user-menu"
          aria-label="Menu do usuário: João da Silva"
          @click="toggleUserMenu"
          @keydown.escape="userMenuOpen && toggleUserMenu()"
        >
          <img
            class="userbox__avatar"
            src="https://i.pravatar.cc/80?img=12"
            alt="Foto do usuário João da Silva"
          />
          <span class="userbox__name">João da Silva</span>
          <i class="ti ti-chevron-down" aria-hidden="true" :aria-label="userMenuOpen ? 'Fechar menu' : 'Abrir menu'"></i>
        </button>
        <Transition name="dropdown">
          <div
            v-if="userMenuOpen"
            class="userbox__menu"
            id="user-menu"
            role="menu"
            :aria-hidden="!userMenuOpen"
            aria-label="Menu do usuário"
            @keydown.escape="toggleUserMenu()"
          >
          <span class="userbox__menu-title" role="heading" aria-level="2">Links úteis</span>
          <button
            class="userbox__menu-item"
            type="button"
            role="menuitem"
            @click="handleMenuAction('portal')"
          >
            Portal PJ
          </button>
          <div class="userbox__submenu">
            <button
              class="userbox__menu-item userbox__menu-item--has-sub"
              type="button"
              role="menuitem"
              :aria-expanded="submenuOpen === 'manuais'"
              aria-controls="user-submenu-manuais"
              @click="toggleSubmenu('manuais')"
              @keydown.enter.prevent="toggleSubmenu('manuais')"
              @keydown.space.prevent="toggleSubmenu('manuais')"
            >
              Manuais
              <i class="ti ti-chevron-right" aria-hidden="true"></i>
            </button>
            <Transition name="submenu">
              <div
                v-if="submenuOpen === 'manuais'"
                class="userbox__submenu-list"
                id="user-submenu-manuais"
                role="menu"
                aria-label="Submenu Manuais"
              >
              <button
                class="userbox__menu-item"
                type="button"
                role="menuitem"
                @click="handleMenuAction('manual1')"
              >
                Manual 1
              </button>
              <button
                class="userbox__menu-item"
                type="button"
                role="menuitem"
                @click="handleMenuAction('manual2')"
              >
                Manual 2
              </button>
              </div>
            </Transition>
          </div>
          <button
            class="userbox__menu-item"
            type="button"
            role="menuitem"
            @click="handleMenuAction('mapao')"
          >
            Mapão de Oportunidades
          </button>
          <button
            class="userbox__menu-item"
            type="button"
            role="menuitem"
            data-action="omega"
            @click="handleMenuAction('omega')"
          >
            Omega
          </button>
          <hr class="userbox__divider" role="separator" aria-orientation="horizontal" />
          <button
            class="userbox__menu-item userbox__menu-item--logout"
            type="button"
            role="menuitem"
            aria-label="Sair da aplicação"
            @click="handleMenuAction('logout')"
          >
            <i class="ti ti-logout-2" aria-hidden="true"></i>
            <span>Sair</span>
          </button>
          </div>
        </Transition>
      </div>
    </nav>
  </header>
</template>

<style scoped>
.topbar {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  z-index: 1100;
  width: 100%;
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 12px;
  padding: 8px 12px;
  background: var(--brad-color-primary-gradient);
  color: var(--brad-color-on-bg-primary);
  box-shadow: 0 6px 16px rgba(204, 9, 47, 0.25);
  margin: 0;
  border: none;
  animation: slideInDown 0.4s cubic-bezier(0.25, 0.1, 0.25, 1);
}

@keyframes slideInDown {
  from {
    opacity: 0;
    transform: translateY(-100%);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

.topbar__left,
.topbar__right {
  display: flex;
  align-items: center;
  gap: 10px;
  min-width: 0;
}

/* Transições para dropdown */
.dropdown-enter-active {
  transition: all 0.3s cubic-bezier(0.25, 0.1, 0.25, 1);
}

.dropdown-leave-active {
  transition: all 0.2s cubic-bezier(0.25, 0.1, 0.25, 1);
}

.dropdown-enter-from {
  opacity: 0;
  transform: translateY(-10px) scale(0.95);
}

.dropdown-leave-to {
  opacity: 0;
  transform: translateY(-10px) scale(0.95);
}

.logo {
  display: flex;
  align-items: center;
  gap: 10px;
  min-width: 0;
}

.logo__mark {
  width: 28px;
  height: 28px;
  border-radius: 50%;
  background: #fff;
  background-image: url('/img/bra-logo.png');
  background-size: 70%;
  background-position: center;
  background-repeat: no-repeat;
  flex: 0 0 28px;
}

.logo__text {
  font-weight: 800;
  color: #fff;
  max-width: 200px;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}

.userbox {
  position: relative;
  display: flex;
  align-items: center;
  min-width: 0;
}

.userbox__trigger {
  display: flex;
  align-items: center;
  gap: 10px;
  background: transparent;
  border: 1.5px solid rgba(255, 255, 255, 1);
  border-radius: 999px;
  padding: 6px 12px;
  color: #fff;
  font-weight: 700;
  cursor: pointer;
  transition: all 0.3s cubic-bezier(0.25, 0.1, 0.25, 1);
  transform: scale(1);
}

.userbox__trigger:hover {
  background: rgba(255, 255, 255, 0.15);
  border-color: rgba(255, 255, 255, 1);
  transform: scale(1.02);
}

.userbox__trigger:active {
  transform: scale(0.98);
}

.userbox__trigger:focus-visible {
  outline: 2px solid rgba(255, 255, 255, 0.85);
  outline-offset: 2px;
}

.userbox__trigger i {
  font-size: 16px;
  stroke-width: 1.5;
  color: #fff;
}

.userbox__avatar {
  width: 36px;
  height: 36px;
  border-radius: 50%;
  border: 2px solid rgba(255, 255, 255, 1);
  object-fit: cover;
  flex: 0 0 36px;
}

.userbox__name {
  font-family: var(--brad-font-family);
  font-size: 14px;
  font-weight: var(--brad-font-weight-semibold);
  color: #fff;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
  max-width: 140px;
}

.userbox__menu {
  position: absolute;
  top: calc(100% + 12px);
  right: 0;
  background: #fff;
  color: #0f1724;
  border-radius: 14px;
  box-shadow: 0 18px 38px rgba(15, 20, 36, 0.18);
  padding: 12px;
  min-width: 220px;
  display: flex;
  flex-direction: column;
  gap: 6px;
  z-index: 1400;
  border: 1px solid rgba(15, 23, 42, 0.08);
  transform-origin: top right;
}

.userbox__menu-title {
  font-size: 12px;
  font-weight: 800;
  color: #64748b;
  text-transform: uppercase;
  letter-spacing: 0.05em;
}

.userbox__menu-item {
  border: none;
  background: transparent;
  color: #0f1724;
  font-size: 13px;
  font-weight: 600;
  padding: 8px 10px;
  border-radius: 10px;
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 8px;
  cursor: pointer;
  transition: all 0.2s cubic-bezier(0.25, 0.1, 0.25, 1);
  transform: translateX(0);
}

.userbox__menu-item:hover,
.userbox__menu-item:focus-visible {
  background: rgba(204, 9, 47, 0.12);
  color: var(--brand);
  outline: none;
  transform: translateX(4px);
}

.userbox__menu-item--logout {
  color: #b30000;
}

.userbox__menu-item--logout i {
  margin-right: 4px;
}

.userbox__divider {
  width: 100%;
  height: 1px;
  border: none;
  background: #e5e7eb;
  margin: 6px 0;
}

.userbox__submenu {
  display: flex;
  flex-direction: column;
  gap: 4px;
}

.userbox__menu-item--has-sub {
  justify-content: space-between;
}

.userbox__menu-item--has-sub i {
  transition: transform 0.3s cubic-bezier(0.25, 0.1, 0.25, 1);
}

.userbox__menu-item--has-sub[aria-expanded='true'] i {
  transform: rotate(90deg);
}

.userbox__submenu-list {
  display: flex;
  flex-direction: column;
  gap: 4px;
  padding-left: 12px;
  overflow: hidden;
}

/* Transições para submenu */
.submenu-enter-active {
  transition: all 0.3s cubic-bezier(0.25, 0.1, 0.25, 1);
}

.submenu-leave-active {
  transition: all 0.2s cubic-bezier(0.25, 0.1, 0.25, 1);
}

.submenu-enter-from {
  opacity: 0;
  max-height: 0;
  transform: translateX(-10px);
}

.submenu-enter-to {
  opacity: 1;
  max-height: 200px;
  transform: translateX(0);
}

.submenu-leave-from {
  opacity: 1;
  max-height: 200px;
  transform: translateX(0);
}

.submenu-leave-to {
  opacity: 0;
  max-height: 0;
  transform: translateX(-10px);
}

.userbox__menu button {
  font-family: inherit;
}

.sr-only {
  position: absolute;
  width: 1px;
  height: 1px;
  padding: 0;
  margin: -1px;
  overflow: hidden;
  clip: rect(0, 0, 0, 0);
  white-space: nowrap;
  border-width: 0;
}
</style>

