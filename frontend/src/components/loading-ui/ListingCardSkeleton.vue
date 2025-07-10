<script setup lang="ts">
defineProps<{
    dark?: boolean
}>()
</script>

<template>
    <div :class="['w-full h-fit min-h-20 p-4 rounded-xl flex items-start gap-4 animate-pulse', { 'bg-gray-300': dark, 'bg-gray-100': !dark }]">
        <!-- Avatar skeleton -->
        <div :class="['mt-1 flex-shrink-0 h-14 w-14 bg-gray-200 rounded-full', { 'bg-gray-400': dark }]"></div>
        <!-- Content skeleton -->
        <div class="flex flex-col flex-grow min-w-0 gap-2">
            <!-- Title skeleton -->
            <div :class="['h-4 w-32 bg-gray-200 rounded', { 'bg-gray-400': dark }]"></div>
            <!-- Subtitle skeleton -->
            <div :class="['h-3 w-24 bg-gray-200 rounded', { 'bg-gray-400': dark }]"></div>
            <!-- Badges skeleton -->
            <div class="flex justify-start items-center gap-1 mt-1">
                <div :class="['h-6 w-16 bg-gray-200 rounded-full', { 'bg-gray-400': dark }]"></div>
                <div :class="['h-6 w-20 bg-gray-200 rounded-full', { 'bg-gray-400': dark }]"></div>
            </div>
            <!-- Price skeleton -->
            <div :class="['h-4 w-16 bg-gray-200 rounded mt-1', { 'bg-gray-400': dark }]"></div>
        </div>
    </div>
</template>