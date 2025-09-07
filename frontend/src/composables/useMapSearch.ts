import { ref, watch } from 'vue'
import { useListings } from '@/composables/useListings'
import { useMapStore } from '@/stores/map'
import { v4 as uuid } from 'uuid'
import axios, { type AxiosResponse } from 'axios'
import { SearchTypes } from '@/types/map/search-types'
import { MapboxFeatureCollection, MapboxFeature } from '@/types/map/mapbox-types'
import { useListingStore } from '@/stores/listing'

// <editor-fold desc="Mapbox API Configuration">---------------------------------

const MAPBOX_API_KEY: string = localized.mapboxAPIKey
const mapboxBaseURL: string = 'https://api.mapbox.com/'
const searchAPIEndpoint: string = 'search/searchbox/v1/'

// </editor-fold>-----------------------------------------------------------------

export function useMapSearch(suggestDelay: number = 500) {


  const sessionToken = ref<string>(uuid())
  const { getCategories } = useListings()
  const mapStore = useMapStore()
  const listingStore = useListingStore()  

  // <editor-fold desc="Search Suggestions">------------------------------------------

  const query = ref<string>('')
  const suggestions = ref<Array<SearchTypes>>([])
  const selectedSuggestion = ref({})

  // #region Functions -----------------------------------------------------------------

  // Gets a certain type of Search Suggestion from api
  const getSuggestions = async (query: string, limit: number = 3, types: Array<string> = ['postcode', 'district', 'place', 'city', 'locality', 'neighborhood', 'address', 'poi']): Promise<Array<SearchTypes>> => {

    const typesString = types.join(', ')

    try {
      const response = await axios.get(mapboxBaseURL + searchAPIEndpoint + 'suggest', {
        params: {
          access_token: MAPBOX_API_KEY,
          session_token: sessionToken.value,
          q: query,
          limit: limit,
          proximity: 'ip',
          country: 'US',
          types: typesString
        }
      })

      if (response.data) {
        return response.data.suggestions ?? []
      }
    } catch (err) {
      console.error('Error fetching suggestions:', err)
    }
    return []
  }

  // Gets all suggestions for the current query
  const suggest = async (query: string): Promise<Array<SearchTypes>> => {

    const broadSuggestions = await getSuggestions(query, 3, ['city', 'locality', 'neighborhood', 'district'])
    const specificSuggestions = await getSuggestions(query, 3, ['address', 'poi'])

    let suggestions: Array<SearchTypes> = [...broadSuggestions, ...specificSuggestions]

    /* Remove duplicates
     * The set is made by comparing mapbox_ids - there are no normal ids on the suggestion results
     * The objects are then matched to their ids
     */

    const uniqueIds = Array.from(new Set(suggestions.map((s: SearchTypes) => s.mapbox_id)))

    // Map each id to its corresponding suggestion, filtering out any undefined values
    suggestions = uniqueIds
      .map((setElement: string) => suggestions.find(s => s.mapbox_id === setElement))
      .filter((suggestion): suggestion is SearchTypes => suggestion !== undefined)

    // Sort by order of priority
    const priorityOrder: Array<string> = ['city', 'district', 'postcode', 'locality', 'neighborhood', 'poi', 'address']
    suggestions = suggestions.sort((a, b) => {
      return priorityOrder.indexOf(a.feature_type) - priorityOrder.indexOf(b.feature_type)
    })

    return suggestions
  }

  // #endregion -----------------------------------------------------------------------

  let searchTimeout: NodeJS.Timeout | null = null // In browser, this is a number, in node it is a Node.js Timeout object

  // flag to track if suggestions should be suppressed
  const suppressSuggestions = ref(false)

  const preventSuggestions = () => {
    if (searchTimeout) clearTimeout(searchTimeout)
    suggestions.value = []
    // Set the flag to prevent new suggestions
    suppressSuggestions.value = true
    // Reset the flag after a brief delay to allow for any pending watch updates to complete
    setTimeout(() => {
      suppressSuggestions.value = false
    }, suggestDelay * 2)
  }

  watch(query, (newValue: string) => {
    if (searchTimeout) clearTimeout(searchTimeout)

    // Don't proceed if suggestions are suppressed or the query is empty
    if (suppressSuggestions.value || newValue.trim() === '') {
      suggestions.value = []
      return
    }

    searchTimeout = setTimeout(async () => {
      try {
        // Only fetch suggestions if not suppressed
        if (!suppressSuggestions.value) {
          const result = await suggest(newValue)
          suggestions.value = result || []
        }
      } catch (error) {
        console.error('Error fetching suggestions:', error)
        suggestions.value = []
      }
    }, suggestDelay)
  })

  // </editor-fold>-------------------------------------------------------------------

  // Retrieves the selected suggestion
  const retrieve = async (selection: SearchTypes): Promise<any> => {

    // Gatekeeper
    if (!selection) {
      console.error('No selection provided')
      return null
    } else if (!selection.mapbox_id) {
      console.error('No mapbox_id found in selection')
      return null
    }

    // Check if the selection is already in the store
    if (mapStore.location && mapStore.location.properties.mapbox_id === selection.mapbox_id) {
      console.log('Selection already in store:', mapStore.location)
      return mapStore.location
    }

    // Make a request to retrieve the selection
    const response: AxiosResponse<MapboxFeatureCollection> = await axios.get(
      mapboxBaseURL + searchAPIEndpoint + 'retrieve' + '/' + selection.mapbox_id,
      {
      params: {
        access_token: MAPBOX_API_KEY,
        session_token: sessionToken.value
      }
    })

    sessionToken.value = uuid()

    if (response?.data?.features && response.data.features.length > 0) {

      // Get the new location from the response
      const newLocation: MapboxFeature  = response.data.features[0] as MapboxFeature

      // Assign the new location to the store
      mapStore.location = newLocation

      // Use the new location to get listings
      listingStore.display = false
      await getCategories(newLocation)

      // Return the new location
      return newLocation

    } else {

      console.error('Invalid response data:', response?.data)

      return null
    }
  }

  return {
    query,
    suggestions,
    selectedSuggestion,
    retrieve,
    preventSuggestions
  }
}
