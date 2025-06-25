<template>
  <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
    <UCard>
      <div class="flex items-center">
        <UIcon name="i-heroicons-currency-dollar" class="text-2xl" />
        <div class="ml-4">
          <p class="text-sm text-gray-500">HOURLY RATE</p>
          <p class="font-bold">$45.00</p>
        </div>
      </div>
    </UCard>
    <UCard>
      <div class="flex items-center">
        <UIcon name="i-heroicons-clock" class="text-2xl" />
        <div class="ml-4">
          <p class="text-sm text-gray-500">LESSON CANCELLATION</p>
          <p class="font-bold">Late or no-shows will be charged the booked rate</p>
        </div>
      </div>
    </UCard>
  </div>
</template>

<script setup lang="ts">
</script> 