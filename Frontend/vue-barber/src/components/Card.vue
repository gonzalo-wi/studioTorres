<script setup>
const props = defineProps({
  hover: {
    type: Boolean,
    default: false
  },
  clickable: {
    type: Boolean,
    default: false
  }
})

const emit = defineEmits(['click'])

const handleClick = () => {
  if (props.clickable) {
    emit('click')
  }
}
</script>

<template>
  <div
    @click="handleClick"
    :class="[
      'bg-dark-900 border border-dark-800 rounded-lg overflow-hidden',
      hover && 'transition-all duration-300 hover:border-primary-600 hover:shadow-lg hover:shadow-primary-600/20',
      clickable && 'cursor-pointer'
    ]"
  >
    <slot />
  </div>
</template>
