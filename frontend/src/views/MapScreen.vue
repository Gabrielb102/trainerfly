<script setup lang="js">
import Map from '@/components/Map.vue'
import MapSearch from '@/components/MapSearch.vue'
import Header from "@/components/Header.vue";

</script>

<template>
  <div class="flex flex-col size-full">
    <Header>
      <template #center>
        <MapSearch />
      </template>
    </Header>
    <div class="hidden flex flex-col items-center justify-center gap-2 h-96 w-full rounded-3xl bg-white">
      <span class="text-3xl font-bold">Test Div</span>
      <div class="flex flex-col h-full w-80 grid grid-cols-2 gap-4 overflow-y-hidden">
      </div>
    </div>
    <Map />
  </div>
</template>
