<script setup lang="ts">

const props = defineProps({
  modelValue: Boolean,
  labelOne: {
    type: String,
    default: 'Mode 1',
  },
  labelTwo: {
    type: String,
    default: 'Mode 2',
  }
});

// <editor-fold desc="Update logic">-----------------------------------

const emit = defineEmits(['update:modelValue']);

const toggle = () => {
  emit('update:modelValue', !props.modelValue);
};

// </editor-fold>-------------------------------------------------------

</script>

<template>
  <div class="relative inline-flex cursor-pointer items-center">
    <input type="checkbox" :checked="modelValue" @change="toggle" class="peer sr-only"/>
    <div class="peer flex h-8 items-center gap-4 rounded-full bg-zinc-200 hover:bg-zinc-300 px-4 after:absolute after:left-1 after: after:h-6 after:w-16 after:rounded-full after:bg-white after:transition-all after:content-[''] peer-checked:after:translate-x-14 peer-focus:outline-none text-sm text-black">
      <span class="uppercase font-light z-100">{{ labelOne }}</span>
      <span class="uppercase font-light z-100">{{ labelTwo }}</span>
    </div>
  </div>
</template>
