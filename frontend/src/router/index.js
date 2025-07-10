import { createRouter, createWebHashHistory } from 'vue-router'
import MapScreen from '@/views/MapScreen.vue'
import RemoteListingScreen from '@/views/RemoteListingScreen.vue'

const router = createRouter({
  history: createWebHashHistory(import.meta.env.BASE_URL),
  routes: [
    {
      path: '/',
      name: 'start',
      component: MapScreen,
    },
    {
      path: '/remote',
      name: 'remote-listings',
      component: RemoteListingScreen,
    },
  ],
})

export default router
