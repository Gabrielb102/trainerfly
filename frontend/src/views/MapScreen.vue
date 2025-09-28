<script setup lang="js">
import Map from '@/components/map-ui/Map.vue'
import MapSearch from '@/components/map-ui/MapSearch.vue'
import Header from "@/components/Header.vue";

const {mobileHomeURLSuffix} = localized

const navigateToHPHome = () => {
  const baseURL = window.location.origin;
  const url = `${baseURL}/${mobileHomeURLSuffix}`;

  window.location.href = url;
};

</script>

<template>
  <div class="size-full lg:hidden flex bg-gray-300 p-4 md:p-8">
    <div class="p-8 flex flex-col gap-4 justify-center items-center size-full rounded-xl bg-white shadow-black shadow-xl">
      <Wordmark alt="Trainerfly Logo" class="h-auto w-full md:w-2/3 mb-8" pathClass="fill-[#212020]" />
      <span class="font-bold text-gray-400">Tap to enter Trainerfly.</span>
      <UButton color="primary" rounded size="large" @click="navigateToHPHome" class="p-4 w-full md:w-1/2">
        Enter
      </UButton>
    </div>
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
