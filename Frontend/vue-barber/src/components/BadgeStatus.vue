<script setup>
const props = defineProps({
  status: {
    type: String,
    required: true,
    validator: (value) => ['PENDING', 'CONFIRMED', 'CANCELLED', 'RESCHEDULED'].includes(value)
  }
})

const statusConfig = {
  PENDING: { label: 'Pendiente', color: 'bg-yellow-500/10 text-yellow-500 border-yellow-500/20' },
  CONFIRMED: { label: 'Confirmado', color: 'bg-green-500/10 text-green-500 border-green-500/20' },
  CANCELLED: { label: 'Cancelado', color: 'bg-red-500/10 text-red-500 border-red-500/20' },
  RESCHEDULED: { label: 'Reprogramado', color: 'bg-blue-500/10 text-blue-500 border-blue-500/20' }
}
</script>

<template>
  <span
    :class="[
      'inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold border',
      statusConfig[status].color
    ]"
  >
    {{ statusConfig[status].label }}
  </span>
</template>
