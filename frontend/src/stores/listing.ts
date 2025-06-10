import { ref } from 'vue'
import { defineStore } from 'pinia'
import { Listing } from '@/types/hivepress/listing-types'

export const useListingStore = defineStore('listing', () => {
  const listings = ref<Array<Listing>>([])
  const selected = ref<Listing | null>(null)

  return { listings, selected }
})
