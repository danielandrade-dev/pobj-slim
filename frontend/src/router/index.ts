import { createRouter, createWebHistory } from 'vue-router'
import type { RouteRecordRaw } from 'vue-router'

const routes: RouteRecordRaw[] = [
  {
    path: '/',
    name: 'Home',
    component: () => import('../views/Home.vue')
  },
  {
    path: '/detalhes',
    name: 'Detalhes',
    component: () => import('../views/Detalhes.vue'),
    alias: ['/table']
  },
  {
    path: '/ranking',
    name: 'Ranking',
    component: () => import('../views/Ranking.vue')
  },
  {
    path: '/campanhas',
    name: 'Campanhas',
    component: () => import('../views/Campanhas.vue')
  },
  {
    path: '/simuladores',
    name: 'Simuladores',
    component: () => import('../views/Simuladores.vue')
  },
  {
    path: '/exec',
    name: 'VisaoExecutiva',
    component: () => import('../views/VisaoExecutiva.vue')
  },
  {
    path: '/omega',
    name: 'Omega',
    component: () => import('../views/Omega.vue')
  },
  {
    path: '/:pathMatch(.*)*',
    name: 'NotFound',
    component: () => import('../views/NotFound.vue')
  }
]

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes
})

export default router
