<script setup lang="ts">
import Wordmark from '@/components/svg/Wordmark.vue'
import { useAuth } from '@/composables/useAuth'

const { isLoggedIn, logOut } = useAuth();

const goToLogin = () => {
  window.location.href = 'wp-login.php'
}

</script>

<template>
  <header class="flex justify-between gap-0 w-full h-32 p-4 bg-white shadow-md/30 z-40">
    <div class="flex justify-start items-center h-full">
      <Wordmark alt="trainerfly" class="h-auto w-60 ml-4" pathClass="fill-[#212020]"/>
    </div>
    <slot name="center"/>
    <div class="flex justify-end items-start h-full">
      <UButton v-if="!isLoggedIn" class="uppercase font-bold" color="primary" variant="ghost"
                 label="Log in" @click="goToLogin"/>
      <UButton v-else class="uppercase font-bold" color="primary" variant="ghost"
                 label="Log out" @click="logOut"/>
    </div>
  </header>
</template>
