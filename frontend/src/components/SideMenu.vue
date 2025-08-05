<script setup lang="ts">
import { onMounted, watch } from 'vue'
import { useSideMenu } from '@/composables/useSideMenu'
import { useLoading } from '@/composables/useLoading'
import SideMenuLoadingVisual from './SideMenuLoadingVisual.vue'
import { onBeforeEnter, onEnter } from '@/helpers/animation'

// Made into a composable so that Child components, as well as the map, can use the same composable
const { goBack, displayListings, categories, listings, selectedCategory, searchQuery, clearAll } = useSideMenu()
const { loading, startLoading, loadWatcherCallback } = useLoading()

// Loading Triggers
watch(categories, loadWatcherCallback, { immediate: true })
watch(listings, loadWatcherCallback)

</script>

<template>
  <div class="flex flex-col w-full h-full px-4 gap-2 items-center">
    <div class="w-full text-center transition-all duration-300">
      <UButton v-if="selectedCategory && selectedCategory.id !== 0" icon="fa6-solid:arrow-left"
        label="go back" variant="outline" color="primary" class="w-full" @click="goBack" />
      <span v-else class="uppercase font-bold">What do I want to learn?</span>
    </div>
    <UInput variant="soft" color="neutral" class="w-full" v-model="searchQuery" />
    <UButton color="primary" label="Search" class="w-full" />
    <div class="flex flex-col gap-2 w-full h-full">
      <hr>
      <span class="w-full text-center text-sm uppercase font-bold">Results</span>
      <SideMenuLoadingVisual v-if="loading" :listing="displayListings" />
      <template v-else>
        <TransitionGroup tag="div" appear @before-enter="onBeforeEnter" @enter="onEnter"
        :class="{ 'grid grid-cols-2 gap-4': !displayListings, 'flex flex-col gap-4': displayListings }">
          <CategoryCard v-for="(c, i) in categories" :key="c.id" :data-index="i" :category="c" @selectCategory="startLoading" />
          <ListingCard v-for="(l, i) in listings" :key="l.id" :data-index="i" :listing="l"
            class="cursor-pointer hover:bg-gray-100 transition-colors" />
          <NoListingsMessage v-if="displayListings && listings.length === 0" :categoryName="selectedCategory?.name || ''" />
        </TransitionGroup>
      </template>
    </div>
  </div>
</template>
