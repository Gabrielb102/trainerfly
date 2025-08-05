<script setup lang="js">
import { onMounted, watch } from 'vue'
import { useSideMenu } from '@/composables/useSideMenu';
import { useLoading } from '@/composables/useLoading';
import { onBeforeEnter, onEnter } from '@/helpers/animation';
import { useListings } from '@/composables/useListings';
import RemoteSearch from '@/components/RemoteSearch.vue'
import RemoteSideMenu from '@/components/RemoteSideMenu.vue'
import Header from "@/components/Header.vue";
import RemoteScreenLoadingVisual from '@/components/RemoteScreenLoadingVisual.vue';

const { displayListings, listings, categories, clearAll } = useSideMenu()
const { loading, startLoading, loadWatcherCallback } = useLoading()
const { getCategories } = useListings()

onMounted(() => {
    getCategories(null, null, null, null, true) // Get the categories with no location data
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
        <div class="flex w-full h-full gap-0 justify-center items-start">
            <RemoteSideMenu :onGoBack="startLoading" />
            <div class="w-full h-full flex justify-center items-start bg-gray-200">
                <RemoteScreenLoadingVisual v-if="loading" :isListings="displayListings" />
                <TransitionGroup v-else tag="div" appear
                    :class="['md:w-[600px] lg:w-[800px] xl:w-[1000px] h-fit grid justify-center items-start md:p-4 lg:px-10 lg:py-6', { 'md:grid-cols-4 lg:grid-cols-5 xl:grid-cols-6 gap-4': !displayListings, 'md:grid-cols-2 lg:grid-cols-3 gap-4': displayListings }]"
                    @before-enter="onBeforeEnter" @enter="onEnter">
                    <ListingCard v-for="(l, i) in listings" :key="l.id" class="w-full h-full" :listing="l"
                        :data-index="i" />
                    <CategoryCard v-for="(c, i) in categories" :key="c.id" :category="c" :data-index="i" :dark="true"
                        :remoteOnly="true" @selectCategory="startLoading" />
                    <NoListingsMessage class="col-span-full" v-if="displayListings && listings.length === 0"
                        :categoryName="selectedCategory?.name || ''" />
                </TransitionGroup>
            </div>
            <VendorDrawer />
        </div>
    </div>
</template>