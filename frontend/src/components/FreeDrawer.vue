<script setup lang="ts">
import { ref } from 'vue'
import gsap from 'gsap'
import { onBeforeEnter, onEnter, onLeave } from '../helpers/animation'

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

// Function to toggle drawer open/closed
const toggleDrawer = () => {
  isOpen.value = !isOpen.value
}

// Get base classes for the drawer
const getBaseClass = (): string => {
  let baseClass = 'absolute z-50 shadow-lg'

  if (props.transparent) {
    baseClass += ' bg-transparent'
  } else {
    baseClass += ' bg-white dark:bg-gray-800'
  }

  if (props.direction === 'left' || props.direction === 'right' || !props.direction) {
    baseClass += ' h-full w-80 min-w-40 top-0 bottom-0'
  } else if (props.direction === 'top') {
    baseClass += ' w-full h-auto max-h-[70vh] top-0'
  } else if (props.direction === 'bottom') {
    baseClass += ' w-full bottom-0 overflow-y-auto'
  }

  if (props.direction === 'left' || !props.direction) {
    baseClass += ' left-0'
  } else if (props.direction === 'right') {
    baseClass += ' right-0'
  }

  return baseClass
}

const onLeave = (el: Element, done: () => void) => {
  const direction = props.direction || 'left'
  let x = 0
  let y = 0

  // Set exit position based on direction
  if (direction === 'left') {
    x = -100
  } else if (direction === 'right') {
    x = 100
  } else if (direction === 'top') {
    y = -100
  } else if (direction === 'bottom') {
    y = 100
  }

  gsap.to(el, {
    x: `${x}%`,
    y: `${y}%`,
    duration: 0.3,
    ease: 'power2.out',
    onComplete: done
  })
}

defineExpose({
  isOpen,
  toggleDrawer
})
</script>

<template>
  <UButton v-if="showToggleButton" color="primary" :label="buttonText" @click="toggleDrawer" />

  <!-- Drawer with Vue Transition and GSAP -->
  <Transition 
    appear
    @before-enter="onBeforeEnter"
    @enter="onEnter"
    @leave="onLeave"
  >
    <div v-if="isOpen" :class="getBaseClass()">
      <slot />
    </div>
  </Transition>
</template>