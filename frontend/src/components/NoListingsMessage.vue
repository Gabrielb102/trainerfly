<script setup lang="ts">
defineProps<{
    categoryName: string
}>()
</script>

<template>
    <div class="w-full h-96 flex flex-col items-center justify-center bg-gray-100 rounded-xl p-4 gap-6">
        <UIcon name="fa6-solid:face-surprise" class="size-12 text-gray-400" />
        <div class="flex flex-col gap-2">
            <span class="text-center text-md leading-5 text-balance font-bold uppercase text-black">No {{ categoryName }} listings here yet</span>
            <span class="text-center text-md leading-5 text-balance font-normal text-black">
                Be the first trainer to add a listing!
            </span>
        </div>
    </div>
</template>