<script setup lang="ts">
import { useSideMenu } from '@/composables/useSideMenu'
import { watch } from 'vue'

// Made into a composable so that Child components, as well as the map, can use the same composable
const { goBack, displayListings, categories, listings, selectedCategory, searchQuery, showListings } = useSideMenu()

watch(selectedCategory, () => {
  console.log('selectedCategory', selectedCategory.value);
}, { immediate: true })

</script>

<template>
  <div class="flex flex-col w-full h-full px-4 gap-2 items-center">
    <div class="w-full text-center">
      <UButton v-if="displayListings || selectedCategory && selectedCategory.id !== 0" icon="fa6-solid:arrow-left" label="go back" variant="outline" color="primary" class="w-full" @click="goBack" />
      <span v-else class="uppercase font-bold">What do I want to learn?</span>
    </div>
    <UInput variant="soft" color="neutral" class="w-full" v-model="searchQuery" />
    <UButton color="primary" label="Search" class="w-full" />
    <div class="flex flex-col gap-2 w-full">
      <hr>
      <span class="w-full text-center text-sm uppercase font-bold">Results</span>
      <div v-if="!displayListings" class="grid grid-cols-2 gap-4">
        <CategoryCard v-for="c in categories" :key="c.id" :category="c" @selectCategory="showListings" />
      </div>
      <div v-if="displayListings" class="flex flex-col gap-2">
        <ListingCard v-for="l in listings" :key="l.id" :listing="l"
          class="cursor-pointer hover:bg-gray-100 transition-colors" />
      </div>
    </div>
  </div>
</template>
