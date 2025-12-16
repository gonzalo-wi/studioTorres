<script setup>
import { ref, computed, onMounted } from 'vue'
import { RouterLink } from 'vue-router'
import Card from '@/components/Card.vue'
import BadgeStatus from '@/components/BadgeStatus.vue'
import { getBookingStats, fetchBookings } from '@/services/bookingsService'
import { formatDate } from '@/utils/dateHelpers'
import { CalendarDaysIcon, ClockIcon, CheckCircleIcon, XCircleIcon } from '@heroicons/vue/24/outline'

const stats = ref({
  today: 0,
  pending: 0,
  confirmed: 0,
  cancelled: 0,
  total: 0
})

const todayBookings = ref([])
const loading = ref(true)

onMounted(async () => {
  try {
    const [statsData, bookingsData] = await Promise.all([
      getBookingStats(),
      fetchBookings({ date: new Date().toISOString().split('T')[0] })
    ])
    
    stats.value = statsData
    todayBookings.value = bookingsData
  } catch (error) {
    console.error('Error cargando dashboard:', error)
  } finally {
    loading.value = false
  }
})

const statCards = computed(() => [
  {
    label: 'Turnos Hoy',
    value: stats.value.today,
    icon: CalendarDaysIcon,
    color: 'bg-blue-500/10 text-blue-500 border-blue-500/20'
  },
  {
    label: 'Pendientes',
    value: stats.value.pending,
    icon: ClockIcon,
    color: 'bg-yellow-500/10 text-yellow-500 border-yellow-500/20'
  },
  {
    label: 'Confirmados',
    value: stats.value.confirmed,
    icon: CheckCircleIcon,
    color: 'bg-green-500/10 text-green-500 border-green-500/20'
  },
  {
    label: 'Cancelados',
    value: stats.value.cancelled,
    icon: XCircleIcon,
    color: 'bg-red-500/10 text-red-500 border-red-500/20'
  }
])
</script>

<template>
  <div>
    <!-- Header -->
    <div class="mb-8">
      <h1 class="text-3xl font-display font-bold text-white mb-2">
        Dashboard
      </h1>
      <p class="text-gray-400">
        Resumen general de las reservas
      </p>
    </div>

    <!-- Loading -->
    <div v-if="loading" class="text-center py-12">
      <ClockIcon class="w-12 h-12 mx-auto mb-4 text-gray-400 animate-pulse" />
      <p class="text-gray-400">Cargando datos...</p>
    </div>

    <!-- Content -->
    <div v-else>
      <!-- Stats Grid -->
      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <Card
          v-for="stat in statCards"
          :key="stat.label"
          class="p-6"
        >
          <div class="flex items-start justify-between">
            <div>
              <p class="text-gray-400 text-sm mb-2">{{ stat.label }}</p>
              <p class="text-4xl font-bold text-white">{{ stat.value }}</p>
            </div>
            <div :class="['p-3 rounded-lg border', stat.color]">
              <component :is="stat.icon" class="w-8 h-8" />
            </div>
          </div>
        </Card>
      </div>

      <!-- Today's Bookings -->
      <Card>
        <div class="p-6 border-b border-dark-800">
          <div class="flex items-center justify-between">
            <h2 class="text-xl font-display font-bold text-white">
              Turnos de Hoy
            </h2>
            <RouterLink
              to="/admin/bookings"
              class="text-primary-500 hover:text-primary-400 text-sm font-semibold"
            >
              Ver todos →
            </RouterLink>
          </div>
        </div>

        <div v-if="todayBookings.length > 0" class="divide-y divide-dark-800">
          <RouterLink
            v-for="booking in todayBookings"
            :key="booking.id"
            :to="`/admin/bookings/${booking.id}`"
            class="block p-6 hover:bg-dark-800 transition-colors"
          >
            <div class="flex items-center justify-between">
              <div class="flex-1">
                <div class="flex items-center gap-3 mb-2">
                  <h3 class="text-white font-semibold">{{ booking.name }}</h3>
                  <BadgeStatus :status="booking.status" />
                </div>
                <div class="text-sm text-gray-400 space-x-4">
                  <span>🕐 {{ booking.time }}</span>
                  <span>✂️ {{ booking.serviceName }}</span>
                  <span>📞 {{ booking.phone }}</span>
                </div>
              </div>
              <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
              </svg>
            </div>
          </RouterLink>
        </div>

        <div v-else class="p-12 text-center">
          <div class="text-5xl mb-4">📭</div>
          <p class="text-gray-400">No hay turnos para hoy</p>
        </div>
      </Card>

      <!-- Quick Actions -->
      <div class="mt-8 grid grid-cols-1 md:grid-cols-2 gap-6">
        <Card hover clickable class="p-6">
          <RouterLink to="/admin/bookings" class="block">
            <div class="flex items-center space-x-4">
              <div class="text-4xl">📋</div>
              <div>
                <h3 class="text-white font-semibold mb-1">Gestionar Turnos</h3>
                <p class="text-gray-400 text-sm">Ver, confirmar y cancelar reservas</p>
              </div>
            </div>
          </RouterLink>
        </Card>

        <Card hover class="p-6 opacity-50 cursor-not-allowed">
          <div class="flex items-center space-x-4">
            <div class="text-4xl">📊</div>
            <div>
              <h3 class="text-white font-semibold mb-1">Reportes</h3>
              <p class="text-gray-400 text-sm">Próximamente disponible</p>
            </div>
          </div>
        </Card>
      </div>
    </div>
  </div>
</template>
