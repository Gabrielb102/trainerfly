<script setup lang="ts">
import { onMounted } from 'vue';
import { Category } from '@/types/hivepress/category-types'
import { useListings } from '@/composables/useListings';
import { useMapStore } from '@/stores/map';
import { useListingStore } from '@/stores/listing';
import { useSideMenu } from '@/composables/useSideMenu';

const props = defineProps<{
    category: Category,
    dark?: boolean,
    remoteOnly?: boolean
}>()

// Handle Clicks
const { getListings, getCategories } = useListings()
const { showListings } = useSideMenu()
const listingStore = useListingStore()
const mapStore = useMapStore()

const emit = defineEmits<{
    (e: 'selectCategory'): void
}>()

const selectCategory = () => {

    // Get Listings for Category
    getListings(mapStore.location, props.category.id, undefined, props.remoteOnly)

    // Set Selected Category in store
    emit('selectCategory')
    listingStore.selectedCategory = props.category

    // Show Listings
    showListings()
}

const viewChildCategories = () => {

    // Set Selected Category in store
    emit('selectCategory')
    listingStore.selectedCategory = props.category

    // Get Child Categories for Category
    getCategories(mapStore.location, undefined, props.category.id)
}

onMounted(() => {
    console.log(`${props.category.name} mounted with icon ${props.category.icon}`)
})

</script>

<template>
    <div :class="['w-full aspect-square min-h-20 flex flex-col items-center justify-center gap-2 py-4 px-2 aspect-square shadow-md/40 rounded-xl cursor-pointer', { 'bg-gray-100 hover:bg-gray-200 transition-colors': !dark, 'bg-white hover:bg-gray-300 transition-colors': dark }]"
        @click="selectCategory">
        <div class="w-full h-full flex items-center justify-center">
            <UIcon :name="category.icon ? `fa6-solid:${category.icon}` : 'fa6-solid:circle-question'"
                class="size-10 text-gray-400" />
        </div>
        <div class="w-full h-fit flex items-center justify-center gap-0">
            <span class="h-fit w-full text-center text-md leading-5 font-bold uppercase text-black line-clamp-2">
                {{ category.name }}
            </span>
            <UButton v-if="category.has_children" @click.stop="viewChildCategories"
                class="rounded-full w-fit h-fit cursor-pointer" icon="fa6-solid:angle-right" variant="ghost"
                size="sm" />
        </div>
    </div>
</template>