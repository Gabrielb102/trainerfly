<script setup lang="ts">
defineProps<{
    dark?: boolean
}>()
</script>

<template>
    <div
        :class="['w-full aspect-square min-h-20 flex flex-col items-center justify-center gap-2 py-4 px-2 aspect-square rounded-xl', { 'bg-gray-300': dark, 'bg-gray-100': !dark }]">
        <!-- Icon skeleton -->
        <div class="w-full h-full flex items-center justify-center">
            <div :class="['size-10 bg-gray-200 rounded-full', { 'bg-gray-400': dark }]"></div>
        </div>
        <!-- Text skeleton -->
        <div class="w-full h-fit flex items-center justify-center gap-0">
            <div :class="['h-4 w-20 bg-gray-200 rounded', { 'bg-gray-400': dark }]"></div>
        </div>
    </div>
</template>