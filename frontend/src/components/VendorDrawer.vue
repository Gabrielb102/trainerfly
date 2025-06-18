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

// <editor-fold desc="Links">--------------------------------

const contactVendor = () => {
  console.log('Contact vendor functionality to be implemented')
}

const goToListing = () => {
  if (selectedListing.value) {
    window.location.href = selectedListing.value.url;
  }
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
    <div v-if="selectedListing" class="flex h-full w-full justify-start">
      <div class="ml-96 w-[1000px] p-4 pt-6 h-full flex flex-col gap-4 bg-white rounded-t-xl">
        <!-- Header: Vendor Image and Info -->
        <div class="flex flex-grow items-start gap-4">
          <img
            :src="selectedListing.vendor.image || 'https://via.placeholder.com/150'"
            alt="Vendor"
            class="flex-none w-20 h-20 rounded-lg object-cover"
          />
          <div id="info" class="flex flex-col flex-grow gap-1">
            <div class="flex gap-6 items-center">
              <h2 class="text-xl font-bold">{{ selectedListing.vendor.name }}</h2>
              <div class="flex gap-2 items-center justify-start">
                <UBadge
                  variant="outline"
                  class="rounded-full"
                  :label="selectedListing.category ?? ''"
                />
              </div>
            </div>
            <h3 class="text-lg font-normal">{{ selectedListing.title }}</h3>
            <p class="text-sm text-gray-600">
              {{ 'No Description' }}
            </p>
          </div>
          <div id="buttons" class="flex flex-none flex-col gap-2 w-64">
            <UButton color="primary" class="w-full" @click="contactVendor">Contact</UButton>
            <UButton color="primary" variant="outline" class="w-full" @click="goToListing">More Info</UButton>
          </div>
        </div>

        <!-- Recent Review (placeholder) -->
        <div class="flex flex-none h-18">
          <div class="bg-blue-100 p-4 rounded-lg w-full">
            <h4 class="font-bold">Review Title</h4>
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
