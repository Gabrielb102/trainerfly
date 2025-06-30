<script setup lang="ts">
import { Category } from '@/types/hivepress/category-types'

defineProps<{
    category: Category
}>()

const emit = defineEmits<{
    (e: 'selectCategory', category: Category): void
}>()

const selectCategory = (category: Category) => {
    emit('selectCategory', category)
}
</script>

<template>
    <div class="w-full h-fit min-h-20 flex flex-col items-center justify-center gap-2 p-4 aspect-square shadow-md/40 rounded-xl cursor-pointer bg-gray-100 hover:bg-gray-200 transition-colors"
        @click="selectCategory(category)">
        <div class="w-full h-full flex items-center justify-center">    
            <UIcon :name="category.icon ? `fa6-solid:${category.icon}` : 'fa6-solid:circle-question'" class="size-10 text-gray-400" />
        </div>
        <span class="h-fit w-full text-md font-bold text-center uppercase text-black">{{ category.name }}</span>
    </div>
</template>