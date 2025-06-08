import { ref } from 'vue'
import { defineStore } from 'pinia'
import { Listing } from '@/types/hivepress/listing-types'

export const useListingStore = defineStore('listing', () => {
  const listings = ref<Array<Listing>>([])

  return { listings }
})
