<script setup lang="ts">
import { computed, ref, watch } from 'vue'
import { useRouter } from 'vue-router'
import { useListingStore } from '@/stores/listing'
import FreeDrawer from '@/components/FreeDrawer.vue'

const listingStore = useListingStore()
const listing = computed(() => listingStore.selectedListing)

// Drawer reference and state
type FreeDrawerType = InstanceType<typeof FreeDrawer>
const drawerRef = ref<FreeDrawerType>()

// Watch for changes in the selected listing
watch(
  () => listing.value,
  (newListing) => {
    if (newListing && drawerRef.value) {
      drawerRef.value.isOpen = true
    } else if (drawerRef.value) {
      drawerRef.value.isOpen = false
    }
  },
)

// <editor-fold desc="Links">--------------------------------
const router = useRouter()
const contactVendor = () => {
  console.log('Contact vendor functionality to be implemented')
}

const goToListing = () => {
  if (listing.value) {
    // router.push(`/listing/${listing.value.id}`)
    window.location.href = listing.value.url;
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
    <div v-if="listing" class="flex h-full w-full justify-start">
      <div class="ml-96 w-[1000px] p-4 pt-6 h-full flex flex-col gap-4 bg-white rounded-t-xl">
        <!-- Header: Vendor Image and Info -->
        <div class="flex flex-grow items-start gap-4">
          <img :src="listing.vendor.image || 'https://via.placeholder.com/150'" alt="Vendor" class="flex-none h-30 aspect-square rounded-lg object-cover" />
          <div id="info" class="flex flex-col flex-grow gap-1">
            <div class="flex gap-6 items-center">
              <h2 class="text-xl font-bold">{{ listing.vendor.name }}</h2>
              <div class="flex gap-2 items-center justify-start">
                <UBadge v-for="d of listing.vendor.descriptors" class="rounded-full px-3 py-2" :label="d ?? ''" variant="outline" />
              </div>
            </div>
            <h3 class="text-lg font-normal">{{ listing.title }}</h3>
            <p class="text-sm text-gray-600">
              {{ listing.description ?? 'No description available' }}
            </p>
          </div>
          <div id="buttons" class="flex flex-none flex-col gap-2 w-64">
            <UButton color="primary" class="w-full" @click="contactVendor">Contact</UButton>
            <UButton color="primary" variant="outline" class="w-full" @click="goToListing">More Info</UButton>
          </div>
        </div>

        <!-- Recent Review -->
        <div class="flex flex-none h-fit">
          <div v-if="listing.reviews.length > 0" class="bg-blue-100 p-4 rounded-lg w-full h-fit min-h-20r">
            <h4 class="font-bold">Recent Review</h4>
            <p class="text-md">
              {{ listing.reviews[0].text }}
            </p>
          </div>
        </div>
      </div>
    </div>
    <div v-else class="h-0 w-full"></div>
  </FreeDrawer>
</template>
