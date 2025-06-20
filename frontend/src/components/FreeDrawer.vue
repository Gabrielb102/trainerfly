<script setup lang="ts">

import { ref, watch, onMounted } from 'vue'

const props = defineProps({
  direction: {
    type: String,
    default: 'left'
  },
  showToggleButton: {
    type: Boolean,
    default: false
  },
  initiallyOpen: {
    type: Boolean,
    default: false
  },
  buttonText: {
    type: String,
    default: 'Open Drawer'
  },
  transparent: {
    type: Boolean,
    default: false
  }
})

const isOpen = ref<boolean>(props.initiallyOpen)
const drawerClass = ref<string>('')

// Function to toggle drawer open/closed
const toggleDrawer = () => {
  isOpen.value = !isOpen.value
}

// Initialize the base classes
const getBaseClass = (): string => {
  let baseClass = 'absolute transition-transform duration-300 ease-in-out z-50 shadow-lg'

  if (props.transparent) {
    baseClass += ' bg-transparent'
  } else {
    baseClass += ' bg-white dark:bg-gray-800'
  }

  if (props.direction === 'left' || props.direction === 'right' || !props.direction) {
    baseClass += ' h-full w-80 min-w-40 top-0 bottom-0'
  } else if (props.direction === 'top') {
    baseClass += ' w-full h-auto max-h-[70vh] min-h-40 top-0'
  } else if (props.direction === 'bottom') {
    // Fixed height for bottom drawer, always anchored to bottom
    baseClass += ' w-full min-h-60 h-fit bottom-0 overflow-y-auto'
  }

  if (props.direction === 'left' || !props.direction) {
    baseClass += ' left-0'
  } else if (props.direction === 'right') {
    baseClass += ' right-0'
  } else if (props.direction === 'top') {
    // already handled above
  } else if (props.direction === 'bottom') {
    // already handled above
  }

  return baseClass
}

// Function to update the transform classes based on isOpen state
const updateTransformClass = () => {
  // First, ensure we have the base class (without any transform classes)
  const baseClass = getBaseClass()

  // Then add the appropriate transform class based on the direction and isOpen state
  if (props.direction === 'left' || !props.direction) {
    drawerClass.value = isOpen.value
      ? `${baseClass} translate-x-0`
      : `${baseClass} -translate-x-full`
  } else if (props.direction === 'right') {
    drawerClass.value = isOpen.value
      ? `${baseClass} translate-x-0`
      : `${baseClass} translate-x-full`
  } else if (props.direction === 'top') {
    drawerClass.value = isOpen.value
      ? `${baseClass} translate-y-0`
      : `${baseClass} -translate-y-full`
  } else if (props.direction === 'bottom') {
    drawerClass.value = isOpen.value
      ? `${baseClass} translate-y-0`
      : `${baseClass} translate-y-full`
  }
}

// Initialize the drawer class on mount
onMounted(() => {
  updateTransformClass()
})

// Watch for changes in isOpen and update the classes
watch(isOpen, () => {
  updateTransformClass()
})

// Watch for changes to the direction prop
watch(() => props.direction, () => {
  updateTransformClass()
})

defineExpose({
  isOpen,
  toggleDrawer
})
</script>

<template>
  <UButton
    v-if="showToggleButton"
    color="primary"
    :label="buttonText"
    @click="toggleDrawer"
  />

  <!-- Drawer with always-on render but controlled by transform classes -->
  <div :class="drawerClass">
    <slot/>
  </div>
</template>
