<script setup>
import { computed } from 'vue'

const props = defineProps({
  modelValue: {
    type: [String, Number],
    default: ''
  },
  label: {
    type: String,
    default: ''
  },
  options: {
    type: Array,
    required: true,
    default: () => []
  },
  error: {
    type: String,
    default: ''
  },
  disabled: {
    type: Boolean,
    default: false
  },
  required: {
    type: Boolean,
    default: false
  },
  placeholder: {
    type: String,
    default: 'Selecciona una opción'
  }
})

const emit = defineEmits(['update:modelValue'])

const selectId = computed(() => `select-${Math.random().toString(36).substr(2, 9)}`)

const updateValue = (event) => {
  emit('update:modelValue', event.target.value)
}
</script>

<template>
  <div class="w-full">
    <label
      v-if="label"
      :for="selectId"
      class="block text-sm font-semibold text-gray-300 mb-2"
    >
      {{ label }}
      <span v-if="required" class="text-primary-500">*</span>
    </label>
    
    <select
      :id="selectId"
      :value="modelValue"
      :disabled="disabled"
      :required="required"
      @change="updateValue"
      :class="[
        'w-full px-4 py-3 bg-dark-800 border rounded-lg text-white transition-colors focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-dark-950 disabled:opacity-50 disabled:cursor-not-allowed',
        error 
          ? 'border-red-500 focus:border-red-500 focus:ring-red-500' 
          : 'border-dark-700 focus:border-primary-500 focus:ring-primary-500'
      ]"
    >
      <option value="" disabled>{{ placeholder }}</option>
      <option
        v-for="option in options"
        :key="option.value"
        :value="option.value"
      >
        {{ option.label }}
      </option>
    </select>
    
    <p v-if="error" class="mt-2 text-sm text-red-500">
      {{ error }}
    </p>
  </div>
</template>
