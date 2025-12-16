<script setup>
import { ref, computed, watch } from 'vue'
import { useRouter } from 'vue-router'
import BaseButton from '@/components/BaseButton.vue'
import BaseInput from '@/components/BaseInput.vue'
import BaseSelect from '@/components/BaseSelect.vue'
import Card from '@/components/Card.vue'
import { useBookingForm } from '@/composables/useBookingForm'
import { generateTimeSlots, getTodayDate, getMaxDate, formatDate, isValidBookingDate } from '@/utils/dateHelpers'
import { getAllServices, fetchBookings, createBooking } from '@/services/bookingsService'

const router = useRouter()
const { formData, errors, validateField, validateForm, resetForm } = useBookingForm()

const currentStep = ref(1)
const isSubmitting = ref(false)
const existingBookings = ref([])
const availableSlots = ref([])

// Servicios
const services = getAllServices()
const serviceOptions = services.map(s => ({
  value: s.id,
  label: `${s.name} (${s.duration} min) - $${s.price}`
}))

const selectedService = computed(() => 
  services.find(s => s.id === formData.value.service)
)

// Cargar reservas existentes cuando cambia la fecha
watch(() => formData.value.date, async (newDate) => {
  if (newDate && formData.value.service) {
    try {
      existingBookings.value = await fetchBookings({ date: newDate })
      updateAvailableSlots()
    } catch (error) {
      console.error('Error cargando reservas:', error)
    }
  }
})

// Actualizar slots cuando cambia el servicio o la fecha
watch([() => formData.value.service, () => formData.value.date], () => {
  updateAvailableSlots()
})

const updateAvailableSlots = () => {
  if (formData.value.date && formData.value.service && selectedService.value) {
    availableSlots.value = generateTimeSlots(
      formData.value.date,
      selectedService.value.duration,
      existingBookings.value
    )
  } else {
    availableSlots.value = []
  }
}

// Navegación entre pasos
const goToStep = (step) => {
  if (step === 2 && currentStep.value === 1) {
    validateField('service')
    if (!errors.value.service) {
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
  formData.value.time = slot.time
  errors.value.time = ''
}

// Enviar formulario
const handleSubmit = async () => {
  if (!validateForm()) {
    return
  }

  isSubmitting.value = true

  try {
    const booking = await createBooking({
      service: formData.value.service,
      date: formData.value.date,
      time: formData.value.time,
      name: formData.value.name,
      phone: formData.value.phone,
      observations: formData.value.observations
    })

    resetForm()
    router.push({ 
      name: 'BookSuccess', 
      query: { id: booking.id }
    })
  } catch (error) {
    alert('Error al crear la reserva. Por favor intentá nuevamente.')
    console.error('Error:', error)
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
        <h1 class="text-4xl font-display font-bold text-white mb-4">
          Reservá tu Turno
        </h1>
        <p class="text-gray-400 text-lg">
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
                ? 'bg-primary-600 text-white'
                : 'bg-dark-800 text-gray-500'
            ]"
          >
            {{ step }}
          </div>
          <div
            v-if="step < 3"
            :class="[
              'w-20 h-1 mx-2',
              currentStep > step ? 'bg-primary-600' : 'bg-dark-800'
            ]"
          ></div>
        </div>
      </div>

      <Card class="p-8">
        <form @submit.prevent="handleSubmit">
          <!-- Step 1: Servicio -->
          <div v-show="currentStep === 1">
            <h2 class="text-2xl font-display font-bold text-white mb-6">
              1. Seleccioná el servicio
            </h2>

            <BaseSelect
              v-model="formData.service"
              label="Servicio"
              :options="serviceOptions"
              :error="errors.service"
              required
              @change="validateField('service')"
            />

            <div v-if="selectedService" class="mt-6 p-4 bg-dark-800 rounded-lg border border-dark-700">
              <p class="text-gray-300 text-sm mb-2">
                <strong>Duración:</strong> {{ selectedService.duration }} minutos
              </p>
              <p class="text-gray-300 text-sm">
                <strong>Precio:</strong> ${{ selectedService.price }}
              </p>
            </div>

            <div class="mt-8 flex justify-end">
              <BaseButton
                @click="goToStep(2)"
                :disabled="!formData.service"
              >
                Siguiente
              </BaseButton>
            </div>
          </div>

          <!-- Step 2: Fecha y Hora -->
          <div v-show="currentStep === 2">
            <div class="flex items-center justify-between mb-6">
              <h2 class="text-2xl font-display font-bold text-white">
                2. Elegí fecha y hora
              </h2>
              <button
                type="button"
                @click="goToStep(1)"
                class="text-gray-400 hover:text-white text-sm"
              >
                ← Volver
              </button>
            </div>

            <div class="space-y-6">
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

              <div v-if="formData.date && isValidBookingDate(formData.date)">
                <label class="block text-sm font-semibold text-gray-300 mb-3">
                  Horarios disponibles <span class="text-primary-500">*</span>
                </label>
                
                <div v-if="availableSlots.length > 0" class="grid grid-cols-3 sm:grid-cols-4 md:grid-cols-6 gap-3">
                  <button
                    v-for="slot in availableSlots"
                    :key="slot.time"
                    type="button"
                    @click="selectTimeSlot(slot)"
                    :class="[
                      'px-4 py-3 rounded-lg font-semibold transition-all border-2',
                      formData.time === slot.time
                        ? 'bg-primary-600 border-primary-600 text-white'
                        : 'bg-dark-800 border-dark-700 text-gray-300 hover:border-primary-600 hover:text-white'
                    ]"
                  >
                    {{ slot.time }}
                  </button>
                </div>

                <p v-else class="text-gray-400 text-center py-8">
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
              <h2 class="text-2xl font-display font-bold text-white">
                3. Tus datos
              </h2>
              <button
                type="button"
                @click="goToStep(2)"
                class="text-gray-400 hover:text-white text-sm"
              >
                ← Volver
              </button>
            </div>

            <!-- Resumen -->
            <div class="mb-8 p-4 bg-dark-800 rounded-lg border border-dark-700">
              <h3 class="text-white font-semibold mb-3">Resumen de tu turno:</h3>
              <div class="space-y-2 text-sm">
                <p class="text-gray-300">
                  <strong>Servicio:</strong> {{ selectedService?.name }}
                </p>
                <p class="text-gray-300">
                  <strong>Fecha:</strong> {{ formatDate(formData.date) }}
                </p>
                <p class="text-gray-300">
                  <strong>Hora:</strong> {{ formData.time }}
                </p>
                <p class="text-gray-300">
                  <strong>Duración:</strong> {{ selectedService?.duration }} minutos
                </p>
              </div>
            </div>

            <div class="space-y-6">
              <BaseInput
                v-model="formData.name"
                label="Nombre completo"
                placeholder="Juan Pérez"
                :error="errors.name"
                required
                @blur="validateField('name')"
              />

              <BaseInput
                v-model="formData.phone"
                label="Teléfono"
                type="tel"
                placeholder="11 2345 6789"
                :error="errors.phone"
                required
                @blur="validateField('phone')"
              />

              <BaseInput
                v-model="formData.observations"
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
