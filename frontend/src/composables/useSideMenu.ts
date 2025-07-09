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

    // Listings
    const listingStore = useListingStore()
    const selectedCategory = computed((): Category | null => listingStore.selectedCategory)
    const categories = computed((): Array<Category> => listingStore.categories)
    const listings = computed((): Array<Listing> => listingStore.listings)
    const displayListings = computed((): boolean => listingStore.display)

    const searchQuery = ref<string>("")

    // Functions
    const { getCategories } = useListings()
    const goBack = () => {

        listingStore.listings = []
        // If listings are displayed, hide them, and navigate to the parent category
        if (listingStore.display) {
            listingStore.display = false
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


    return {
        goBack,
        searchQuery,
        listings,
        categories,
        selectedCategory,
        displayListings,
        showListings,
        hideListings,
    }
}