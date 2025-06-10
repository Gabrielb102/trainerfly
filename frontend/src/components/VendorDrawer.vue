<script setup lang="ts">
import { computed, ref, watch } from 'vue'
import { useListingStore } from '@/stores/listing'
import FreeDrawer from '@/components/FreeDrawer.vue'

const listingStore = useListingStore()
const selectedListing = computed(() => listingStore.selected)

// Drawer reference and state
type FreeDrawerType = InstanceType<typeof FreeDrawer>
const drawerRef = ref<FreeDrawerType>()

// Watch for changes in the selected listing
watch(
  () => selectedListing.value,
  (newListing) => {
    if (newListing && drawerRef.value) {
      drawerRef.value.isOpen = true
    } else if (drawerRef.value) {
      drawerRef.value.isOpen = false
    }
  },
)

// Placeholder for contact vendor function
const contactVendor = () => {
  console.log('Contact vendor functionality to be implemented')
}

// Placeholder for view vendor page function
const viewVendorPage = () => {
  console.log('View vendor page functionality to be implemented')
}
</script>

<template>
  <FreeDrawer
    ref="drawerRef"
    direction="bottom"
    :showToggleButton="false"
    :initiallyOpen="false"
    :transparent="true"
  >
    <!-- When Opened -->
    <div
      v-if="selectedListing"
      class="pt-2 px-4 pb-4 flex flex-col gap-4"
      style="margin-left: auto; width: calc(100% - 96rem)"
    >
      <!-- Header: Vendor Image and Info -->
      <div class="flex items-center gap-4 pb-3 rounded-t-xl bg-white">
        <img
          :src="selectedListing.vendor.image || 'https://via.placeholder.com/150'"
          alt="Vendor"
          class="w-20 h-20 rounded-lg object-cover"
        />
        <div id="info" class="flex flex-col gap-1">
          <div class="flex gap-6">
            <h2 class="text-xl font-bold">{{ selectedListing.vendor.name }}</h2>
            <UBadge :label="selectedListing.category ?? ''" />
          </div>
          <h3 class="text-lg font-normal">{{ selectedListing.title }}</h3>
          <p class="text-sm text-gray-600">
            {{ 'No Description' }}
          </p>
        </div>
        <div id="buttons" class="flex flex-col gap-2">
          <UButton color="primary" class="px-6" @click="contactVendor">Contact</UButton>
          <UButton color="secondary" class="px-6" @click="viewVendorPage">More Info</UButton>
        </div>
      </div>

      <div class="grid grid-cols-2 gap-4">
        <!-- Recent Review (placeholder) -->
        <div>
          <h4 class="font-bold">Review Title</h4>
          <div class="bg-gray-100 p-2 rounded mt-1">
            <p class="text-md">
              "Most recent review! I made this review so recently that it is the first one you see."
            </p>
          </div>
        </div>
      </div>
    </div>
    <div v-else class="h-0 w-full"></div>
  </FreeDrawer>
</template>
