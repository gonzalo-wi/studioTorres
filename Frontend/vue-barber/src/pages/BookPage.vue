<script setup>
import { ref, computed, watch, onMounted } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import BaseButton from '@/components/BaseButton.vue'
import BaseInput from '@/components/BaseInput.vue'
import BaseSelect from '@/components/BaseSelect.vue'
import Card from '@/components/Card.vue'
import { useBookingForm } from '@/composables/useBookingForm'
import { getTodayDate, getMaxDate, formatDate, isValidBookingDate } from '@/utils/dateHelpers'
import { fetchServices, fetchAvailableSlots, createBooking } from '@/services/bookingsService'
import { fetchBarbers } from '@/services/barbersService'

const router = useRouter()
const route = useRoute()
const { formData, errors, validateField, validateForm, resetForm } = useBookingForm()

const currentStep = ref(1)
const isSubmitting = ref(false)
const services = ref([])
const barbers = ref([])
const availableSlots = ref([])
const loadingSlots = ref(false)
const preSelectedService = ref(null)

// Cargar servicios y barberos al montar
onMounted(async () => {
  try {
    services.value = await fetchServices()
    barbers.value = await fetchBarbers()
    
    // Pre-seleccionar servicio si viene en la URL
    const serviceIdFromQuery = route.query.service
    if (serviceIdFromQuery) {
      const serviceId = parseInt(serviceIdFromQuery)
      const service = services.value.find(s => s.id === serviceId)
      if (service) {
        formData.value.service_id = serviceId
        preSelectedService.value = service
      }
    }
  } catch (error) {
    console.error('Error cargando datos:', error)
  }
})

const serviceOptions = computed(() => 
  services.value.map(s => ({
    value: s.id,
    label: `${s.title} (${s.duration_minutes} min) - $${s.price}`
  }))
)

const barberOptions = computed(() => 
  barbers.value.map(b => ({
    value: b.id,
    label: b.name
  }))
)

const selectedService = computed(() => 
  services.value.find(s => s.id === formData.value.service_id)
)

const selectedBarber = computed(() => 
  barbers.value.find(b => b.id === formData.value.barber_id)
)

// Cargar slots cuando cambia la fecha, servicio o barbero
watch([() => formData.value.date, () => formData.value.service_id, () => formData.value.barber_id], 
  async ([newDate, newService, newBarber]) => {
    if (newDate && newService && newBarber) {
      await loadAvailableSlots()
    } else {
      availableSlots.value = []
    }
  }
)

const loadAvailableSlots = async () => {
  if (!formData.value.date || !formData.value.service_id || !formData.value.barber_id) return
  
  try {
    loadingSlots.value = true
    const slots = await fetchAvailableSlots(
      formData.value.date, 
      formData.value.service_id,
      formData.value.barber_id
    )
    
    // Asegurar que slots es un array
    if (Array.isArray(slots)) {
      availableSlots.value = slots.map(slot => ({
        value: slot.time,
        label: slot.time,
        disabled: !slot.available
      }))
    } else {
      console.warn('Slots no es un array:', slots)
      availableSlots.value = []
    }
  } catch (error) {
    console.error('Error cargando horarios:', error)
    availableSlots.value = []
  } finally {
    loadingSlots.value = false
  }
}

// Navegación entre pasos
const goToStep = (step) => {
  if (step === 2 && currentStep.value === 1) {
    if (!preSelectedService.value) {
      validateField('service_id')
      if (errors.value.service_id) return
    }
    validateField('barber_id')
    if (!errors.value.barber_id) {
      currentStep.value = step
    }
  } else if (step === 3 && currentStep.value === 2) {
    validateField('date')
    validateField('time')
    if (!errors.value.date && !errors.value.time) {
      currentStep.value = step
    }
  } else if (step < currentStep.value) {
    currentStep.value = step
  }
}

const selectTimeSlot = (slot) => {
  formData.value.time = slot.value
  errors.value.time = ''
}

// Enviar formulario
const handleSubmit = async () => {
  if (!validateForm()) {
    return
  }

  isSubmitting.value = true

  try {
    const response = await createBooking({
      service_id: parseInt(formData.value.service_id),
      barber_id: parseInt(formData.value.barber_id),
      date: formData.value.date,
      time: formData.value.time,
      client_name: formData.value.client_name,
      client_phone: formData.value.client_phone,
      client_email: formData.value.client_email || null,
      notes: formData.value.notes
    })

    const appointment = response.appointment
    if (!appointment?.public_code) {
      throw new Error('No se encontró el código de la reserva')
    }

    resetForm()
    router.push({ 
      name: 'BookSuccess', 
      query: { code: appointment.public_code }
    })
  } catch (error) {
    console.error('Error completo:', JSON.stringify(error, null, 2))
    console.error('Datos enviados:', {
      service_id: parseInt(formData.value.service_id),
      barber_id: parseInt(formData.value.barber_id),
      date: formData.value.date,
      time: formData.value.time,
      client_name: formData.value.client_name,
      client_phone: formData.value.client_phone,
    })
    console.error('Errores de validación:', error?.errors)
    console.error('Data completo:', JSON.stringify(error?.data, null, 2))
    console.error('Message:', error?.message)
    
    // Extraer mensajes de error detallados
    let errorMessage = 'Error al crear la reserva.'
    if (error?.errors) {
      const errorList = Object.values(error.errors).flat()
      errorMessage = errorList.join('\n')
    } else if (error?.message) {
      errorMessage = error.message
    }
    
    alert(errorMessage)
  } finally {
    isSubmitting.value = false
  }
}
</script>

<template>
  <div class="py-12 min-h-[80vh]">
    <div class="container mx-auto px-4 max-w-4xl">
      <!-- Header -->
      <div class="text-center mb-12">
        <h1 class="text-4xl font-display font-bold text-dark-900 mb-4">
          Reservá tu Turno
        </h1>
        <p class="text-dark-600 text-lg">
          Completá el formulario y asegurá tu lugar
        </p>
      </div>

      <!-- Progress Steps -->
      <div class="flex items-center justify-center mb-12">
        <div v-for="step in 3" :key="step" class="flex items-center">
          <div
            :class="[
              'w-10 h-10 rounded-full flex items-center justify-center font-bold transition-all',
              currentStep >= step
                ? 'bg-gold-500 text-white shadow-md'
                : 'bg-gray-200 text-gray-500'
            ]"
          >
            {{ step }}
          </div>
          <div
            v-if="step < 3"
            :class="[
              'w-20 h-1 mx-2',
              currentStep > step ? 'bg-gold-500' : 'bg-gray-200'
            ]"
          ></div>
        </div>
      </div>

      <Card class="p-8">
        <form @submit.prevent="handleSubmit">
          <!-- Step 1: Servicio y Barbero -->
          <div v-show="currentStep === 1">
            <h2 class="text-2xl font-display font-bold text-dark-900 mb-6">
              1. {{ preSelectedService ? 'Elegí tu barbero' : 'Seleccioná servicio y barbero' }}
            </h2>

            <!-- Servicio pre-seleccionado -->
            <div v-if="preSelectedService" class="mb-6 p-4 bg-gold-50 border border-gold-300 rounded-lg">
              <h3 class="text-dark-900 font-semibold mb-2">Servicio seleccionado:</h3>
              <p class="text-lg font-bold text-gold-700">{{ preSelectedService.title }}</p>
              <div class="flex gap-4 mt-2 text-sm text-dark-600">
                <span>⏱️ {{ preSelectedService.duration_minutes }} minutos</span>
                <span>💰 ${{ preSelectedService.price }}</span>
              </div>
            </div>

            <!-- Selector de servicio (solo si no hay pre-selección) -->
            <div v-if="!preSelectedService" class="mb-6">
              <BaseSelect
                v-model="formData.service_id"
                label="Servicio"
                :options="serviceOptions"
                :error="errors.service_id"
                required
                @change="validateField('service_id')"
              />

              <div v-if="selectedService" class="mt-4 p-4 bg-gold-50 rounded-lg border border-gold-200">
                <p class="text-dark-700 text-sm mb-2">
                  ⏱️ <strong>Duración:</strong> {{ selectedService.duration_minutes }} minutos
                </p>
                <p class="text-dark-700 text-sm">
                  <strong>Precio:</strong> ${{ selectedService.price }}
                </p>
              </div>
            </div>

            <!-- Selector de barbero -->
            <div class="mb-6">
              <BaseSelect
                v-model="formData.barber_id"
                label="Barbero"
                :options="barberOptions"
                :error="errors.barber_id"
                required
                @change="validateField('barber_id')"
              />
            </div>

            <div class="mt-8 flex justify-end">
              <BaseButton
                @click="goToStep(2)"
                :disabled="!formData.service_id || !formData.barber_id"
              >
                Siguiente
              </BaseButton>
            </div>
          </div>

          <!-- Step 2: Fecha y Hora -->
          <div v-show="currentStep === 2">
            <div class="flex items-center justify-between mb-6">
              <h2 class="text-2xl font-display font-bold text-dark-900">
                2. Elegí fecha y horario
              </h2>
              <button
                type="button"
                @click="goToStep(1)"
                class="text-dark-600 hover:text-gold-600 text-sm"
              >
                ← Volver
              </button>
            </div>

            <div class="space-y-6">
              <div>
                <BaseInput
                  v-model="formData.date"
                  type="date"
                  label="Fecha"
                  :error="errors.date"
                  :min="getTodayDate()"
                  :max="getMaxDate()"
                  required
                  @change="validateField('date')"
                />
                <p class="mt-2 text-sm text-dark-600 flex items-center gap-2">
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                  </svg>
                  Podés reservar desde hoy hasta 7 días adelante. No trabajamos domingos.
                </p>
              </div>

              <div v-if="formData.date && isValidBookingDate(formData.date)">
                <label class="block text-sm font-semibold text-dark-700 mb-3">
                  Horarios disponibles <span class="text-primary-500">*</span>
                </label>
                
                <div v-if="formData.date && availableSlots.length > 0" class="grid grid-cols-2 xs:grid-cols-3 sm:grid-cols-4 md:grid-cols-5 lg:grid-cols-6 gap-2 sm:gap-3">
                  <button
                    v-for="slot in availableSlots"
                    :key="slot.value"
                    type="button"
                    @click="selectTimeSlot(slot)"
                    :disabled="slot.disabled"
                    :class="[
                      'px-3 py-2.5 sm:px-4 sm:py-3 rounded-lg text-sm sm:text-base font-semibold transition-all border-2',
                      formData.time === slot.value
                        ? 'bg-gold-500 border-gold-500 text-white shadow-md'
                        : slot.disabled
                        ? 'bg-gray-100 border-gray-300 text-gray-400 cursor-not-allowed'
                        : 'bg-white border-gold-200 text-dark-700 hover:border-gold-400 hover:bg-gold-50'
                    ]"
                  >
                    {{ slot.label }}
                  </button>
                </div>

                <p v-else class="text-dark-600 text-center py-8">
                  No hay horarios disponibles para esta fecha.
                  Probá con otra fecha.
                </p>

                <p v-if="errors.time" class="mt-2 text-sm text-red-500">
                  {{ errors.time }}
                </p>
              </div>

              <div v-else-if="formData.date" class="p-4 bg-red-500/10 border border-red-500/20 rounded-lg">
                <p class="text-red-500 text-sm">
                  La fecha seleccionada no está disponible. Recordá que no atendemos los domingos.
                </p>
              </div>
            </div>

            <div class="mt-8 flex justify-end">
              <BaseButton
                @click="goToStep(3)"
                :disabled="!formData.date || !formData.time"
              >
                Siguiente
              </BaseButton>
            </div>
          </div>

          <!-- Step 3: Datos personales -->
          <div v-show="currentStep === 3">
            <div class="flex items-center justify-between mb-6">
              <h2 class="text-2xl font-display font-bold text-dark-900">
                3. Completá tus datos
              </h2>
              <button
                type="button"
                @click="goToStep(2)"
                class="text-dark-600 hover:text-gold-600 text-sm"
              >
                ← Volver
              </button>
            </div>

            <!-- Resumen -->
            <div class="mb-8 p-4 bg-gold-50 rounded-lg border border-gold-200">
              <h3 class="text-dark-900 font-semibold mb-3">Resumen de tu turno:</h3>
              <div class="space-y-2">
                <p class="text-dark-700">
                  <strong>Servicio:</strong> {{ selectedService?.title }}
                </p>
                <p class="text-dark-700">
                  <strong>Barbero:</strong> {{ selectedBarber?.name }}
                </p>
                <p class="text-dark-700">
                  <strong>Fecha:</strong> {{ formatDate(formData.date) }}
                </p>
                <p class="text-dark-700">
                  <strong>Horario:</strong> {{ formData.time }}
                </p>
                <p class="text-dark-700">
                  <strong>Duración:</strong> {{ selectedService?.duration_minutes }} minutos
                </p>
                <p class="text-dark-700">
                  <strong>Precio:</strong> ${{ selectedService?.price }}
                </p>
              </div>
            </div>

            <div class="space-y-6">
              <div>
                <BaseInput
                  v-model="formData.client_name"
                  label="Nombre completo"
                  placeholder="Juan Pérez"
                  :error="errors.client_name"
                  required
                  @blur="validateField('client_name')"
                />
                <p class="mt-1 text-xs text-dark-500">
                  Ingresá tu nombre y apellido (solo letras, sin números ni caracteres especiales)
                </p>
              </div>

              <BaseInput
                v-model="formData.client_phone"
                label="Teléfono"
                type="tel"
                placeholder="11 2345 6789"
                :error="errors.client_phone"
                @blur="validateField('client_phone')"
              />

              <BaseInput
                v-model="formData.client_email"
                label="Email"
                type="email"
                placeholder="tu@email.com"
                :error="errors.client_email"
                required
                @blur="validateField('client_email')"
              />

              <BaseInput
                v-model="formData.notes"
                label="Observaciones (opcional)"
                placeholder="Comentarios adicionales..."
              />
            </div>

            <div class="mt-8 flex gap-4">
              <BaseButton
                type="submit"
                :loading="isSubmitting"
                :disabled="isSubmitting"
                full-width
              >
                {{ isSubmitting ? 'Reservando...' : 'Confirmar Reserva' }}
              </BaseButton>
            </div>
          </div>
        </form>
      </Card>
    </div>
  </div>
</template>
