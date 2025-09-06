<script setup lang="ts">
import ListingCardSkeleton from '@/components/loading-ui/ListingCardSkeleton.vue';
import CategoryCardSkeleton from '@/components/loading-ui/CategoryCardSkeleton.vue';

defineProps<{
  listing: boolean
}>()

</script>

<template>
  <div v-if="listing" class="flex flex-col gap-4 items-center justify-center">
    <ListingCardSkeleton v-for="n in 5" :key="`loading-listing-${n}`" :dark="false"/>
  </div>
  <div v-else class="grid grid-cols-2 gap-4">
    <CategoryCardSkeleton v-for="n in 5" :key="`loading-category-${n}`" :dark="false"/>
  </div>
</template>