<script setup lang="ts">
import {Listing} from '@/types/hivepress/listing-types'

defineProps<{
  listing: Listing
}>()
</script>

<template>
  <div class="w-full p-4 shadow-md rounded-xl">
    <div class="flex items-start gap-4">
      <UAvatar
        :src="listing.vendor.image || ''"
        :alt="listing.vendor.name"
        size="3xl"
        class="mt-4 flex-shrink-0"
      />
      <div class="flex flex-col flex-grow min-w-0">
        <h3 class="text-md font-extrabold text-primary truncate uppercase">
            {{ listing.title }}
        </h3>
        <h4 class="text-md text-primary truncate">
          {{ listing.vendor.name }}
        </h4>
        <UBadge v-if="listing.category" color="primary" size="sm" variant="outline"
                class="w-fit lowercase rounded-full" :label="listing.category"/>
        <span v-if="listing.price" class="mt-1 font-extrabold text-primary">
          ${{ listing.price }}
        </span>
      </div>
    </div>
  </div>
</template>
