import { ref } from 'vue'
import { defineStore } from 'pinia'
import { Listing } from '@/types/hivepress/listing-types'
import { Category } from '@/types/hivepress/category-types'

export const useListingStore = defineStore('listing', () => {
  const listings = ref<Array<Listing>>([])
  const selectedListing = ref<Listing | null>(null)
  const selectedCategory = ref<Category | null>(null)
  const categories = ref<Array<Category>>([])
  const display = ref<boolean>(false)

  return { listings, selectedListing, selectedCategory, categories, display }
})
