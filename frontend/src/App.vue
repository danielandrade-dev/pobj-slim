<script setup lang="ts">
import { defineAsyncComponent, onMounted, computed } from 'vue'
import Header from './components/Header.vue'
import Footer from './components/Footer.vue'
import ChatWidget from './components/ChatWidget.vue'
import { useRoute, useRouter } from 'vue-router'

interface OmegaDetail {
  openDrawer?: boolean
  intent?: string
  preferredQueue?: string
  queue?: string
  observation?: string
}

onMounted(async () => {
  await Promise.all([
    import('./assets/animations.css'),
    import('./assets/microinteractions.css'),
    import('./assets/accessibility.css')
  ])
})

const route = useRoute()
const router = useRouter()
const isOmegaRoute = computed(() => route.name === 'Omega')

const Filters = defineAsyncComponent(() => import('./components/Filters.vue'))
const TabsNavigation = defineAsyncComponent(() => import('./components/TabsNavigation.vue'))

function openOmegaInNewTab(detail?: OmegaDetail): void {
  if (typeof window === 'undefined') return

  const query: Record<string, string> = {}
  const shouldOpenDrawer = detail?.openDrawer || detail?.intent === 'new-ticket'

  if (shouldOpenDrawer) {
    query.openDrawer = 'true'
    query.intent = 'new-ticket'
    const queue = detail?.preferredQueue || detail?.queue || 'POBJ'
    query.preferredQueue = queue
    query.queue = queue
    if (detail?.observation) {
      query.observation = detail.observation
    }
  }

  const omegaRoute = router.resolve({ name: 'Omega', query })
  window.open(omegaRoute.href, '_blank')
}

if (typeof window !== 'undefined') {
  const globalWindow = window as Window & Record<string, unknown>
  globalWindow.__openOmegaFromVue = openOmegaInNewTab
  globalWindow.openOmegaModule = openOmegaInNewTab
  globalWindow.openOmega = openOmegaInNewTab
}
</script>

<template>
  <div class="app">
    <a href="#main-content" class="skip-link">Pular para conteúdo principal</a>
    <a href="#main-navigation" class="skip-link">Pular para navegação</a>
    
    <Header />
    <main id="main-content" class="main-content" role="main" aria-label="Conteúdo principal">
      <div class="main-container" :class="{ 'is-omega': isOmegaRoute }">
        <Filters v-if="!isOmegaRoute" />
        <TabsNavigation v-if="!isOmegaRoute" />
        
        <div class="view-content" :class="{ 'is-omega': isOmegaRoute }">
          <router-view v-slot="{ Component }">
            <Transition name="page" mode="out-in">
              <component :is="Component" :key="route.path" />
            </Transition>
          </router-view>
        </div>
      </div>
    </main>
    <Footer v-if="!isOmegaRoute" />
    <ChatWidget v-if="!isOmegaRoute" />
  </div>
</template>

<style>
* {
  box-sizing: border-box;
  margin: 0;
  padding: 0;
}

html,
body {
  margin: 0;
  padding: 0;
  width: 100%;
  overflow-x: hidden;
  font-family: "Bradesco", system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Arial, sans-serif;
  -webkit-font-smoothing: antialiased;
  -moz-osx-font-smoothing: grayscale;
}

#app {
  width: 100%;
  margin: 0;
  padding: 0;
  font-family: "Bradesco", system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Arial, sans-serif;
}

/* Garantir que SVGs dos ícones sejam exibidos */
svg {
  display: inline-block !important;
  vertical-align: middle;
}

body {
  background-color: #f6f7fc;
  background-image:
    url("./bg.svg"),
    radial-gradient(1200px 720px at 95% -30%, #dfe8ff 0%, transparent 60%),
    radial-gradient(1100px 720px at -25% -10%, #ffe6ea 0%, transparent 55%);
  background-repeat: repeat, no-repeat, no-repeat;
  background-size: 320px 320px, auto, auto;
  background-position: center center, 95% -30%, -25% -10%;
  background-attachment: fixed;
}
</style>

<style scoped>
.app {
  min-height: 100vh;
  width: 100%;
  display: flex;
  flex-direction: column;
  margin: 0;
  padding: 0;
}

.main-content {
  flex: 1;
  width: 100%;
  padding-top: 66px;
}


.main-container {
  max-width: min(1600px, 96vw);
  margin: 18px auto;
  padding: 0 16px;
}

.main-container.is-omega {
  max-width: 100%;
  margin: 0;
  padding: 0;
}

.view-content {
  width: 100%;
}

.view-content.is-omega {
  width: 100%;
  padding: 0;
}

.page-enter-active {
  transition: all 0.4s cubic-bezier(0.25, 0.1, 0.25, 1);
}

.page-leave-active {
  transition: all 0.3s cubic-bezier(0.25, 0.1, 0.25, 1);
}

.page-enter-from {
  opacity: 0;
  transform: translateY(20px);
}

.page-leave-to {
  opacity: 0;
  transform: translateY(-10px);
}
</style>
