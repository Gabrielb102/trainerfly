import {ref} from 'vue'
import L from 'leaflet'

export function useMap(mapId) {
  const lat = ref(51.505);
  const lng = ref(-0.09);
  const zoom = ref(14);

  const createMap = () => {
    const map = L.map(mapId).setView([lat.value, lng.value], zoom.value);

    L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
      maxZoom: 19,
      attribution: '&copy; <a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a>'
    }).addTo(map);
  }

  return {
    createMap,
    lat,
    lng,
    zoom
  };
}
