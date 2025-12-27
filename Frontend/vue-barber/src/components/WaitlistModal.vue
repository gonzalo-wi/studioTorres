<script setup>
import { ref, computed } from 'vue'
import BaseButton from './BaseButton.vue'
import BaseInput from './BaseInput.vue'
import { useWaitlist } from '@/composables/useWaitlist'

const props = defineProps({
  service: { type: Object, required: true },
  selectedDate: { type: String, required: true },
  barberId: { type: [Number, String], default: null },
})

const emit = defineEmits(['close', 'success'])

const { addToWaitlist, loading, error } = useWaitlist()

// Form data
const form = ref({
  client_name: '',
  client_phone: '',
  client_email: '',
  preferred_time_start: '',
  preferred_time_end: '',
})

const formErrors = ref({})
const showSuccess = ref(false)

// Time options for dropdowns
const timeOptions = computed(() => {
  const options = []
  for (let hour = 10; hour < 19; hour++) {
    for (let min of [0, 30]) {
      const time = `${hour.toString().padStart(2, '0')}:${min.toString().padStart(2, '0')}`
      options.push(time)
    }
  }
  return options
})

// Validation
function validateForm() {
  formErrors.value = {}
  
  if (!form.value.client_name.trim()) {
    formErrors.value.client_name = 'El nombre es requerido'
  }
  
  if (!form.value.client_phone.trim()) {
    formErrors.value.client_phone = 'El teléfono es requerido'
  } else if (!/^[0-9\s\-\+\(\)]{8,20}$/.test(form.value.client_phone)) {
    formErrors.value.client_phone = 'Formato de teléfono inválido'
  }
  
  if (form.value.client_email && !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(form.value.client_email)) {
    formErrors.value.client_email = 'Email inválido'
  }

  // Si especifica rango horario, validar que start < end
  if (form.value.preferred_time_start && form.value.preferred_time_end) {
    if (form.value.preferred_time_start >= form.value.preferred_time_end) {
      formErrors.value.preferred_time_end = 'La hora de fin debe ser posterior a la de inicio'
    }
  }
  
  return Object.keys(formErrors.value).length === 0
}

// Submit
async function handleSubmit() {
  if (!validateForm()) return
  
  try {
    const data = {
      client_name: form.value.client_name,
      client_phone: form.value.client_phone,
      client_email: form.value.client_email || null,
      service_id: props.service.id,
      preferred_date: props.selectedDate,
      preferred_time_start: form.value.preferred_time_start || null,
      preferred_time_end: form.value.preferred_time_end || null,
      barber_id: props.barberId || null,
    }
    
    await addToWaitlist(data)
    showSuccess.value = true
    
    // Auto-close después de 3 segundos
    setTimeout(() => {
      emit('success')
      emit('close')
    }, 3000)
  } catch (err) {
    // Error ya manejado en composable
    console.error('Error submitting waitlist:', err)
  }
}
</script>

<template>
  <div class="fixed inset-0 bg-black/60 backdrop-blur-sm flex items-center justify-center z-50 p-4" @click.self="$emit('close')">
    <div class="bg-white rounded-xl shadow-2xl max-w-md w-full max-h-[90vh] overflow-y-auto">
      <!-- Header -->
      <div class="sticky top-0 bg-gradient-to-r from-gold-500 to-gold-600 p-6 rounded-t-xl">
        <div class="flex items-center justify-between">
          <div class="flex items-center gap-3">
            <div class="w-10 h-10 bg-white rounded-full flex items-center justify-center">
              <svg class="w-6 h-6 text-gold-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
              </svg>
            </div>
            <h2 class="text-2xl font-bold text-white">Lista de Espera</h2>
          </div>
          <button 
            @click="$emit('close')"
            class="text-white/80 hover:text-white transition-colors p-1 hover:bg-white/10 rounded-lg"
          >
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
          </button>
        </div>
      </div>

      <!-- Success Message -->
      <div v-if="showSuccess" class="p-6">
        <div class="bg-green-50 border-2 border-green-500 rounded-lg p-6 text-center">
          <div class="w-16 h-16 bg-green-500 rounded-full flex items-center justify-center mx-auto mb-4">
            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7" />
            </svg>
          </div>
          <h3 class="text-xl font-bold text-dark-900 mb-2">¡Listo!</h3>
          <p class="text-dark-600">
            Te agregamos a la lista de espera. Te notificaremos cuando se libere un turno.
          </p>
        </div>
      </div>

      <!-- Form -->
      <form v-else @submit.prevent="handleSubmit" class="p-6 space-y-5">
        <!-- Info -->
        <div class="bg-blue-50 border-l-4 border-blue-500 rounded-r-lg p-4">
          <div class="flex items-start gap-3">
            <svg class="w-5 h-5 text-blue-600 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
              <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
            </svg>
            <div>
              <p class="text-sm font-semibold text-blue-900 mb-1">
                No hay turnos disponibles para esta fecha
              </p>
              <p class="text-xs text-blue-700">
                Dejá tus datos y te avisaremos por email si se libera un turno.
              </p>
            </div>
          </div>
        </div>

        <!-- Selected Info -->
        <div class="bg-gray-50 rounded-lg p-4 space-y-2">
          <div class="flex items-center text-sm">
            <span class="font-semibold text-dark-900 mr-2">📅 Fecha:</span>
            <span class="text-dark-600">{{ new Date(selectedDate).toLocaleDateString('es-AR', { day: '2-digit', month: 'long', year: 'numeric' }) }}</span>
          </div>
          <div class="flex items-center text-sm">
            <span class="font-semibold text-dark-900 mr-2">✂️ Servicio:</span>
            <span class="text-dark-600">{{ service.title }}</span>
          </div>
        </div>

        <!-- Name -->
        <BaseInput
          v-model="form.client_name"
          label="Nombre completo"
          placeholder="Juan Pérez"
          :error="formErrors.client_name"
        />

        <!-- Phone -->
        <BaseInput
          v-model="form.client_phone"
          label="Teléfono"
          type="tel"
          placeholder="11 1234-5678"
          :error="formErrors.client_phone"
        />

        <!-- Email -->
        <BaseInput
          v-model="form.client_email"
          label="Email (opcional)"
          type="email"
          placeholder="tu@email.com"
          :error="formErrors.client_email"
        />
        <p class="text-xs text-dark-500 -mt-3 flex items-start gap-1">
          <svg class="w-4 h-4 text-gold-600 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
            <path d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z" />
            <path d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z" />
          </svg>
          <span>Te enviaremos un email cuando haya un turno disponible</span>
        </p>

        <!-- Time Preferences -->
        <div class="space-y-3">
          <label class="block text-sm font-semibold text-dark-900">
            Preferencia horaria (opcional)
          </label>
          <p class="text-xs text-dark-500 -mt-2">
            Indicá un rango horario de preferencia o dejalo vacío para cualquier horario
          </p>
          
          <div class="grid grid-cols-2 gap-3">
            <div>
              <label class="block text-xs font-medium text-dark-700 mb-1">Desde</label>
              <select 
                v-model="form.preferred_time_start"
                class="w-full bg-white border-2 border-gray-300 text-dark-900 rounded-lg px-3 py-2.5 focus:outline-none focus:border-gold-500 focus:ring-2 focus:ring-gold-200 transition-colors"
              >
                <option value="">Sin preferencia</option>
                <option v-for="time in timeOptions" :key="time" :value="time">
                  {{ time }}
                </option>
              </select>
            </div>
            
            <div>
              <label class="block text-xs font-medium text-dark-700 mb-1">Hasta</label>
              <select 
                v-model="form.preferred_time_end"
                class="w-full bg-white border-2 border-gray-300 text-dark-900 rounded-lg px-3 py-2.5 focus:outline-none focus:border-gold-500 focus:ring-2 focus:ring-gold-200 transition-colors"
              >
                <option value="">Sin preferencia</option>
                <option v-for="time in timeOptions" :key="time" :value="time">
                  {{ time }}
                </option>
              </select>
            </div>
          </div>
          <p v-if="formErrors.preferred_time_end" class="text-xs text-red-600 mt-1">
            {{ formErrors.preferred_time_end }}
          </p>
        </div>

        <!-- Error Message -->
        <div v-if="error" class="bg-red-50 border-l-4 border-red-500 rounded-r-lg p-3 flex items-start gap-2">
          <svg class="w-5 h-5 text-red-600 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
          </svg>
          <span class="text-sm text-red-800">{{ error }}</span>
        </div>

        <!-- Actions -->
        <div class="flex gap-3 pt-2">
          <button
            type="button"
            @click="$emit('close')"
            class="flex-1 px-4 py-3 bg-gray-100 hover:bg-gray-200 text-dark-700 font-semibold rounded-lg transition-colors"
          >
            Cancelar
          </button>
          <button
            type="submit"
            :disabled="loading"
            class="flex-1 px-4 py-3 bg-gold-500 hover:bg-gold-600 disabled:bg-gray-300 disabled:cursor-not-allowed text-white font-bold rounded-lg shadow-md hover:shadow-lg transition-all"
          >
            {{ loading ? 'Guardando...' : '🔔 Unirme a Lista' }}
          </button>
        </div>
      </form>
    </div>
  </div>
</template>
