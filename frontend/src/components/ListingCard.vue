<script setup lang="ts">
import { computed } from 'vue'
import { Listing } from '@/types/hivepress/listing-types'
import { useListingStore } from '@/stores/listing'


const props = defineProps<{
  listing: Listing
}>()

const listingStore = useListingStore()

const selectListing = () => {
  listingStore.selectedListing = props.listing
}

const selected = computed((): boolean => listingStore.selectedListing?.id === props.listing.id)
</script>

<template>
  <div :class="['w-full h-fit min-h-20 p-4 shadow-md/40 rounded-xl flex items-start gap-4 transition-all duration-300 bg-white hover:bg-gray-100 cursor-pointer', {'ring-1 ring-primary': selected}]" @click="selectListing">
    <img :src="listing.vendor.image || ''" :alt="listing.vendor.name"
      class="mt-1 flex-shrink-0 h-14 w-14 rounded-full object-cover" />
    <div class="flex flex-col flex-grow min-w-0">
      <h3 class="text-sm font-bold text-primary truncate uppercase w-full">
        {{ listing.title }}
      </h3>
      <h4 class="text-sm font-normal text-primary truncate w-full">
        {{ listing.vendor.name }}
      </h4>
      <div class="flex justify-start items-center gap-1 mt-1">
        <UBadge v-for="d of listing.vendor.descriptors" color="primary" size="sm" variant="outline"
          class="w-fit lowercase ring rounded-full px-2.5 py-1.5" :label="d" />
      </div>
      <span v-if="listing.price" class="mt-1 font-extrabold text-primary">
        ${{ listing.price }}
      </span>
    </div>
  </div>
</template>
