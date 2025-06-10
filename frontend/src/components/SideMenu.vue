<script setup lang="ts">
import {computed} from 'vue'
import {useListingStore} from '@/stores/listing'
import {Listing} from '@/types/hivepress/listing-types'

const listingStore = useListingStore()
const listings = computed((): Array<Listing> => listingStore.listings)

// Function to handle listing selection
const selectListing = (listing: Listing) => {
  listingStore.selected = listing
}
</script>

<template>
  <div class="flex flex-col w-full h-full px-4 gap-2 items-center">
    <div class="w-full px-10 text-center">
      <span class="uppercase font-bold">What do I want to learn?</span>
    </div>
    <UInput variant="soft" color="neutral" class="w-full"/>
    <UButton color="primary" label="Search" class="w-full"/>
    <div class="flex flex-col gap-2 w-full">
      <hr>
      <span class="w-full text-center text-sm uppercase font-bold">Results</span>
      <ListingCard
        v-for="l in listings"
        :key="l.id"
        :listing="l"
        @click="selectListing(l)"
        class="cursor-pointer hover:bg-gray-100 transition-colors"
      />
    </div>
  </div>
</template>
