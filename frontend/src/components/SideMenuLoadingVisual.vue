<script setup lang="ts">

defineProps<{
  listing: boolean
}>()

</script>

<template>
  <div v-if="listing" class="flex flex-col gap-4 items-center justify-center">
    <USkeleton v-for="n in Array.from({ length: 5 })" class="w-full h-30 animate-pulse rounded-xl bg-gray-100" />
  </div>
  <div v-else class="grid grid-cols-2 gap-4">
    <USkeleton v-for="n in Array.from({ length: 5 })" class="w-full aspect-square animate-pulse rounded-xl bg-gray-100" />
  </div>
</template>