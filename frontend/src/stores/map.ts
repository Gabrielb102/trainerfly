import { ref } from 'vue'
import { defineStore } from 'pinia'
import { MapboxFeature} from '@/types/map/mapbox-types'

export const useMapStore = defineStore('map', () => {

  const defaultLocation: MapboxFeature = {
    type: "Feature",
    geometry: {
      type: "Point",
      coordinates: [100, 40]   // [lon, lat] per GeoJSON spec
    },
    properties: {
      mapbox_id: '0' // ← add custom attributes here
    }
  };
  const location = ref<MapboxFeature>(defaultLocation)

  return { location }
})
