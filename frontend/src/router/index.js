import { createRouter, createWebHistory } from 'vue-router'
import MapScreen from '@/views/MapScreen.vue'

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes: [
    {
      path: '/',
      name: 'start',
      component: MapScreen,
    }
  ],
})

export default router
