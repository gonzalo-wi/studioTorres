<script setup>
import { ref, onMounted } from 'vue'
import { barberApiClient } from '@/services/apiClient'
import BarberLayout from '@/layouts/BarberLayout.vue'
import LoadingSpinner from '@/components/LoadingSpinner.vue'

const loading = ref(true)
const stats = ref(null)
const error = ref(null)

const fetchStats = async () => {
  loading.value = true
  error.value = null

  try {
    const response = await barberApiClient.get('/barber/stats')
    stats.value = response
  } catch (err) {
    error.value = err.message || 'Error al cargar estadísticas'
  } finally {
    loading.value = false
  }
}

const formatDate = (dateStr) => {
  const date = new Date(dateStr)
  return date.toLocaleDateString('es-AR', { day: '2-digit', month: '2-digit', year: 'numeric' })
}

const formatTime = (timeStr) => {
  return timeStr.substring(0, 5)
}

onMounted(() => {
  fetchStats()
})
</script>

<template>
  <BarberLayout>
    <div class="max-w-7xl mx-auto">
      <!-- Header -->
      <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Inicio</h1>
        <p class="text-gray-600 mt-2">Resumen de tus turnos y actividad</p>
      </div>

      <LoadingSpinner v-if="loading" />

      <div v-else-if="error" class="bg-red-50 border border-red-200 text-red-700 px-6 py-4 rounded-xl">
        {{ error }}
      </div>

      <div v-else-if="stats">
        <!-- Stats de Hoy -->
        <div class="mb-8">
          <h2 class="text-xl font-semibold text-gray-900 mb-4">Turnos de Hoy</h2>
          <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <!-- Total -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
              <div class="flex items-center justify-between">
                <div>
                  <p class="text-sm font-medium text-gray-600">Total</p>
                  <p class="text-3xl font-bold text-gray-900 mt-2">{{ stats.today.total }}</p>
                </div>
                <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                  <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                  </svg>
                </div>
              </div>
            </div>

            <!-- Confirmados -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
              <div class="flex items-center justify-between">
                <div>
                  <p class="text-sm font-medium text-gray-600">Confirmados</p>
                  <p class="text-3xl font-bold text-green-600 mt-2">{{ stats.today.confirmed }}</p>
                </div>
                <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                  <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                  </svg>
                </div>
              </div>
            </div>

            <!-- Pendientes -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
              <div class="flex items-center justify-between">
                <div>
                  <p class="text-sm font-medium text-gray-600">Pendientes</p>
                  <p class="text-3xl font-bold text-yellow-600 mt-2">{{ stats.today.pending }}</p>
                </div>
                <div class="w-12 h-12 bg-yellow-100 rounded-lg flex items-center justify-center">
                  <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                  </svg>
                </div>
              </div>
            </div>

            <!-- Cancelados -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
              <div class="flex items-center justify-between">
                <div>
                  <p class="text-sm font-medium text-gray-600">Cancelados</p>
                  <p class="text-3xl font-bold text-red-600 mt-2">{{ stats.today.cancelled }}</p>
                </div>
                <div class="w-12 h-12 bg-red-100 rounded-lg flex items-center justify-center">
                  <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                  </svg>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Stats del Mes -->
        <div class="mb-8">
          <h2 class="text-xl font-semibold text-gray-900 mb-4">Turnos del Mes</h2>
          <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
              <p class="text-sm font-medium text-gray-600">Total</p>
              <p class="text-2xl font-bold text-gray-900 mt-2">{{ stats.month.total }}</p>
            </div>
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
              <p class="text-sm font-medium text-gray-600">Confirmados</p>
              <p class="text-2xl font-bold text-green-600 mt-2">{{ stats.month.confirmed }}</p>
            </div>
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
              <p class="text-sm font-medium text-gray-600">Pendientes</p>
              <p class="text-2xl font-bold text-yellow-600 mt-2">{{ stats.month.pending }}</p>
            </div>
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
              <p class="text-sm font-medium text-gray-600">Cancelados</p>
              <p class="text-2xl font-bold text-red-600 mt-2">{{ stats.month.cancelled }}</p>
            </div>
          </div>
        </div>

        <!-- Próximos Turnos -->
        <div>
          <h2 class="text-xl font-semibold text-gray-900 mb-4">Próximos Turnos (7 días)</h2>
          <div v-if="stats.upcoming.length === 0" class="bg-white rounded-xl shadow-sm border border-gray-200 p-8 text-center">
            <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
            </svg>
            <p class="text-gray-500">No tenés turnos próximos</p>
          </div>

          <div v-else class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="divide-y divide-gray-200">
              <div
                v-for="appointment in stats.upcoming"
                :key="appointment.id"
                class="p-6 hover:bg-gray-50 transition-colors"
              >
                <div class="flex items-start justify-between">
                  <div class="flex-1">
                    <div class="flex items-center gap-3 mb-2">
                      <span class="text-lg font-semibold text-gray-900">{{ appointment.client_name }}</span>
                      <span
                        :class="[
                          'px-3 py-1 rounded-full text-xs font-medium',
                          appointment.status === 'CONFIRMED' ? 'bg-green-100 text-green-700' :
                          appointment.status === 'PENDING' ? 'bg-yellow-100 text-yellow-700' :
                          'bg-gray-100 text-gray-700'
                        ]"
                      >
                        {{ appointment.status === 'CONFIRMED' ? 'Confirmado' :
                           appointment.status === 'PENDING' ? 'Pendiente' : appointment.status }}
                      </span>
                    </div>
                    <div class="space-y-1 text-sm text-gray-600">
                      <p><strong>Servicio:</strong> {{ appointment.service.name }}</p>
                      <p><strong>Fecha:</strong> {{ formatDate(appointment.appointment_date) }}</p>
                      <p><strong>Hora:</strong> {{ formatTime(appointment.appointment_time) }}</p>
                      <p v-if="appointment.client_phone"><strong>Teléfono:</strong> {{ appointment.client_phone }}</p>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </BarberLayout>
</template>
