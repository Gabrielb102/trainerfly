<script setup lang="js">
import Map from '@/components/map-ui/Map.vue'
import MapSearch from '@/components/map-ui/MapSearch.vue'
import Header from "@/components/Header.vue";
import MobileMapBanner from '@/components/MobileMapBanner.vue'

</script>

<template>
  <div class="size-full lg:hidden flex bg-gray-300 p-4 md:p-8">
    <MobileMapBanner/>
  </div>
  <div class="size-full hidden lg:flex">
    <div class="flex flex-col size-full">
      <Header>
        <template #center>
          <MapSearch />
        </template>
      </Header>
      <Map />
    </div>
  </div>
</template>
