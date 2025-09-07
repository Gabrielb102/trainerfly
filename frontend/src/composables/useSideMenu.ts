import { computed, ref, watch } from "vue"
import { useListingStore } from "@/stores/listing"
import { useMapStore } from "@/stores/map"
import { useListings } from "@/composables/useListings"
import { Category } from "@/types/hivepress/category-types"
import { MapboxFeature } from "@/types/map/mapbox-types"
import { Listing } from "@/types/hivepress/listing-types"

export function useSideMenu() {

    const mapStore = useMapStore()
    const location = computed((): MapboxFeature => mapStore.location)

    // #region Listings -----------------------------------------------------------------

    const listingStore = useListingStore()
    const selectedCategory = computed((): Category | null => listingStore.selectedCategory)
    const categories = computed((): Array<Category> => listingStore.categories)
    const listings = computed((): Array<Listing> => listingStore.listings)
    const displayListings = computed((): boolean => listingStore.display)

    // #endregion -----------------------------------------------------------------------

    const searchQuery = ref<string>("")

    // #region Functions -----------------------------------------------------------------

    const clearAll = () => {
        mapStore.resetLocation()
        listingStore.listings = []
        listingStore.categories = []
        listingStore.display = false
    }

    const { getCategories, getListings } = useListings()

    const goBack = () => {

        listingStore.listings = []
        listingStore.categories = []
        
        // If listings are displayed, hide them, and navigate to the parent category
        if (listingStore.display) {
            listingStore.display = false
            listingStore.selectedListing = null
            getCategories(location.value, searchQuery.value, selectedCategory.value?.parent)
            if (selectedCategory.value?.parent === 0) {
                listingStore.selectedCategory = null
            }

        } else {
            getCategories(location.value, searchQuery.value, selectedCategory.value?.grandparent)
            listingStore.selectedCategory = null
        }
    }

    const showListings = () => {
        listingStore.categories = []
        listingStore.display = true
    }

    const hideListings = () => {
        listingStore.display = false
    }

    // #endregion -----------------------------------------------------------------------

    let searchTimeout: NodeJS.Timeout | null = null // In browser, this is a number, in node it is a Node.js Timeout object
    const searchDelay = 500

    const search = () => {
        getCategories(location.value, searchQuery.value, selectedCategory.value?.parent)
    }

    watch(searchQuery, (newValue: string) => {
        if (searchTimeout) clearTimeout(searchTimeout)
    
        searchTimeout = setTimeout(async () => {
          try {
            await search()
          } catch (error) {
            console.error('Error searching categories:', error)
          }
        }, searchDelay)
      })

    return {
        goBack,
        searchQuery,
        listings,
        categories,
        selectedCategory,
        displayListings,
        showListings,
        hideListings,
        clearAll,
        search
    }
}