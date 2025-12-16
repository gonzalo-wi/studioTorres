<script setup>
import { ref, computed, onMounted } from 'vue'
import { RouterLink } from 'vue-router'
import Card from '@/components/Card.vue'
import BadgeStatus from '@/components/BadgeStatus.vue'
import BaseSelect from '@/components/BaseSelect.vue'
import BaseInput from '@/components/BaseInput.vue'
import { fetchBookings, getAllServices } from '@/services/bookingsService'
import { formatDate } from '@/utils/dateHelpers'
import { ClockIcon } from '@heroicons/vue/24/outline'

const bookings = ref([])
const loading = ref(true)

// Filtros
const filters = ref({
  date: '',
  status: '',
  service: '',
  search: ''
})

const services = getAllServices()
const statusOptions = [
  { value: '', label: 'Todos los estados' },
  { value: 'PENDING', label: 'Pendiente' },
  { value: 'CONFIRMED', label: 'Confirmado' },
  { value: 'CANCELLED', label: 'Cancelado' },
  { value: 'RESCHEDULED', label: 'Reprogramado' }
]

const serviceOptions = [
  { value: '', label: 'Todos los servicios' },
  ...services.map(s => ({ value: s.id, label: s.name }))
]

// Filtrado de bookings
const filteredBookings = computed(() => {
  let result = [...bookings.value]

  if (filters.value.date) {
    result = result.filter(b => b.date === filters.value.date)
  }

  if (filters.value.status) {
    result = result.filter(b => b.status === filters.value.status)
  }

  if (filters.value.service) {
    result = result.filter(b => b.service === filters.value.service)
  }

  if (filters.value.search) {
    const search = filters.value.search.toLowerCase()
    result = result.filter(b =>
      b.name.toLowerCase().includes(search) ||
      b.phone.includes(search) ||
      b.id.includes(search)
    )
  }

  // Ordenar por fecha y hora (más recientes primero)
  result.sort((a, b) => {
    const dateCompare = b.date.localeCompare(a.date)
    if (dateCompare !== 0) return dateCompare
    return b.time.localeCompare(a.time)
  })

  return result
})

const loadBookings = async () => {
  loading.value = true
  try {
    bookings.value = await fetchBookings()
  } catch (error) {
    console.error('Error cargando turnos:', error)
  } finally {
    loading.value = false
  }
}

const clearFilters = () => {
  filters.value = {
    date: '',
    status: '',
    service: '',
    search: ''
  }
}

onMounted(() => {
  loadBookings()
})
</script>

<template>
  <div>
    <!-- Header -->
    <div class="mb-8">
      <h1 class="text-3xl font-display font-bold text-white mb-2">
        Gestión de Turnos
      </h1>
      <p class="text-gray-400">
        Administrá todas las reservas
      </p>
    </div>

    <!-- Filters -->
    <Card class="p-6 mb-6">
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
        <BaseInput
          v-model="filters.search"
          placeholder="Buscar por nombre, teléfono o ID..."
        />

        <BaseInput
          v-model="filters.date"
          type="date"
          placeholder="Filtrar por fecha"
        />

        <BaseSelect
          v-model="filters.status"
          :options="statusOptions"
        />

        <BaseSelect
          v-model="filters.service"
          :options="serviceOptions"
        />
      </div>

      <div class="mt-4 flex justify-between items-center">
        <p class="text-sm text-gray-400">
          Mostrando {{ filteredBookings.length }} de {{ bookings.length }} turnos
        </p>
        <button
          @click="clearFilters"
          class="text-sm text-primary-500 hover:text-primary-400 font-semibold"
        >
          Limpiar filtros
        </button>
      </div>
    </Card>

    <!-- Loading -->
    <div v-if="loading" class="text-center py-12">
      <ClockIcon class="w-12 h-12 mx-auto mb-4 text-gray-400 animate-pulse" />
      <p class="text-gray-400">Cargando turnos...</p>
    </div>

    <!-- Bookings Table -->
    <Card v-else-if="filteredBookings.length > 0">
      <!-- Desktop Table -->
      <div class="hidden md:block overflow-x-auto">
        <table class="w-full">
          <thead class="border-b border-dark-800">
            <tr>
              <th class="text-left p-4 text-gray-400 text-sm font-semibold">ID</th>
              <th class="text-left p-4 text-gray-400 text-sm font-semibold">Cliente</th>
              <th class="text-left p-4 text-gray-400 text-sm font-semibold">Fecha</th>
              <th class="text-left p-4 text-gray-400 text-sm font-semibold">Hora</th>
              <th class="text-left p-4 text-gray-400 text-sm font-semibold">Servicio</th>
              <th class="text-left p-4 text-gray-400 text-sm font-semibold">Estado</th>
              <th class="text-left p-4 text-gray-400 text-sm font-semibold">Acciones</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-dark-800">
            <tr
              v-for="booking in filteredBookings"
              :key="booking.id"
              class="hover:bg-dark-800 transition-colors"
            >
              <td class="p-4 text-gray-300 text-sm font-mono">#{{ booking.id }}</td>
              <td class="p-4">
                <div class="text-white font-semibold">{{ booking.name }}</div>
                <div class="text-gray-400 text-sm">{{ booking.phone }}</div>
              </td>
              <td class="p-4 text-gray-300 text-sm">
                {{ formatDate(booking.date) }}
              </td>
              <td class="p-4 text-white font-semibold">{{ booking.time }}</td>
              <td class="p-4 text-gray-300 text-sm">{{ booking.serviceName }}</td>
              <td class="p-4">
                <BadgeStatus :status="booking.status" />
              </td>
              <td class="p-4">
                <RouterLink
                  :to="`/admin/bookings/${booking.id}`"
                  class="text-primary-500 hover:text-primary-400 text-sm font-semibold"
                >
                  Ver detalle →
                </RouterLink>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Mobile Cards -->
      <div class="md:hidden divide-y divide-dark-800">
        <RouterLink
          v-for="booking in filteredBookings"
          :key="booking.id"
          :to="`/admin/bookings/${booking.id}`"
          class="block p-4 hover:bg-dark-800 transition-colors"
        >
          <div class="flex items-start justify-between mb-3">
            <div>
              <h3 class="text-white font-semibold mb-1">{{ booking.name }}</h3>
              <p class="text-gray-400 text-sm">{{ booking.phone }}</p>
            </div>
            <BadgeStatus :status="booking.status" />
          </div>
          
          <div class="grid grid-cols-2 gap-2 text-sm mb-2">
            <div>
              <span class="text-gray-400">Fecha:</span>
              <p class="text-white">{{ booking.date }}</p>
            </div>
            <div>
              <span class="text-gray-400">Hora:</span>
              <p class="text-white font-semibold">{{ booking.time }}</p>
            </div>
          </div>
          
          <p class="text-gray-300 text-sm">{{ booking.serviceName }}</p>
        </RouterLink>
      </div>
    </Card>

    <!-- Empty State -->
    <Card v-else class="p-12 text-center">
      <div class="text-6xl mb-4">📭</div>
      <h3 class="text-xl font-display font-bold text-white mb-2">
        No hay turnos
      </h3>
      <p class="text-gray-400">
        {{ filters.search || filters.date || filters.status || filters.service
          ? 'No se encontraron turnos con los filtros aplicados'
          : 'Todavía no hay turnos registrados'
        }}
      </p>
    </Card>
  </div>
</template>
