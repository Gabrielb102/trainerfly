<script setup lang="ts">

const {mobileHomeURLSuffix} = localized

const navigateToHPHome = () => {
  const baseURL = window.location.origin;
  const url = `${baseURL}/${mobileHomeURLSuffix}`;

  console.log("Navigating to:", url);

  window.location.href = url;
};

</script>

<template>
    <div
        class="p-8 flex flex-col gap-4 justify-center items-center size-full rounded-xl bg-white shadow-black shadow-xl">
        <Wordmark alt="Trainerfly Logo" class="h-auto w-full md:w-2/3 mb-8" pathClass="fill-[#212020]" />
        <span class="font-bold text-gray-400">Tap to enter Trainerfly.</span>
        <UButton color="primary" rounded size="lg" @click="navigateToHPHome" class="p-4 w-full md:w-1/2 hover:cursor-pointer">
            Enter
        </UButton>
    </div>
</template>