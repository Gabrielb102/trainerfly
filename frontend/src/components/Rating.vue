<template>
  <div class="flex items-center">
    <span v-for="i in 5" :key="i">
      <UIcon
        name="i-heroicons-star-solid"
        class="text-yellow-400"
        :class="{ 'text-gray-300': i > rating }"
      />
    </span>
    <span class="ml-2 text-sm text-gray-500">({{ reviews }} reviews)</span>
  </div>
</template>

<script setup lang="ts">
defineProps({
  rating: {
    type: Number,
    required: true,
  },
  reviews: {
    type: Number,
    required: true,
  },
});
</script> 