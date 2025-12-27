<script setup>
import { ref, computed, onMounted } from 'vue'
import { barberApiClient } from '@/services/apiClient'
import BarberLayout from '@/layouts/BarberLayout.vue'
import LoadingSpinner from '@/components/LoadingSpinner.vue'
import BaseButton from '@/components/BaseButton.vue'

const loading = ref(true)
const appointments = ref([])
const error = ref(null)
const actionLoading = ref({})

// Filtros
const filters = ref({
  status: '',
  date: ''
})

const statusOptions = [
  { value: '', label: 'Todos los estados' },
  { value: 'PENDING', label: 'Pendientes' },
  { value: 'CONFIRMED', label: 'Confirmados' },
  { value: 'CANCELLED', label: 'Cancelados' }
]

const filteredAppointments = computed(() => {
  let result = [...appointments.value]

  if (filters.value.status) {
    result = result.filter(app => app.status === filters.value.status)
  }

  if (filters.value.date) {
    result = result.filter(app => app.appointment_date === filters.value.date)
  }

  return result
})

const fetchAppointments = async () => {
  loading.value = true
  error.value = null

  try {
    const response = await barberApiClient.get('/barber/appointments')
    appointments.value = response
  } catch (err) {
    error.value = err.message || 'Error al cargar turnos'
  } finally {
    loading.value = false
  }
}

const confirmAppointment = async (id) => {
  actionLoading.value[id] = true

  try {
    await barberApiClient.patch(`/barber/appointments/${id}/confirm`)
    
    // Actualizar el estado localmente
    const appointment = appointments.value.find(app => app.id === id)
    if (appointment) {
      appointment.status = 'CONFIRMED'
    }
  } catch (err) {
    alert(err.message || 'Error al confirmar el turno')
  } finally {
    actionLoading.value[id] = false
  }
}

const cancelAppointment = async (id) => {
  if (!confirm('¿Estás seguro de cancelar este turno?')) return

  actionLoading.value[id] = true

  try {
    await barberApiClient.patch(`/barber/appointments/${id}/cancel`)
    
    // Actualizar el estado localmente
    const appointment = appointments.value.find(app => app.id === id)
    if (appointment) {
      appointment.status = 'CANCELLED'
    }
  } catch (err) {
    alert(err.message || 'Error al cancelar el turno')
  } finally {
    actionLoading.value[id] = false
  }
}

const formatDate = (dateStr) => {
  const date = new Date(dateStr)
  return date.toLocaleDateString('es-AR', { 
    weekday: 'short',
    day: '2-digit', 
    month: '2-digit', 
    year: 'numeric' 
  })
}

const formatTime = (timeStr) => {
  return timeStr.substring(0, 5)
}

const getStatusColor = (status) => {
  switch (status) {
    case 'CONFIRMED':
      return 'bg-green-100 text-green-700'
    case 'PENDING':
      return 'bg-yellow-100 text-yellow-700'
    case 'CANCELLED':
      return 'bg-red-100 text-red-700'
    case 'RESCHEDULED':
      return 'bg-blue-100 text-blue-700'
    default:
      return 'bg-gray-100 text-gray-700'
  }
}

const getStatusText = (status) => {
  switch (status) {
    case 'CONFIRMED':
      return 'Confirmado'
    case 'PENDING':
      return 'Pendiente'
    case 'CANCELLED':
      return 'Cancelado'
    case 'RESCHEDULED':
      return 'Reprogramado'
    default:
      return status
  }
}

onMounted(() => {
  fetchAppointments()
})
</script>

<template>
  <BarberLayout>
    <div class="max-w-7xl mx-auto">
      <!-- Header -->
      <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Mis Turnos</h1>
        <p class="text-gray-600 mt-2">Gestiona tus turnos: confirma, cancela o revisa detalles</p>
      </div>

      <!-- Filtros -->
      <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <!-- Filtro por Estado -->
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Estado</label>
            <select
              v-model="filters.status"
              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
            >
              <option v-for="option in statusOptions" :key="option.value" :value="option.value">
                {{ option.label }}
              </option>
            </select>
          </div>

          <!-- Filtro por Fecha -->
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Fecha</label>
            <input
              type="date"
              v-model="filters.date"
              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
            />
          </div>
        </div>
      </div>

      <LoadingSpinner v-if="loading" />

      <div v-else-if="error" class="bg-red-50 border border-red-200 text-red-700 px-6 py-4 rounded-xl">
        {{ error }}
      </div>

      <div v-else>
        <!-- Lista de Turnos -->
        <div v-if="filteredAppointments.length === 0" class="bg-white rounded-xl shadow-sm border border-gray-200 p-12 text-center">
          <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
          </svg>
          <p class="text-gray-500 text-lg">No hay turnos con los filtros seleccionados</p>
        </div>

        <div v-else class="space-y-4">
          <div
            v-for="appointment in filteredAppointments"
            :key="appointment.id"
            class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 hover:shadow-md transition-shadow"
          >
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
              <!-- Info del Turno -->
              <div class="flex-1">
                <div class="flex items-center gap-3 mb-3">
                  <h3 class="text-xl font-bold text-gray-900">{{ appointment.client_name }}</h3>
                  <span :class="['px-3 py-1 rounded-full text-xs font-medium', getStatusColor(appointment.status)]">
                    {{ getStatusText(appointment.status) }}
                  </span>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-3 text-sm text-gray-600">
                  <div class="flex items-center gap-2">
                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                    <span><strong>Fecha:</strong> {{ formatDate(appointment.appointment_date) }}</span>
                  </div>

                  <div class="flex items-center gap-2">
                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span><strong>Hora:</strong> {{ formatTime(appointment.appointment_time) }}</span>
                  </div>

                  <div class="flex items-center gap-2">
                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span><strong>Servicio:</strong> {{ appointment.service.name }}</span>
                  </div>

                  <div v-if="appointment.client_phone" class="flex items-center gap-2">
                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                    </svg>
                    <span><strong>Teléfono:</strong> {{ appointment.client_phone }}</span>
                  </div>
                </div>

                <div v-if="appointment.observations" class="mt-3 p-3 bg-gray-50 rounded-lg">
                  <p class="text-sm text-gray-700"><strong>Observaciones:</strong> {{ appointment.observations }}</p>
                </div>
              </div>

              <!-- Acciones -->
              <div v-if="appointment.status === 'PENDING' || appointment.status === 'CONFIRMED'" class="flex gap-2">
                <BaseButton
                  v-if="appointment.status === 'PENDING'"
                  @click="confirmAppointment(appointment.id)"
                  :loading="actionLoading[appointment.id]"
                  class="bg-green-600 hover:bg-green-700 text-white"
                >
                  Confirmar
                </BaseButton>

                <BaseButton
                  v-if="appointment.status !== 'CANCELLED'"
                  @click="cancelAppointment(appointment.id)"
                  :loading="actionLoading[appointment.id]"
                  class="bg-red-600 hover:bg-red-700 text-white"
                >
                  Cancelar
                </BaseButton>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </BarberLayout>
</template>
