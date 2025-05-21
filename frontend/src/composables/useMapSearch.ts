import { ref, watch } from 'vue'
import { useMapStore } from '@/stores/map.js'
import { v4 as uuid } from 'uuid'
import axios, { type AxiosResponse } from 'axios'
import { SearchSuggestion } from '@/types/map/SearchSuggestion'
import { MapboxFeatureCollection } from '@/types/map/MapboxFeatures'


const MAPBOX_API_KEY: string = localized.mapboxAPIKey
const mapboxBaseURL: string = 'https://api.mapbox.com/'
const searchAPIURL: string = 'search/searchbox/v1/'

export function useMapSearch(suggestDelay: number = 500) {

  const sessionToken = ref<string>(uuid())
  const mapStore = useMapStore()

  // <editor-fold desc="Search Suggestions">------------------------------------------

  const query = ref<string>('')
  const suggestions = ref<Array<SearchSuggestion>>([])
  const selectedSuggestion = ref({})

  // <editor-fold desc="Functions">------------------------------------------

  // Gets a certain type of Search Suggestion from api
  const getSuggestions = async (query: string, limit: number = 3, types: Array<string> = ['postcode', 'district', 'place', 'city', 'locality', 'neighborhood', 'address', 'poi']): Promise<Array<SearchSuggestion>> => {

    const typesString = types.join(', ')

    try {
      const response = await axios.get(mapboxBaseURL + searchAPIURL + 'suggest', {
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
  const suggest = async (query: string): Promise<Array<SearchSuggestion>> => {

    // Get multiple types of suggestions
    const broadSuggestions = await getSuggestions(query, 3, ['city', 'locality', 'neighborhood', 'district'])
    const specificSuggestions = await getSuggestions(query, 3, ['address', 'poi'])

    // Combine the suggestions
    let suggestions: Array<SearchSuggestion> = [...broadSuggestions, ...specificSuggestions]

    /* Remove duplicates
     * The set is made by comparing mapbox_ids - there are no normal ids on the suggestion results
     * The objects are then matched to their ids
     */

    // Create a set of unique mapbox_ids
    const uniqueIds = Array.from(new Set(suggestions.map((s: SearchSuggestion) => s.mapbox_id)))

    // Map each id to its corresponding suggestion, filtering out any undefined values
    suggestions = uniqueIds
      .map((setElement: string) => suggestions.find(s => s.mapbox_id === setElement))
      .filter((suggestion): suggestion is SearchSuggestion => suggestion !== undefined)

    // Sort by order of priority
    const priorityOrder: Array<string> = ['city', 'district', 'postcode', 'locality', 'neighborhood', 'poi', 'address']
    suggestions = suggestions.sort((a, b) => {
      return priorityOrder.indexOf(a.feature_type) - priorityOrder.indexOf(b.feature_type)
    })

    return suggestions
  }

  // </editor-fold>-----------------------------------------------------------

  let searchTimeout: number | null = null // In browser, this is a number, in node it is a Node.js Timeout object

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
  const retrieve = async (selection: SearchSuggestion): Promise<any> => {

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
    const response: AxiosResponse<MapboxFeatureCollection> = await axios.get(mapboxBaseURL + searchAPIURL + 'retrieve' + '/' + selection.mapbox_id, {
      params: {
        access_token: MAPBOX_API_KEY,
        session_token: sessionToken.value
      }
    })
    sessionToken.value = uuid()

    if (response?.data?.features && response.data.features.length > 0) {
      // set the location in the store
      const newLocation = response.data.features[0]

      console.log("Setting location in store:", newLocation);
      mapStore.location = newLocation
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
