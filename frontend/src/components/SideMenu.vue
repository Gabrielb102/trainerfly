<script setup lang="ts">
import { computed, ref, watch } from 'vue'
import { useListingStore } from '@/stores/listing'
import { Listing } from '@/types/hivepress/listing-types'
import { Category } from '@/types/hivepress/category-types'

const listingStore = useListingStore()
const listings = computed((): Array<Listing> => listingStore.listings)
const categories = computed((): Array<Category> => listingStore.categories)

const showListings = ref<boolean>(false)
watch(categories, () => {
  showListings.value = false
})

// Function to handle listing selection
const selectListing = (listing: Listing) => {
  listingStore.selectedListing = listing
}

</script>

<template>
  <div class="flex flex-col w-full h-full px-4 gap-2 items-center">
    <div class="w-full px-10 text-center">
      <span class="uppercase font-bold">What do I want to learn?</span>
    </div>
    <UInput variant="soft" color="neutral" class="w-full" />
    <UButton color="primary" label="Search" class="w-full" />
    <div class="flex flex-col gap-2 w-full">
      <hr>
      <span class="w-full text-center text-sm uppercase font-bold">Results</span>
      <div v-if="!showListings" class="grid grid-cols-2 gap-4">
        <CategoryCard v-for="c in categories" :key="c.id" :category="c" @selectCategory="showListings = true" />
      </div>
      <div v-if="showListings" class="flex flex-col gap-2">
        <ListingCard v-for="l in listings" :key="l.id" :listing="l" @click="selectListing(l)"
          class="cursor-pointer hover:bg-gray-100 transition-colors" />
      </div>
    </div>
  </div>
</template>
