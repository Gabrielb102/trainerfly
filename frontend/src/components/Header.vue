<script setup lang="ts">
import Wordmark from '@/components/svg/Wordmark.vue'
</script>

<template>
  <header class="flex justify-between gap-0 w-full h-32 p-4 bg-white shadow-md/30 z-40">
    <div class="flex justify-start items-center h-full">
      <Wordmark alt="trainerfly" class="h-auto w-60 ml-4" pathClass="fill-[#212020]" />
    </div>
    <slot name="center" />
    <div class="flex justify-end items-start h-full">
      <NavMenu />
    </div>
  </header>
</template>
