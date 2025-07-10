<script setup lang="js">
import { onMounted, watch } from 'vue'
import RemoteSearch from '@/components/RemoteSearch.vue'
import RemoteSideMenu from '@/components/RemoteSideMenu.vue'
import Header from "@/components/Header.vue";
import { useSideMenu } from '@/composables/useSideMenu';
import { useLoading } from '@/composables/useLoading';
import { onBeforeEnter, onEnter } from '@/helpers/animation';
import { useListings } from '@/composables/useListings';

const { displayListings, listings, categories } = useSideMenu()
const { loading, startLoading, loadWatcherCallback } = useLoading()
const { getCategories } = useListings()

onMounted(() => {
    getCategories() // Get the categories with no location data
})

// Loading Triggers
watch(categories, loadWatcherCallback)
watch(listings, loadWatcherCallback)

</script>

<template>
    <div class="flex flex-col size-full">
        <Header>
            <template #center>
                <RemoteSearch />
            </template>
        </Header>
        <div class="flex w-full h-full gap-0">
            <RemoteSideMenu />
            <TransitionGroup tag="div" :class="['w-full h-full grid justify-center items-start bg-gray-100', {'grid-cols-6 gap-4': !displayListings, 'grid-cols-3 gap-6': displayListings}]"
            @before-enter="onBeforeEnter" @enter="onEnter">
                <ListingCard v-if="displayListings" v-for="listing in listings" :key="listing.id" class="w-full h-full" :listing="listing"/>
                <CategoryCard v-else v-for="category in categories" :key="category.id" :category="category" @selectCategory="startLoading"/>
            </TransitionGroup>
        </div>
    </div>
</template>