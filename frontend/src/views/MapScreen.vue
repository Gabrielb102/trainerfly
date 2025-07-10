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
    <Map />
  </div>
</template>
