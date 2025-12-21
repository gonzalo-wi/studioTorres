<script setup>
import { ref, computed, onMounted, watch } from 'vue'
import { RouterLink } from 'vue-router'
import Card from '@/components/Card.vue'
import BadgeStatus from '@/components/BadgeStatus.vue'
import { getBookingStats, getMonthlyStats, fetchBookings } from '@/services/bookingsService'
import { formatDate } from '@/utils/dateHelpers'
import { CalendarDaysIcon, ClockIcon, CheckCircleIcon, XCircleIcon, CurrencyDollarIcon, ChartBarIcon } from '@heroicons/vue/24/outline'

const stats = ref({
  today: 0,
  pending: 0,
  confirmed: 0,
  cancelled: 0,
  total: 0
})

const monthlyData = ref([])
const todayBookings = ref([])
const loading = ref(true)
const selectedMonth = ref(new Date().toISOString().split('T')[0].substring(0, 7))

const loadBookingsForMonth = async () => {
  try {
    // Obtener primer y último día del mes seleccionado
    const [year, month] = selectedMonth.value.split('-')
    const firstDay = new Date(year, month - 1, 1).toISOString().split('T')[0]
    const lastDay = new Date(year, month, 0).toISOString().split('T')[0]
    
    const bookingsData = await fetchBookings({ 
      from: firstDay,
      to: lastDay
    })
    todayBookings.value = bookingsData
  } catch (error) {
    console.error('Error cargando turnos:', error)
  }
}

watch(selectedMonth, loadBookingsForMonth)

onMounted(async () => {
  try {
    const [statsData, monthly] = await Promise.all([
      getBookingStats(),
      getMonthlyStats()
    ])
    
    stats.value = statsData
    monthlyData.value = monthly
    await loadBookingsForMonth()
  } catch (error) {
    console.error('Error cargando dashboard:', error)
  } finally {
    loading.value = false
  }
})

const statCards = computed(() => [
  {
    label: 'Turnos Hoy',
    value: stats.value.today_appointments || 0,
    icon: CalendarDaysIcon,
    color: 'bg-blue-500/10 text-blue-500 border-blue-500/20'
  },
  {
    label: 'Pendientes',
    value: stats.value.pending || 0,
    icon: ClockIcon,
    color: 'bg-yellow-500/10 text-yellow-500 border-yellow-500/20'
  },
  {
    label: 'Confirmados',
    value: stats.value.confirmed || 0,
    icon: CheckCircleIcon,
    color: 'bg-green-500/10 text-green-500 border-green-500/20'
  },
  {
    label: 'Cancelados Hoy',
    value: stats.value.cancelled_today || 0,
    icon: XCircleIcon,
    color: 'bg-red-500/10 text-red-500 border-red-500/20'
  }
])

const totalMonthlyRevenue = computed(() => {
  return monthlyData.value.reduce((sum, month) => sum + (month.revenue || 0), 0)
})

const totalMonthlyAppointments = computed(() => {
  return monthlyData.value.reduce((sum, month) => sum + (month.total || 0), 0)
})

const maxRevenue = computed(() => {
  return Math.max(...monthlyData.value.map(m => m.revenue || 0), 1)
})

const maxAppointments = computed(() => {
  return Math.max(...monthlyData.value.map(m => m.total || 0), 1)
})
</script>

<template>
  <div class="min-h-screen bg-gray-50 px-4 py-6 md:px-6 md:py-8">
    <!-- Header -->
    <div class="mb-6 md:mb-8">
      <h1 class="text-3xl md:text-4xl font-bold text-dark-800 mb-2">
        Dashboard
      </h1>
      <p class="text-dark-600 text-sm md:text-base">
        Resumen general y estadísticas del negocio
      </p>
    </div>

    <!-- Loading -->
    <div v-if="loading" class="text-center py-12">
      <ClockIcon class="w-12 h-12 mx-auto mb-4 text-gold-500 animate-pulse" />
      <p class="text-dark-600">Cargando datos...</p>
    </div>

    <!-- Content -->
    <div v-else class="space-y-8">
      <!-- Stats Grid -->
      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 md:gap-6">
        <Card
          v-for="stat in statCards"
          :key="stat.label"
          class="p-4 md:p-6 hover:border-gold-500 hover:shadow-lg transition-all duration-300"
        >
          <div class="flex items-start justify-between">
            <div>
              <p class="text-dark-600 text-xs md:text-sm mb-2">{{ stat.label }}</p>
              <p class="text-3xl md:text-4xl font-bold text-dark-800">{{ stat.value }}</p>
            </div>
            <div :class="['p-2 md:p-3 rounded-lg border', stat.color]">
              <component :is="stat.icon" class="w-6 h-6 md:w-8 md:h-8" />
            </div>
          </div>
        </Card>
      </div>

      <!-- Monthly Stats Section -->
      <div class="grid lg:grid-cols-2 gap-6 md:gap-8">
        <!-- Appointments Chart -->
        <div class="bg-white border border-gold-200 rounded-xl overflow-hidden shadow-lg">
          <div class="bg-gradient-to-r from-gold-500 to-gold-600 px-4 py-3 md:px-6 md:py-4">
            <div class="flex items-center gap-2 md:gap-3">
              <ChartBarIcon class="w-5 h-5 md:w-6 md:h-6 text-white" />
              <div>
                <h2 class="text-lg md:text-xl font-bold text-white">Turnos por Mes</h2>
                <p class="text-white/90 text-xs md:text-sm">Últimos 6 meses</p>
              </div>
            </div>
          </div>
          
          <div class="p-4 md:p-6">
            <div class="mb-6 text-center">
              <div class="text-4xl font-bold text-dark-800">{{ totalMonthlyAppointments }}</div>
              <div class="text-dark-600 text-sm">turnos totales</div>
            </div>
            
            <div class="space-y-4">
              <div v-for="month in monthlyData" :key="month.month" class="space-y-2">
                <div class="flex items-center justify-between text-sm">
                  <span class="text-dark-700 font-medium">{{ month.label }}</span>
                  <span class="text-dark-800 font-bold">{{ month.total }}</span>
                </div>
                <div class="relative h-8 bg-gray-100 rounded-full overflow-hidden border border-gray-200">
                  <div 
                    class="absolute h-full bg-gradient-to-r from-blue-500 to-blue-600 rounded-full transition-all duration-500"
                    :style="{ width: `${(month.total / maxAppointments) * 100}%` }"
                  ></div>
                  <div class="absolute inset-0 flex items-center px-3 text-xs">
                    <span class="text-white font-bold drop-shadow-[0_2px_4px_rgba(0,0,0,0.8)]">
                      {{ month.confirmed }} confirmados
                    </span>
                    <span class="ml-auto text-white font-bold drop-shadow-[0_2px_4px_rgba(0,0,0,0.8)]">
                      {{ month.pending }} pendientes
                    </span>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Revenue Chart -->
        <div class="bg-white border border-gold-200 rounded-xl overflow-hidden shadow-lg">
          <div class="bg-gradient-to-r from-green-600 to-green-700 px-4 py-3 md:px-6 md:py-4">
            <div class="flex items-center gap-2 md:gap-3">
              <CurrencyDollarIcon class="w-5 h-5 md:w-6 md:h-6 text-white" />
              <div>
                <h2 class="text-lg md:text-xl font-bold text-white">Ingresos por Mes</h2>
                <p class="text-white/90 text-xs md:text-sm">Últimos 6 meses</p>
              </div>
            </div>
          </div>
          
          <div class="p-4 md:p-6">
            <div class="mb-6 text-center">
              <div class="text-4xl font-bold text-green-600">${{ totalMonthlyRevenue.toFixed(2) }}</div>
              <div class="text-dark-600 text-sm">ingresos totales</div>
            </div>
            
            <div class="space-y-4">
              <div v-for="month in monthlyData" :key="month.month" class="space-y-2">
                <div class="flex items-center justify-between text-sm">
                  <span class="text-dark-700 font-medium">{{ month.label }}</span>
                  <span class="text-green-600 font-bold">${{ month.revenue?.toFixed(2) || '0.00' }}</span>
                </div>
                <div class="relative h-8 bg-gray-100 rounded-full overflow-hidden border border-gray-200">
                  <div 
                    class="absolute h-full bg-gradient-to-r from-green-500 to-green-600 rounded-full transition-all duration-500"
                    :style="{ width: `${(month.revenue / maxRevenue) * 100}%` }"
                  ></div>
                  <div class="absolute inset-0 flex items-center px-3 text-xs">
                    <span class="text-white font-bold drop-shadow-[0_2px_4px_rgba(0,0,0,0.8)]">
                      {{ month.confirmed }} servicios
                    </span>
                    <span class="ml-auto text-white font-bold drop-shadow-[0_2px_4px_rgba(0,0,0,0.8)]">
                      ${{ (month.confirmed > 0 ? month.revenue / month.confirmed : 0).toFixed(2) }} promedio
                    </span>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Bookings Section -->
      <Card class="overflow-hidden">
        <div class="bg-gradient-to-r from-gold-500 to-gold-600 px-4 py-3 md:px-6 md:py-4">
          <div class="flex flex-col md:flex-row md:items-center justify-between gap-3 md:gap-4">
            <div class="flex items-center gap-2 md:gap-3">
              <CalendarDaysIcon class="w-5 h-5 md:w-6 md:h-6 text-white flex-shrink-0" />
              <div>
                <h2 class="text-lg md:text-xl font-bold text-white">Turnos del Mes</h2>
                <p class="text-gold-100 text-xs md:text-sm">{{ todayBookings.length }} turnos programados</p>
              </div>
            </div>
            <div class="flex items-center gap-2 md:gap-3">
              <input 
                v-model="selectedMonth" 
                type="month" 
                class="bg-white/10 border border-white/20 text-white text-sm py-2 px-3 md:px-4 rounded-lg focus:ring-2 focus:ring-white/50 focus:border-transparent transition"
              />
              <RouterLink
                to="/admin/bookings"
                class="bg-white/10 hover:bg-white/20 text-white px-3 md:px-4 py-2 rounded-lg text-xs md:text-sm font-semibold transition whitespace-nowrap"
              >
                Ver todos →
              </RouterLink>
            </div>
          </div>
        </div>

        <!-- Table Desktop -->
        <div v-if="todayBookings.length > 0" class="hidden md:block overflow-x-auto">
          <table class="min-w-full">
            <thead>
              <tr class="bg-gray-100 text-dark-700 text-xs uppercase tracking-wider">
                <th class="py-3 px-4 text-left w-32">Fecha</th>
                <th class="py-3 px-4 text-left w-20">Hora</th>
                <th class="py-3 px-4 text-left min-w-[150px]">Cliente</th>
                <th class="py-3 px-4 text-left w-32">Teléfono</th>
                <th class="py-3 px-4 text-left min-w-[120px]">Servicio</th>
                <th class="py-3 px-4 text-left min-w-[120px]">Barbero</th>
                <th class="py-3 px-4 text-left w-32">Estado</th>
                <th class="py-3 px-4 text-left w-12"></th>
              </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
              <tr 
                v-for="booking in todayBookings"
                :key="booking.id"
                class="hover:bg-gray-50 transition-colors"
              >
                <td class="py-4 px-4 text-dark-700">{{ booking.date }}</td>
                <td class="py-4 px-4 text-dark-700 font-mono">{{ booking.time }}</td>
                <td class="py-4 px-4 text-dark-800 font-semibold">{{ booking.name }}</td>
                <td class="py-4 px-4 text-dark-600 text-sm">{{ booking.phone }}</td>
                <td class="py-4 px-4 text-dark-700">{{ booking.serviceName }}</td>
                <td class="py-4 px-4 text-dark-700">{{ booking.barberName }}</td>
                <td class="py-4 px-4">
                  <BadgeStatus :status="booking.status" />
                </td>
                <td class="py-4 px-4">
                  <RouterLink :to="`/admin/bookings/${booking.id}`" class="text-gold-600 hover:text-gold-700">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                  </RouterLink>
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <!-- Cards Mobile -->
        <div v-if="todayBookings.length > 0" class="md:hidden space-y-3 p-4">
          <RouterLink
            v-for="booking in todayBookings"
            :key="booking.id"
            :to="`/admin/bookings/${booking.id}`"
            class="block bg-white hover:bg-gray-50 border border-gold-200 rounded-lg p-4 transition-colors shadow-sm"
          >
            <div class="flex items-start justify-between mb-3">
              <div>
                <div class="text-dark-800 font-semibold text-lg mb-1">{{ booking.name }}</div>
                <div class="text-dark-600 text-sm">{{ booking.phone }}</div>
              </div>
              <BadgeStatus :status="booking.status" />
            </div>
            
            <div class="grid grid-cols-2 gap-3 text-sm">
              <div>
                <div class="text-dark-500 text-xs uppercase mb-1">Fecha</div>
                <div class="text-dark-700">{{ booking.date }}</div>
              </div>
              <div>
                <div class="text-dark-500 text-xs uppercase mb-1">Hora</div>
                <div class="text-dark-700 font-mono">{{ booking.time }}</div>
              </div>
              <div>
                <div class="text-dark-500 text-xs uppercase mb-1">Servicio</div>
                <div class="text-dark-700">{{ booking.serviceName }}</div>
              </div>
              <div>
                <div class="text-dark-500 text-xs uppercase mb-1">Barbero</div>
                <div class="text-dark-700">{{ booking.barberName }}</div>
              </div>
            </div>
          </RouterLink>
        </div>

        <div v-else class="p-8 md:p-12 text-center bg-gray-50">
          <div class="text-5xl mb-4">📭</div>
          <p class="text-dark-600">No hay turnos programados para hoy</p>
          <RouterLink to="/admin/bookings" class="inline-block mt-4 text-gold-600 hover:text-gold-700 font-semibold">
            Ver todos los turnos →
          </RouterLink>
        </div>
      </Card>

      <!-- Quick Actions -->
      <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <Card hover clickable class="p-6 bg-white border-gold-200 shadow-md hover:shadow-lg">
          <RouterLink to="/admin/bookings" class="block">
            <div class="flex items-center space-x-4">
              <div class="text-5xl">📋</div>
              <div>
                <h3 class="text-dark-800 font-semibold text-lg mb-1">Gestionar Turnos</h3>
                <p class="text-dark-600 text-sm">Ver, confirmar y cancelar reservas</p>
              </div>
            </div>
          </RouterLink>
        </Card>

        <Card hover clickable class="p-6 bg-white border-gold-200 shadow-md hover:shadow-lg">
          <RouterLink to="/admin/barbers" class="block">
            <div class="flex items-center space-x-4">
              <div class="text-5xl">💈</div>
              <div>
                <h3 class="text-dark-800 font-semibold text-lg mb-1">Barberos</h3>
                <p class="text-dark-600 text-sm">Administrar barberos y horarios</p>
              </div>
            </div>
          </RouterLink>
        </Card>

        <Card hover clickable class="p-6 bg-white border-gold-200 shadow-md hover:shadow-lg">
          <RouterLink to="/admin/services" class="block">
            <div class="flex items-center space-x-4">
              <div class="text-5xl">✂️</div>
              <div>
                <h3 class="text-dark-800 font-semibold text-lg mb-1">Servicios</h3>
                <p class="text-dark-600 text-sm">Gestionar servicios y precios</p>
              </div>
            </div>
          </RouterLink>
        </Card>
      </div>
    </div>
  </div>
</template>
