<script setup>
import {onMounted} from 'vue'
import {useMap} from '@/composables/useMap'

const {createMap} = useMap('trainerfly-start-map')

onMounted(() => {
  createMap('trainerfly-start-map')
})
</script>

<template>
  <div class="w-full h-full" id="trainerfly-start-map"></div>
</template>
