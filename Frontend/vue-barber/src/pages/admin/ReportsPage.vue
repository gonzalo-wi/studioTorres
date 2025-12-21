<template>
  <div class="min-h-screen bg-gradient-to-br from-purple-950 via-gray-900 to-black px-4 py-6 md:px-6 md:py-8">
    <div class="mx-auto">
      <!-- Header -->
      <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-6 md:mb-8">
        <div>
          <h1 class="text-3xl md:text-4xl font-bold text-white mb-2">
            Reportes y Estadísticas
          </h1>
          <p class="text-gray-400 text-sm md:text-base">
            Análisis detallado del negocio con exportación a Excel
          </p>
        </div>
        <button
          @click="exportToExcel"
          :disabled="exporting"
          class="flex items-center gap-2 bg-gradient-to-r from-green-600 to-green-700 hover:from-green-700 hover:to-green-800 disabled:from-gray-600 disabled:to-gray-700 text-white font-semibold py-3 px-6 rounded-lg shadow-lg shadow-green-600/30 transition-all duration-200"
        >
          <ArrowDownTrayIcon class="w-5 h-5" />
          {{ exporting ? 'Exportando...' : 'Exportar a Excel' }}
        </button>
      </div>

      <!-- Loading -->
      <div v-if="loading" class="flex items-center justify-center py-20">
        <div class="text-gray-400 text-lg">Cargando reportes...</div>
      </div>

      <!-- Content -->
      <div v-else class="space-y-6">
        <!-- Empty State -->
        <div v-if="!barberStats.length && !serviceStats.length && summary.totalAppointments === 0" class="bg-gradient-to-br from-gray-900 to-gray-950 border border-gray-800 rounded-xl p-12 text-center">
          <div class="w-16 h-16 bg-gradient-to-br from-purple-600 to-purple-700 rounded-full flex items-center justify-center mx-auto mb-4 shadow-lg shadow-purple-600/30">
            <DocumentTextIcon class="w-8 h-8 text-white" />
          </div>
          <p class="text-gray-400 text-lg mb-2">No hay datos para el período seleccionado</p>
          <p class="text-gray-500 text-sm">Intenta seleccionar un rango de fechas diferente</p>
        </div>

        <!-- Date Filter -->
        <div class="bg-gradient-to-br from-gray-900 to-gray-950 border border-gray-800 rounded-xl p-4 md:p-6">
          <h3 class="text-white font-semibold mb-4 flex items-center gap-2">
            <CalendarDaysIcon class="w-5 h-5 text-purple-400" />
            Seleccionar Período
          </h3>
          
          <!-- Quick Filters -->
          <div class="flex flex-wrap gap-2 mb-4">
            <button
              v-for="preset in datePresets"
              :key="preset.label"
              @click="applyDatePreset(preset.days)"
              class="px-3 py-2 bg-gray-800 hover:bg-purple-600 text-gray-300 hover:text-white text-sm rounded-lg transition-colors border border-gray-700 hover:border-purple-600"
            >
              {{ preset.label }}
            </button>
          </div>

          <!-- Date Inputs -->
          <div class="grid sm:grid-cols-2 gap-4">
            <div>
              <label class="block text-gray-300 text-sm font-semibold mb-2 flex items-center gap-2">
                <span>📅</span> Fecha Inicio
              </label>
              <input 
                v-model="filters.startDate" 
                type="date"
                @change="loadReports"
                class="w-full bg-gray-950 border border-gray-700 focus:border-purple-600 text-white py-3 px-4 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-600/50 transition-all cursor-pointer" 
              />
            </div>
            <div>
              <label class="block text-gray-300 text-sm font-semibold mb-2 flex items-center gap-2">
                <span>📅</span> Fecha Fin
              </label>
              <input 
                v-model="filters.endDate" 
                type="date"
                @change="loadReports"
                class="w-full bg-gray-950 border border-gray-700 focus:border-purple-600 text-white py-3 px-4 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-600/50 transition-all cursor-pointer" 
              />
            </div>
          </div>

          <!-- Period Summary -->
          <div class="mt-4 pt-4 border-t border-gray-800">
            <div class="flex items-center justify-between text-sm">
              <span class="text-gray-400">Período seleccionado:</span>
              <span class="text-white font-semibold">{{ daysDifference }} días</span>
            </div>
          </div>
        </div>

        <template v-if="barberStats.length || serviceStats.length || summary.totalAppointments > 0">
          <!-- Summary Cards -->
          <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 md:gap-6">
          <div class="bg-gradient-to-br from-blue-900/50 to-blue-950/50 border border-blue-800/50 rounded-xl p-6">
            <div class="flex items-center justify-between mb-3">
              <CalendarDaysIcon class="w-8 h-8 text-blue-400" />
              <span class="text-blue-400 text-sm font-semibold">Total</span>
            </div>
            <div class="text-3xl font-bold text-white mb-1">{{ summary.totalAppointments }}</div>
            <div class="text-gray-400 text-sm">Turnos totales</div>
          </div>

          <div class="bg-gradient-to-br from-green-900/50 to-green-950/50 border border-green-800/50 rounded-xl p-6">
            <div class="flex items-center justify-between mb-3">
              <CurrencyDollarIcon class="w-8 h-8 text-green-400" />
              <span class="text-green-400 text-sm font-semibold">Ingresos</span>
            </div>
            <div class="text-3xl font-bold text-white mb-1">${{ summary.totalRevenue.toFixed(2) }}</div>
            <div class="text-gray-400 text-sm">Facturación total</div>
          </div>

          <div class="bg-gradient-to-br from-purple-900/50 to-purple-950/50 border border-purple-800/50 rounded-xl p-6">
            <div class="flex items-center justify-between mb-3">
              <UserGroupIcon class="w-8 h-8 text-purple-400" />
              <span class="text-purple-400 text-sm font-semibold">Barberos</span>
            </div>
            <div class="text-3xl font-bold text-white mb-1">{{ barberStats.length }}</div>
            <div class="text-gray-400 text-sm">Profesionales activos</div>
          </div>

          <div class="bg-gradient-to-br from-orange-900/50 to-orange-950/50 border border-orange-800/50 rounded-xl p-6">
            <div class="flex items-center justify-between mb-3">
              <ChartBarIcon class="w-8 h-8 text-orange-400" />
              <span class="text-orange-400 text-sm font-semibold">Promedio</span>
            </div>
            <div class="text-3xl font-bold text-white mb-1">${{ summary.averageTicket.toFixed(2) }}</div>
            <div class="text-gray-400 text-sm">Ticket promedio</div>
          </div>
        </div>

        <!-- Charts Section -->
        <div class="grid lg:grid-cols-2 gap-6">
          <!-- Barbers Performance Chart -->
          <div class="bg-gradient-to-br from-gray-900 to-gray-950 border border-gray-800 rounded-xl overflow-hidden shadow-2xl">
            <div class="bg-gradient-to-r from-purple-600 to-purple-700 px-4 py-3 md:px-6 md:py-4">
              <div class="flex items-center gap-2 md:gap-3">
                <UserGroupIcon class="w-5 h-5 md:w-6 md:h-6 text-white" />
                <div>
                  <h2 class="text-lg md:text-xl font-bold text-white">Rendimiento por Barbero</h2>
                  <p class="text-purple-100 text-xs md:text-sm">Turnos realizados</p>
                </div>
              </div>
            </div>
            
            <div class="p-4 md:p-6 space-y-4">
              <div v-for="barber in barberStats" :key="barber.barber_id" class="space-y-2">
                <div class="flex items-center justify-between text-sm">
                  <span class="text-gray-300 font-medium">{{ barber.barber_name }}</span>
                  <span class="text-white font-bold">{{ barber.total_appointments }} turnos</span>
                </div>
                <div class="relative h-10 bg-gray-800 rounded-full overflow-hidden">
                  <div 
                    class="absolute h-full bg-gradient-to-r from-purple-500 to-purple-600 rounded-full transition-all duration-500"
                    :style="{ width: `${(barber.total_appointments / maxBarberAppointments) * 100}%` }"
                  ></div>
                  <div class="absolute inset-0 flex items-center justify-between px-4 text-xs">
                    <span class="text-white font-semibold">
                      ${{ parseFloat(barber.total_revenue).toFixed(2) }}
                    </span>
                    <span class="text-gray-300">
                      {{ barber.confirmed_appointments }} confirmados
                    </span>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Services Performance Chart -->
          <div class="bg-gradient-to-br from-gray-900 to-gray-950 border border-gray-800 rounded-xl overflow-hidden shadow-2xl">
            <div class="bg-gradient-to-r from-indigo-600 to-indigo-700 px-4 py-3 md:px-6 md:py-4">
              <div class="flex items-center gap-2 md:gap-3">
                <ChartBarIcon class="w-5 h-5 md:w-6 md:h-6 text-white" />
                <div>
                  <h2 class="text-lg md:text-xl font-bold text-white">Servicios Más Solicitados</h2>
                  <p class="text-indigo-100 text-xs md:text-sm">Popularidad de servicios</p>
                </div>
              </div>
            </div>
            
            <div class="p-4 md:p-6 space-y-4">
              <div v-for="service in serviceStats" :key="service.service_id" class="space-y-2">
                <div class="flex items-center justify-between text-sm">
                  <span class="text-gray-300 font-medium">{{ service.service_name }}</span>
                  <span class="text-white font-bold">{{ service.total_appointments }} veces</span>
                </div>
                <div class="relative h-10 bg-gray-800 rounded-full overflow-hidden">
                  <div 
                    class="absolute h-full bg-gradient-to-r from-indigo-500 to-indigo-600 rounded-full transition-all duration-500"
                    :style="{ width: `${(service.total_appointments / maxServiceAppointments) * 100}%` }"
                  ></div>
                  <div class="absolute inset-0 flex items-center justify-between px-4 text-xs">
                    <span class="text-white font-semibold">
                      ${{ parseFloat(service.total_revenue).toFixed(2) }}
                    </span>
                    <span class="text-gray-300">
                      ${{ (parseFloat(service.total_revenue) / service.total_appointments).toFixed(2) }} promedio
                    </span>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Detailed Table -->
        <div class="bg-gradient-to-br from-gray-900 to-gray-950 border border-gray-800 rounded-xl overflow-hidden shadow-2xl">
          <div class="bg-gradient-to-r from-red-600 to-red-700 px-4 py-3 md:px-6 md:py-4">
            <div class="flex items-center gap-2 md:gap-3">
              <DocumentTextIcon class="w-5 h-5 md:w-6 md:h-6 text-white" />
              <div>
                <h2 class="text-lg md:text-xl font-bold text-white">Detalle Completo por Barbero</h2>
                <p class="text-red-100 text-xs md:text-sm">Desglose de estadísticas</p>
              </div>
            </div>
          </div>

          <!-- Desktop Table -->
          <div class="hidden md:block overflow-x-auto">
            <table class="min-w-full">
              <thead>
                <tr class="bg-gray-800/50 text-gray-400 text-xs uppercase tracking-wider">
                  <th class="py-3 px-4 text-left">Barbero</th>
                  <th class="py-3 px-4 text-center">Total Turnos</th>
                  <th class="py-3 px-4 text-center">Confirmados</th>
                  <th class="py-3 px-4 text-center">Pendientes</th>
                  <th class="py-3 px-4 text-center">Cancelados</th>
                  <th class="py-3 px-4 text-right">Ingresos Total</th>
                  <th class="py-3 px-4 text-right">Promedio</th>
                </tr>
              </thead>
              <tbody class="divide-y divide-gray-800">
                <tr 
                  v-for="barber in barberStats"
                  :key="barber.barber_id"
                  class="hover:bg-gray-900 transition-colors"
                >
                  <td class="py-4 px-4 text-white font-semibold">{{ barber.barber_name }}</td>
                  <td class="py-4 px-4 text-center text-gray-300">{{ barber.total_appointments }}</td>
                  <td class="py-4 px-4 text-center text-green-400">{{ barber.confirmed_appointments }}</td>
                  <td class="py-4 px-4 text-center text-yellow-400">{{ barber.pending_appointments }}</td>
                  <td class="py-4 px-4 text-center text-red-400">{{ barber.cancelled_appointments }}</td>
                  <td class="py-4 px-4 text-right text-white font-bold">${{ parseFloat(barber.total_revenue).toFixed(2) }}</td>
                  <td class="py-4 px-4 text-right text-gray-300">${{ (parseFloat(barber.total_revenue) / barber.total_appointments).toFixed(2) }}</td>
                </tr>
              </tbody>
            </table>
          </div>

          <!-- Mobile Cards -->
          <div class="md:hidden space-y-3 p-4">
            <div
              v-for="barber in barberStats"
              :key="barber.barber_id"
              class="bg-gray-900 border border-gray-800 rounded-lg p-4"
            >
              <div class="text-white font-bold text-lg mb-3">{{ barber.barber_name }}</div>
              
              <div class="grid grid-cols-2 gap-3 text-sm">
                <div>
                  <div class="text-gray-500 text-xs uppercase mb-1">Total Turnos</div>
                  <div class="text-white font-semibold">{{ barber.total_appointments }}</div>
                </div>
                <div>
                  <div class="text-gray-500 text-xs uppercase mb-1">Confirmados</div>
                  <div class="text-green-400 font-semibold">{{ barber.confirmed_appointments }}</div>
                </div>
                <div>
                  <div class="text-gray-500 text-xs uppercase mb-1">Pendientes</div>
                  <div class="text-yellow-400 font-semibold">{{ barber.pending_appointments }}</div>
                </div>
                <div>
                  <div class="text-gray-500 text-xs uppercase mb-1">Cancelados</div>
                  <div class="text-red-400 font-semibold">{{ barber.cancelled_appointments }}</div>
                </div>
                <div>
                  <div class="text-gray-500 text-xs uppercase mb-1">Ingresos</div>
                  <div class="text-white font-bold">${{ parseFloat(barber.total_revenue).toFixed(2) }}</div>
                </div>
                <div>
                  <div class="text-gray-500 text-xs uppercase mb-1">Promedio</div>
                  <div class="text-gray-300 font-semibold">${{ (parseFloat(barber.total_revenue) / barber.total_appointments).toFixed(2) }}</div>
                </div>
              </div>
            </div>
          </div>
        </div>
        </template>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { 
  ArrowDownTrayIcon, 
  CalendarDaysIcon, 
  CurrencyDollarIcon, 
  UserGroupIcon, 
  ChartBarIcon,
  DocumentTextIcon
} from '@heroicons/vue/24/outline'
import { get } from '@/services/apiClient'

const loading = ref(true)
const exporting = ref(false)
const barberStats = ref([])
const serviceStats = ref([])
const summary = ref({
  totalAppointments: 0,
  totalRevenue: 0,
  averageTicket: 0
})

const filters = ref({
  startDate: new Date(new Date().getFullYear(), new Date().getMonth(), 1).toISOString().split('T')[0],
  endDate: new Date().toISOString().split('T')[0]
})

const datePresets = [
  { label: 'Hoy', days: 0 },
  { label: 'Última Semana', days: 7 },
  { label: 'Este Mes', days: 'month' },
  { label: 'Último Mes', days: 'lastMonth' },
  { label: 'Últimos 3 Meses', days: 90 },
  { label: 'Este Año', days: 'year' }
]

const daysDifference = computed(() => {
  const start = new Date(filters.value.startDate)
  const end = new Date(filters.value.endDate)
  const diff = Math.ceil((end - start) / (1000 * 60 * 60 * 24))
  return diff + 1
})

function applyDatePreset(days) {
  const today = new Date()
  let startDate, endDate
  
  if (days === 'month') {
    // Este mes
    startDate = new Date(today.getFullYear(), today.getMonth(), 1)
    endDate = today
  } else if (days === 'lastMonth') {
    // Mes pasado
    startDate = new Date(today.getFullYear(), today.getMonth() - 1, 1)
    endDate = new Date(today.getFullYear(), today.getMonth(), 0)
  } else if (days === 'year') {
    // Este año
    startDate = new Date(today.getFullYear(), 0, 1)
    endDate = today
  } else if (days === 0) {
    // Hoy
    startDate = today
    endDate = today
  } else {
    // N días atrás
    startDate = new Date(today)
    startDate.setDate(today.getDate() - days)
    endDate = today
  }
  
  filters.value.startDate = startDate.toISOString().split('T')[0]
  filters.value.endDate = endDate.toISOString().split('T')[0]
  loadReports()
}

const maxBarberAppointments = computed(() => {
  return Math.max(...barberStats.value.map(b => b.total_appointments), 1)
})

const maxServiceAppointments = computed(() => {
  return Math.max(...serviceStats.value.map(s => s.total_appointments), 1)
})

async function loadReports() {
  try {
    loading.value = true
    const params = new URLSearchParams({
      start_date: filters.value.startDate,
      end_date: filters.value.endDate
    })
    
    const response = await get(`/admin/reports?${params}`)
    console.log('Respuesta de reportes:', response)
    
    // La respuesta viene en response.data cuando usa el wrapper de success()
    const data = response.data || response
    
    console.log('Datos parseados:', data)
    console.log('Barberos:', data.barber_stats)
    console.log('Servicios:', data.service_stats)
    console.log('Resumen:', data.summary)
    
    barberStats.value = data.barber_stats || []
    serviceStats.value = data.service_stats || []
    summary.value = {
      totalAppointments: data.summary?.total_appointments || 0,
      totalRevenue: parseFloat(data.summary?.total_revenue || 0),
      averageTicket: parseFloat(data.summary?.average_ticket || 0)
    }
    
    console.log('Estado actualizado:', {
      barbers: barberStats.value.length,
      services: serviceStats.value.length,
      summary: summary.value
    })
  } catch (error) {
    console.error('Error cargando reportes:', error)
    alert('Error al cargar los reportes: ' + (error.message || 'Error desconocido'))
  } finally {
    loading.value = false
  }
}

async function exportToExcel() {
  try {
    exporting.value = true
    const params = new URLSearchParams({
      start_date: filters.value.startDate,
      end_date: filters.value.endDate
    })
    
    const response = await fetch(`${import.meta.env.VITE_API_BASE_URL}/admin/reports/export?${params}`, {
      method: 'GET',
      headers: {
        'Authorization': `Bearer ${localStorage.getItem('admin_token')}`,
      }
    })
    
    if (!response.ok) throw new Error('Error al exportar')
    
    const blob = await response.blob()
    const url = window.URL.createObjectURL(blob)
    const a = document.createElement('a')
    a.href = url
    a.download = `reporte_barberia_${filters.value.startDate}_${filters.value.endDate}.xlsx`
    document.body.appendChild(a)
    a.click()
    window.URL.revokeObjectURL(url)
    document.body.removeChild(a)
  } catch (error) {
    console.error('Error exportando:', error)
    alert('Error al exportar el reporte')
  } finally {
    exporting.value = false
  }
}

onMounted(loadReports)
</script>
