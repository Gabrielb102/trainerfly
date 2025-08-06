<script setup lang="ts">
import CategoryCardSkeleton from '@/components/loading-ui/CategoryCardSkeleton.vue';
import ListingCardSkeleton from '@/components/loading-ui/ListingCardSkeleton.vue';

defineProps<{
    isListings: boolean
}>()

</script>

<template>
    <div
        :class="['md:w-[600px] lg:w-[800px] xl:w-[1000px] h-fit grid justify-center items-start md:p-4 lg:px-10 lg:py-6', { 'md:grid-cols-4 lg:grid-cols-5 xl:grid-cols-6 gap-4': !isListings, 'md:grid-cols-2 lg:grid-cols-3 gap-6': isListings }]">
        <!-- Categories Skeleton Grid (18 cards) -->
        <CategoryCardSkeleton v-if="!isListings" v-for="i in 11" :key="`loading-category-${i}`" :dark="true"/>

        <!-- Listings Skeleton Grid (8 cards) -->
        <ListingCardSkeleton v-else v-for="i in 8" :key="`loading-listing-${i}`" :dark="true"/>
    </div>
</template>