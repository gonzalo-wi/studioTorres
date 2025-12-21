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
      'bg-white border-2 border-gold-200/50 rounded-xl overflow-hidden shadow-lg shadow-gray-200/50',
      hover && 'transition-all duration-300 hover:border-gold-400 hover:shadow-2xl hover:shadow-gold-500/30 hover:-translate-y-2',
      clickable && 'cursor-pointer'
    ]"
  >
    <slot />
  </div>
</template>
