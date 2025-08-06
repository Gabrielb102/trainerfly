<script setup lang="js">
import Map from '@/components/map-ui/Map.vue'
import MapSearch from '@/components/map-ui/MapSearch.vue'
import Header from "@/components/Header.vue";

</script>

<template>
  <div class="flex flex-col size-full">
    <Header>
      <template #center>
        <MapSearch />
      </template>
    </Header>
    <Map />
  </div>
</template>
