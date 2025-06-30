import {useListingStore} from "@/stores/listing";
import {useAPI} from "@/composables/useAPI";
import {AxiosResponse} from "axios";
import {MapboxFeature} from "@/types/map/mapbox-types";
import {Listing} from "@/types/hivepress/listing-types";
import {Category} from "@/types/hivepress/category-types";
import type {Map} from 'maplibre-gl'
import {Marker} from 'maplibre-gl'
import mapMarkerIcon from '@/assets/images/map-marker.svg'

const makeMarkerElement = () => {
  const markerElement = document.createElement('div')
  markerElement.className = 'custom-marker'

// Option 1: Use an image
  markerElement.innerHTML = `
      <img src="${mapMarkerIcon}"
           alt="Listing marker"
           style="width: 30px; height: 30px;" />
    `

  return markerElement
}

export function useListings() {

  const listingStore = useListingStore()
  const {get} = useAPI()

  const getListings = async (newLocation?: MapboxFeature, radius?: number, categoryId?: number): Promise<Array<Listing>> => {
    const latitude: number | null = newLocation?.geometry.coordinates[1] ?? null
    const longitude: number | null = newLocation?.geometry.coordinates[0] ?? null

    const res: AxiosResponse<Array<Listing>> = await get('listings', {latitude, longitude, radius, categoryId})

    listingStore.listings = res.data

    return res.data
  }

  const getCategories = async (newLocation?: MapboxFeature, radius?: number, searchQuery?: string, categoryId?: number): Promise<Array<Category>> => {
    const latitude: number | null = newLocation?.geometry.coordinates[1] ?? null
    const longitude: number | null = newLocation?.geometry.coordinates[0] ?? null

    const res: AxiosResponse<Array<Category>> = await get('categories', {latitude, longitude, radius, searchQuery, categoryId})

    listingStore.categories = res.data

    return res.data
  }

  const addMarkersToMap = (map: Map, listings: Array<Listing>): Array<Marker> => {
    const markers: Array<Marker> = []

    listings.forEach((listing: Listing) => {
      // Skip listings without valid coordinates
      if (!listing.latitude || !listing.longitude) {
        return
      }

      // Create and add marker
      const marker: Marker = new Marker({element: makeMarkerElement()})
        .setLngLat([listing.longitude, listing.latitude])
        .addTo(map)

      markers.push(marker)
    })

    return markers
  }

  const removeMarkersFromMap = (markers: Marker[]): void => {
    markers.forEach(marker => marker.remove())
  }


  return {
    getListings,
    getCategories,
    addMarkersToMap,
    removeMarkersFromMap
  }
}
