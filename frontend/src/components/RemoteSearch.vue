<script setup>
import { useRouter } from 'vue-router'
import ModeSwitch from '@/components/ModeSwitch.vue'

//#region Navigate to Map Screen

const router = useRouter()

const goToMap = () => {
  router.push('/')
}

//#endregion

</script>

<template>
  <!--Goes within the Header component slot -->
  <div class="flex flex-col gap-2 justify-end h-full items-center">
    <div class="flex gap-6 justify-center items-center relative">
      <span class="uppercase font-bold">Looking for online training</span>
      <UButton label="Search for Local" variant="outline"
               color="primary" @click="goToMap" />
    </div>
    <div class="flex gap-2 justify-center items-center">
      <span class="uppercase font-bold">Let's:</span>
      <ModeSwitch labelOne="Learn" labelTwo="Share" />
    </div>
  </div>
</template>
