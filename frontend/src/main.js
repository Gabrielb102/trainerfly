import './assets/main.css'

// MapLibre
import 'maplibre-gl/dist/maplibre-gl.css';
import VueMaplibreGl from '@indoorequal/vue-maplibre-gl'

import { createApp } from 'vue'
import { createPinia } from 'pinia'

import App from './App.vue'
import router from './router'

const app = createApp(App)
app.use(createPinia())
app.use(router)
app.use(VueMaplibreGl)

app.mount('#trainerfly')
