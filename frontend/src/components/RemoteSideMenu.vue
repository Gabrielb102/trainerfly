<script setup lang="ts">
import { useSideMenu } from '@/composables/useSideMenu'

// Made into a composable so that Child components, as well as the map, can use the same composable
const { goBack, selectedCategory, searchQuery} = useSideMenu()

</script>

<template>
  <div class="flex flex-col md:w-80 lg:w-96 h-full px-4 gap-2 items-center bg-white z-50">
    <div class="w-full text-center transition-all duration-300">
      <UButton v-if="selectedCategory && selectedCategory.id !== 0" icon="fa6-solid:arrow-left"
        label="go back" variant="outline" color="primary" class="w-full" @click="goBack" />
      <span v-else class="uppercase font-bold">What do I want to learn?</span>
    </div>
    <UInput variant="soft" color="neutral" class="w-full" v-model="searchQuery" />
    <UButton color="primary" label="Search" class="w-full" />
  </div>
</template>
 