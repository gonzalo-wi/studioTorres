<script setup>
const props = defineProps({
  size: {
    type: String,
    default: 'md',
    validator: (value) => ['sm', 'md', 'lg', 'xl'].includes(value)
  },
  text: {
    type: String,
    default: ''
  }
})

const sizes = {
  sm: 'w-6 h-6',
  md: 'w-12 h-12',
  lg: 'w-16 h-16',
  xl: 'w-20 h-20'
}

const borderSizes = {
  sm: 'border-2',
  md: 'border-3',
  lg: 'border-4',
  xl: 'border-[5px]'
}
</script>

<template>
  <div class="flex flex-col items-center justify-center">
    <div class="relative" :class="sizes[size]">
      <!-- Outer ring -->
      <div 
        class="absolute inset-0 rounded-full border-gold-200 animate-spin"
        :class="borderSizes[size]"
        style="border-top-color: transparent; animation-duration: 0.8s;"
      ></div>
      
      <!-- Inner ring -->
      <div 
        class="absolute inset-0 rounded-full border-transparent animate-spin"
        :class="borderSizes[size]"
        style="border-right-color: #d4af37; animation-duration: 1.2s; animation-direction: reverse;"
      ></div>
      
      <!-- Center dot -->
      <div class="absolute inset-0 flex items-center justify-center">
        <div class="w-2 h-2 bg-gold-500 rounded-full animate-pulse"></div>
      </div>
    </div>
    
    <p v-if="text" class="text-dark-600 mt-4" :class="{
      'text-sm': size === 'sm',
      'text-base': size === 'md',
      'text-lg': size === 'lg',
      'text-xl': size === 'xl'
    }">
      {{ text }}
    </p>
  </div>
</template>
