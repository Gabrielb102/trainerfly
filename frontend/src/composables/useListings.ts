import { shallowRef, watch } from "vue";
import { useListingStore } from "@/stores/listing";
import { useAPI } from "@/composables/useAPI";
import { AxiosResponse } from "axios";
import { MapboxFeature } from "@/types/map/mapbox-types";
import { Listing } from "@/types/hivepress/listing-types";
import { Category } from "@/types/hivepress/category-types";
import { Marker } from 'maplibre-gl'
import mapMarkerIcon from '@/assets/images/map-marker.svg'
import type { Map } from 'maplibre-gl'


export function useListings() {

  const listingStore = useListingStore()
  const { get } = useAPI()

  const getListings = async (newLocation?: MapboxFeature, categoryId?: number, radius?: number, remoteOnly?: boolean): Promise<Array<Listing>> => {
    const latitude: number | null = newLocation?.geometry.coordinates[1] ?? null
    const longitude: number | null = newLocation?.geometry.coordinates[0] ?? null

    const res: AxiosResponse<Array<Listing>> = await get('listings', { latitude, longitude, radius, categoryId, remoteOnly })

    listingStore.listings = res.data

    return res.data
  }

  const getCategories = async (newLocation?: MapboxFeature, searchQuery?: string, categoryId?: number, radius?: number): Promise<Array<Category>> => {
    const latitude: number | null = newLocation?.geometry.coordinates[1] ?? null
    const longitude: number | null = newLocation?.geometry.coordinates[0] ?? null

    const res: AxiosResponse<Array<Category>> = await get('categories', { latitude, longitude, radius, searchQuery, categoryId })

    listingStore.categories = res.data

    return res.data
  }

  const markers = shallowRef<Array<Marker>>([])

  const makeMarkerElement = (listing: Listing, selected: boolean) => {
    const markerElement = document.createElement('div')
    markerElement.className = 'custom-marker'
  
    markerElement.innerHTML = `
      <div class="trainerfly-listing-marker-container relative inline-flex items-center">
        <img src="${mapMarkerIcon}"
            alt="Listing marker"
            class="trainerfly-listing-marker peer size-10 shrink-0 cursor-pointer transition-transform duration-200 hover:scale-110"
            data-listing-id="${listing.id}" />
        <div id="trainerfly-listing-marker-tooltip-${listing.id}" class="absolute left-full top-1/2 -translate-y-1/2 ml-2 ${selected ? 'opacity-100 visible scale-100' : 'opacity-0 invisible scale-95'} peer-hover:opacity-100 peer-hover:visible peer-hover:scale-100 transition-all duration-150 ease-out origin-left transform-gpu bg-white rounded-sm py-1 px-2 shadow-md whitespace-nowrap z-10">
          <b class="trainerfly-listing-marker-tooltip-title">${listing.title}</b>
          <p class="trainerfly-listing-marker-tooltip-description">${listing.vendor.name}</p>
        </div>
      </div>
      `
  
    markerElement.addEventListener('click', () => {
      listingStore.selectedListing = listing
    })
  
    return markerElement
  }

  const addMarkersToMap = (map: Map, listings: Array<Listing>): void => {
    // Remove existing markers from the map first
    markers.value.forEach((m: Marker) => m.remove())
    markers.value = []

    listings.forEach((listing: Listing) => {
      // Skip listings without valid coordinates
      if (!listing.latitude || !listing.longitude) {
        return
      }

      const selected = listing.id === listingStore.selectedListing?.id

      // Create and add marker
      const marker = new Marker({ element: makeMarkerElement(listing, selected) })
        .setLngLat([listing.longitude, listing.latitude])
        .addTo(map)
        markers.value.push(marker)
      })
  }

  const removeMarkersFromMap = (): void => {
    markers.value.forEach((marker) => marker.remove())
    markers.value = []
  }

  watch(() => listingStore.selectedListing, (newListing, oldListing) => {
    const currentSelectedMarkerTooltip = document.getElementById(`trainerfly-listing-marker-tooltip-${oldListing?.id}`)
    if (currentSelectedMarkerTooltip) {
      currentSelectedMarkerTooltip?.classList.add('opacity-0', 'invisible', 'scale-95')
    }
    const newSelectedMarkerTooltip = document.getElementById(`trainerfly-listing-marker-tooltip-${newListing?.id}`)
    if (newSelectedMarkerTooltip) {
      newSelectedMarkerTooltip?.classList.remove('opacity-0', 'invisible', 'scale-95')
    }
  })

  return {
    getListings,
    getCategories,
    addMarkersToMap,
    removeMarkersFromMap
  }
}
