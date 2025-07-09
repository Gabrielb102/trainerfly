<script setup lang="ts">
import { ref, watch } from 'vue'
import { useSideMenu } from '@/composables/useSideMenu'
import SideMenuLoadingVisual from './SideMenuLoadingVisual.vue'
import gsap from 'gsap'

// Made into a composable so that Child components, as well as the map, can use the same composable
const { goBack, displayListings, categories, listings, selectedCategory, searchQuery, showListings } = useSideMenu()

//#region Loading State

const loading = ref(true)

const startLoading = () => {
  loading.value = true
}

const stopLoading = () => {
  loading.value = false
}

const loadWatcherCallback = (newVal: Array<any>, oldVal: Array<any>) => {
  if (newVal.length > 0) {
    stopLoading()
  } else if (newVal.length === 0 && oldVal.length > 0) {
    startLoading()
  } else if (newVal.length === 0 && oldVal.length === 0) {
    stopLoading()
  }
}

watch(categories, loadWatcherCallback)
watch(listings, loadWatcherCallback)

//#endregion

//#region Animation Functions

function onBeforeEnter(el: Element) {
  const htmlEl = el as HTMLElement
  htmlEl.style.opacity = '0'
}

function onEnter(el: Element, done: () => void) {
  const htmlEl = el as HTMLElement
  gsap.to(el, {
    opacity: 1,
    delay: Number(htmlEl?.dataset?.index) * 0.05,
    onComplete: done
  })
}

//#endregion

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
          <ListingCard v-for="(l, i) in listings" :key="l.id" :data-index="i" :listing="l" @selectListing="startLoading"
            class="cursor-pointer hover:bg-gray-100 transition-colors" />
        </TransitionGroup>
      </template>
    </div>
  </div>
</template>
