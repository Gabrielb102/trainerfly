import { createRouter, createWebHistory } from 'vue-router'
import MapScreen from '@/views/MapScreen.vue'
import ListingScreen from '@/views/ListingScreen.vue'

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes: [
    {
      path: '/',
      name: 'start',
      component: MapScreen,
    },
    {
      path: '/listing/:id',
      name: 'listing',
      component: ListingScreen,
    },
  ],
})

export default router
