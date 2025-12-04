import { createRouter, createWebHistory } from 'vue-router'
import type { RouteRecordRaw } from 'vue-router'
import Home from '../pages/Home.vue'
import Ranking from '../pages/Ranking.vue'
import Campanhas from '../pages/Campanhas.vue'
import Simuladores from '../pages/Simuladores.vue'
import Detalhes from '../pages/Detalhes.vue'
import VisaoExecutiva from '../pages/VisaoExecutiva.vue'
import Omega from '../pages/Omega.vue'
import NotFound from '../pages/NotFound.vue'

const routes: RouteRecordRaw[] = [
  {
    path: '/',
    name: 'Home',
    component: Home
  },
  {
    path: '/detalhes',
    name: 'Detalhes',
    component: Detalhes,
    alias: ['/table']
  },
  {
    path: '/ranking',
    name: 'Ranking',
    component: Ranking
  },
  {
    path: '/campanhas',
    name: 'Campanhas',
    component: Campanhas
  },
  {
    path: '/simuladores',
    name: 'Simuladores',
    component: Simuladores
  },
  {
    path: '/exec',
    name: 'VisaoExecutiva',
    component: VisaoExecutiva
  },
  {
    path: '/omega',
    name: 'Omega',
    component: Omega
  },
  {
    path: '/:pathMatch(.*)*',
    name: 'NotFound',
    component: NotFound
  }
]

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes
})

export default router
