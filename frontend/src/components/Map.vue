<script setup lang="ts">
import { ref, computed, watch } from 'vue'
import { useMapStore } from '@/stores/map'
import { useListingStore } from '@/stores/listing'
import { useMap } from '@indoorequal/vue-maplibre-gl'
import { useListings } from '@/composables/useListings'
import { type LngLatLike } from 'maplibre-gl'
import { type MapboxFeature } from '@/types/map/mapbox-types'
import { type MapInstance } from '@indoorequal/vue-maplibre-gl'
import FreeDrawer from '@/components/FreeDrawer.vue'

const mapStore = useMapStore()
const map: MapInstance = useMap('main')
const location = computed<MapboxFeature>(() => mapStore.location)

type FreeDrawerType = InstanceType<typeof FreeDrawer>
const drawerRef = ref<FreeDrawerType>()

// <editor-fold desc="Fly to Location">---------------------------------------

// Helper function for fallback to coordinates
const fitToCoordinates = (location: MapboxFeature) => {
  if (!map.map || !location?.properties?.coordinates) return

  const { longitude, latitude } = location.properties.coordinates
  if (longitude !== null && latitude !== null) {
    map.map.flyTo({
      center: [longitude, latitude],
      zoom: 10,
      speed: 1.5,
      curve: 1
    })

    // Only show options after map animation completes
    setTimeout(() => {
      if (drawerRef.value) {
        drawerRef.value.isOpen = true
      }
    }, 1000)
  }
}

watch(() => location.value, (newLocation: MapboxFeature) => {

  if (!map.map) {
    console.log('Map not ready yet')
    return
  }

  if (newLocation?.properties?.bbox && Array.isArray(newLocation.properties.bbox) && newLocation.properties.bbox.length === 4) {
    // Use bbox to fit the map view to the bounding box
    // bbox format: [west, south, east, north]
    const [west, south, east, north] = newLocation.properties.bbox

    // Ensure all values are numbers and not null
    if (west !== null && south !== null && east !== null && north !== null) {
      // Create the coordinates of the bounds in the format expected by fitBounds
      const sw: LngLatLike = [west, south]
      const ne: LngLatLike = [east, north]

      // Fit the map to the bounds with some padding
      map.map.fitBounds([sw, ne], {
        padding: 50, // Add some padding around the bounds
        maxZoom: 14, // Limit zoom to avoid getting too close
        duration: 1500, // Animation duration in milliseconds
        animate: true
      })

      // Only show options after map animation completes
      setTimeout(() => {
        console.log('Drawer Ref', drawerRef.value)
        if (drawerRef.value) {
          drawerRef.value.isOpen = true
        }
      }, 1000) // Set timeout slightly longer than animation duration
    } else {
      // Fallback to coordinates if any bbox value is null
      fitToCoordinates(newLocation)
    }
  } else if (newLocation?.properties?.coordinates) {
    // Fallback if bbox isn't available
    fitToCoordinates(newLocation)
  }
})

// </editor-fold>-------------------------------------------------------------

// <editor-fold desc="Listings">----------------------------------------------

const listingStore = useListingStore()
const listings = computed(() => listingStore.listings)
const { addMarkersToMap } = useListings()

watch(() => listings.value, (newListings) => {
  // Place markers on the map for each listing
  if (!map.map) return
  addMarkersToMap(map.map, newListings)

})

// </editor-fold>-------------------------------------------------------------

</script>

<template>
  <div class="relative w-full h-full">
    <FreeDrawer ref="drawerRef" direction="left" :showToggleButton="false" :initiallyOpen="false">
      <SideMenu />
    </FreeDrawer>

    <!-- Vendor Drawer Component -->
    <VendorDrawer />

    <!--  MapLibre with Open Free Map used to avoid usage fees - at least for now -->
    <MglMap mapKey="main" map-style="https://tiles.openfreemap.org/styles/liberty" :max-bounds="[[-180, 17], [-65, 72]]"
      :max-pitch="0" :max-zoom="15" min-zoom="5" :center="[-100, 40]" :zoom="5" :pitch="0" :bearing="0"
      :antialias="false" :preserve-drawing-buffer="false" />
  </div>
</template>
