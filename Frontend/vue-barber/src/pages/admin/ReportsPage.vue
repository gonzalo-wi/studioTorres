<template>
  <div class="min-h-screen bg-gray-50 px-4 py-6 md:px-6 md:py-8">
    <div class="mx-auto">
      <!-- Header -->
      <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-6 md:mb-8">
        <div>
          <h1 class="text-3xl md:text-4xl font-bold text-dark-800 mb-2">
            Reportes y Estadísticas
          </h1>
          <p class="text-dark-600 text-sm md:text-base">
            Análisis detallado del negocio con exportación a CSV
          </p>
        </div>
        <button
          @click="exportToExcel"
          :disabled="exporting"
          class="flex items-center gap-2 bg-gradient-to-r from-green-600 to-green-700 hover:from-green-700 hover:to-green-800 disabled:from-gray-400 disabled:to-gray-500 text-white font-semibold py-3 px-6 rounded-lg shadow-lg transition-all duration-200"
        >
          <ArrowDownTrayIcon class="w-5 h-5" />
          {{ exporting ? 'Exportando...' : 'Exportar CSV' }}
        </button>
      </div>

      <!-- Loading -->
      <div v-if="loading" class="flex items-center justify-center py-20">
        <div class="text-dark-600 text-lg">Cargando reportes...</div>
      </div>

      <!-- Content -->
      <div v-else class="space-y-6">
        <!-- Empty State -->
        <div v-if="!barberStats.length && !serviceStats.length && summary.totalAppointments === 0" class="bg-white border border-gold-200 rounded-xl p-12 text-center shadow-lg">
          <div class="w-16 h-16 bg-gradient-to-br from-gold-500 to-gold-600 rounded-full flex items-center justify-center mx-auto mb-4 shadow-lg">
            <DocumentTextIcon class="w-8 h-8 text-white" />
          </div>
          <p class="text-dark-600 text-lg mb-2">No hay datos para el período seleccionado</p>
          <p class="text-dark-500 text-sm">Intenta seleccionar un rango de fechas diferente</p>
        </div>

        <!-- Date Filter -->
        <div class="bg-white border border-gold-200 rounded-xl p-4 md:p-6 shadow-lg">
          <h3 class="text-dark-800 font-semibold mb-4 flex items-center gap-2">
            <CalendarDaysIcon class="w-5 h-5 text-gold-600" />
            Seleccionar Período
          </h3>
          
          <!-- Quick Filters -->
          <div class="flex flex-wrap gap-2 mb-4">
            <button
              v-for="preset in datePresets"
              :key="preset.label"
              @click="applyDatePreset(preset.days)"
              class="px-3 py-2 bg-gray-100 hover:bg-gold-500 text-dark-700 hover:text-white text-sm rounded-lg transition-colors border border-gray-300 hover:border-gold-500"
            >
              {{ preset.label }}
            </button>
          </div>

          <!-- Date Inputs -->
          <div class="grid sm:grid-cols-2 gap-4">
            <div>
              <label class="block text-dark-700 text-sm font-semibold mb-2 flex items-center gap-2">
                <span>📅</span> Fecha Inicio
              </label>
              <input 
                v-model="filters.startDate" 
                type="date"
                @change="loadReports"
                class="w-full bg-white border border-gold-300 focus:border-gold-500 text-dark-800 py-3 px-4 rounded-lg focus:outline-none focus:ring-2 focus:ring-gold-500/50 transition-all cursor-pointer" 
              />
            </div>
            <div>
              <label class="block text-dark-700 text-sm font-semibold mb-2 flex items-center gap-2">
                <span>📅</span> Fecha Fin
              </label>
              <input 
                v-model="filters.endDate" 
                type="date"
                @change="loadReports"
                class="w-full bg-white border border-gold-300 focus:border-gold-500 text-dark-800 py-3 px-4 rounded-lg focus:outline-none focus:ring-2 focus:ring-gold-500/50 transition-all cursor-pointer" 
              />
            </div>
          </div>

          <!-- Period Summary -->
          <div class="mt-4 pt-4 border-t border-gold-200">
            <div class="flex items-center justify-between text-sm">
              <span class="text-dark-600">Período seleccionado:</span>
              <span class="text-dark-800 font-semibold">{{ daysDifference }} días</span>
            </div>
          </div>
        </div>

        <template v-if="barberStats.length || serviceStats.length || summary.totalAppointments > 0">
          <!-- Summary Cards -->
          <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 md:gap-6">
          <div class="bg-white border border-blue-300 rounded-xl p-6 shadow-lg">
            <div class="flex items-center justify-between mb-3">
              <CalendarDaysIcon class="w-8 h-8 text-blue-600" />
              <span class="text-blue-600 text-sm font-semibold">Total</span>
            </div>
            <div class="text-3xl font-bold text-dark-800 mb-1">{{ summary.totalAppointments }}</div>
            <div class="text-dark-600 text-sm">Turnos totales</div>
          </div>

          <div class="bg-white border border-green-300 rounded-xl p-6 shadow-lg">
            <div class="flex items-center justify-between mb-3">
              <CurrencyDollarIcon class="w-8 h-8 text-green-600" />
              <span class="text-green-600 text-sm font-semibold">Ingresos</span>
            </div>
            <div class="text-3xl font-bold text-dark-800 mb-1">${{ summary.totalRevenue.toFixed(2) }}</div>
            <div class="text-dark-600 text-sm">Facturación total</div>
          </div>

          <div class="bg-white border border-gold-300 rounded-xl p-6 shadow-lg">
            <div class="flex items-center justify-between mb-3">
              <UserGroupIcon class="w-8 h-8 text-gold-600" />
              <span class="text-gold-600 text-sm font-semibold">Barberos</span>
            </div>
            <div class="text-3xl font-bold text-dark-800 mb-1">{{ barberStats.length }}</div>
            <div class="text-dark-600 text-sm">Profesionales activos</div>
          </div>

          <div class="bg-white border border-orange-300 rounded-xl p-6 shadow-lg">
            <div class="flex items-center justify-between mb-3">
              <ChartBarIcon class="w-8 h-8 text-orange-600" />
              <span class="text-orange-600 text-sm font-semibold">Promedio</span>
            </div>
            <div class="text-3xl font-bold text-dark-800 mb-1">${{ summary.averageTicket.toFixed(2) }}</div>
            <div class="text-dark-600 text-sm">Ticket promedio</div>
          </div>
        </div>

        <!-- Charts Section -->
        <div class="grid lg:grid-cols-2 gap-6">
          <!-- Barbers Performance Chart -->
          <div class="bg-white border border-gold-200 rounded-xl overflow-hidden shadow-lg">
            <div class="bg-gradient-to-r from-gold-500 to-gold-600 px-4 py-3 md:px-6 md:py-4">
              <div class="flex items-center gap-2 md:gap-3">
                <UserGroupIcon class="w-5 h-5 md:w-6 md:h-6 text-white" />
                <div>
                  <h2 class="text-lg md:text-xl font-bold text-white">Rendimiento por Barbero</h2>
                  <p class="text-white/90 text-xs md:text-sm">Turnos realizados</p>
                </div>
              </div>
            </div>
            
            <div class="p-4 md:p-6 space-y-4">
              <div v-for="barber in barberStats" :key="barber.barber_id" class="space-y-2">
                <div class="flex items-center justify-between text-sm">
                  <span class="text-dark-700 font-semibold">{{ barber.barber_name }}</span>
                  <span class="text-dark-800 font-bold">{{ barber.total_appointments }} turnos</span>
                </div>
                <div class="relative h-10 bg-gray-100 rounded-full overflow-hidden border border-gray-200">
                  <div 
                    class="absolute h-full bg-gradient-to-r from-purple-500 to-purple-600 rounded-full transition-all duration-500"
                    :style="{ width: `${(barber.total_appointments / maxBarberAppointments) * 100}%` }"
                  ></div>
                  <div class="absolute inset-0 flex items-center justify-between px-4 text-xs">
                    <span class="text-white font-bold drop-shadow-md">
                      ${{ parseFloat(barber.total_revenue).toFixed(2) }}
                    </span>
                    <span class="text-white font-bold drop-shadow-md">
                      {{ barber.confirmed_appointments }} confirmados
                    </span>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Services Performance Chart -->
          <div class="bg-white border border-gold-200 rounded-xl overflow-hidden shadow-lg">
            <div class="bg-gradient-to-r from-indigo-600 to-indigo-700 px-4 py-3 md:px-6 md:py-4">
              <div class="flex items-center gap-2 md:gap-3">
                <ChartBarIcon class="w-5 h-5 md:w-6 md:h-6 text-white" />
                <div>
                  <h2 class="text-lg md:text-xl font-bold text-white">Servicios Más Solicitados</h2>
                  <p class="text-white/90 text-xs md:text-sm">Popularidad de servicios</p>
                </div>
              </div>
            </div>
            
            <div class="p-4 md:p-6 space-y-4">
              <div v-for="service in serviceStats" :key="service.service_id" class="space-y-2">
                <div class="flex items-center justify-between text-sm">
                  <span class="text-dark-700 font-semibold">{{ service.service_name }}</span>
                  <span class="text-dark-800 font-bold">{{ service.total_appointments }} veces</span>
                </div>
                <div class="relative h-10 bg-gray-100 rounded-full overflow-hidden border border-gray-200">
                  <div 
                    class="absolute h-full bg-gradient-to-r from-indigo-500 to-indigo-600 rounded-full transition-all duration-500"
                    :style="{ width: `${(service.total_appointments / maxServiceAppointments) * 100}%` }"
                  ></div>
                  <div class="absolute inset-0 flex items-center justify-between px-4 text-xs">
                    <span class="text-white font-bold drop-shadow-md">
                      ${{ parseFloat(service.total_revenue).toFixed(2) }}
                    </span>
                    <span class="text-white font-bold drop-shadow-md">
                      ${{ (parseFloat(service.total_revenue) / service.total_appointments).toFixed(2) }} promedio
                    </span>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Detailed Table -->
        <div class="bg-white border border-gold-200 rounded-xl overflow-hidden shadow-lg">
          <div class="bg-gradient-to-r from-gold-500 to-gold-600 px-4 py-3 md:px-6 md:py-4">
            <div class="flex items-center gap-2 md:gap-3">
              <DocumentTextIcon class="w-5 h-5 md:w-6 md:h-6 text-white" />
              <div>
                <h2 class="text-lg md:text-xl font-bold text-white">Detalle Completo por Barbero</h2>
                <p class="text-white/90 text-xs md:text-sm">Desglose de estadísticas</p>
              </div>
            </div>
          </div>

          <!-- Desktop Table -->
          <div class="hidden md:block overflow-x-auto">
            <table class="min-w-full">
              <thead>
                <tr class="bg-gray-100 text-dark-700 text-xs uppercase tracking-wider">
                  <th class="py-3 px-4 text-left font-semibold">Barbero</th>
                  <th class="py-3 px-4 text-center font-semibold">Total Turnos</th>
                  <th class="py-3 px-4 text-center font-semibold">Confirmados</th>
                  <th class="py-3 px-4 text-center font-semibold">Pendientes</th>
                  <th class="py-3 px-4 text-center font-semibold">Cancelados</th>
                  <th class="py-3 px-4 text-right font-semibold">Ingresos Total</th>
                  <th class="py-3 px-4 text-right font-semibold">Promedio</th>
                </tr>
              </thead>
              <tbody class="divide-y divide-gray-200">
                <tr 
                  v-for="barber in barberStats"
                  :key="barber.barber_id"
                  class="hover:bg-gold-50 transition-colors"
                >
                  <td class="py-4 px-4 text-dark-800 font-bold">{{ barber.barber_name }}</td>
                  <td class="py-4 px-4 text-center text-dark-700 font-semibold">{{ barber.total_appointments }}</td>
                  <td class="py-4 px-4 text-center text-green-600 font-semibold">{{ barber.confirmed_appointments }}</td>
                  <td class="py-4 px-4 text-center text-yellow-600 font-semibold">{{ barber.pending_appointments }}</td>
                  <td class="py-4 px-4 text-center text-red-600 font-semibold">{{ barber.cancelled_appointments }}</td>
                  <td class="py-4 px-4 text-right text-dark-800 font-bold">${{ parseFloat(barber.total_revenue).toFixed(2) }}</td>
                  <td class="py-4 px-4 text-right text-dark-700 font-semibold">${{ (parseFloat(barber.total_revenue) / barber.total_appointments).toFixed(2) }}</td>
                </tr>
              </tbody>
            </table>
          </div>

          <!-- Mobile Cards -->
          <div class="md:hidden space-y-3 p-4">
            <div
              v-for="barber in barberStats"
              :key="barber.barber_id"
              class="bg-gray-50 border border-gold-200 rounded-lg p-4"
            >
              <div class="text-dark-800 font-bold text-lg mb-3">{{ barber.barber_name }}</div>
              
              <div class="grid grid-cols-2 gap-3 text-sm">
                <div>
                  <div class="text-dark-600 text-xs uppercase mb-1 font-semibold">Total Turnos</div>
                  <div class="text-dark-800 font-bold">{{ barber.total_appointments }}</div>
                </div>
                <div>
                  <div class="text-dark-600 text-xs uppercase mb-1 font-semibold">Confirmados</div>
                  <div class="text-green-600 font-bold">{{ barber.confirmed_appointments }}</div>
                </div>
                <div>
                  <div class="text-dark-600 text-xs uppercase mb-1 font-semibold">Pendientes</div>
                  <div class="text-yellow-600 font-bold">{{ barber.pending_appointments }}</div>
                </div>
                <div>
                  <div class="text-dark-600 text-xs uppercase mb-1 font-semibold">Cancelados</div>
                  <div class="text-red-600 font-bold">{{ barber.cancelled_appointments }}</div>
                </div>
                <div>
                  <div class="text-dark-600 text-xs uppercase mb-1 font-semibold">Ingresos</div>
                  <div class="text-dark-800 font-bold">${{ parseFloat(barber.total_revenue).toFixed(2) }}</div>
                </div>
                <div>
                  <div class="text-dark-600 text-xs uppercase mb-1 font-semibold">Promedio</div>
                  <div class="text-dark-700 font-bold">${{ (parseFloat(barber.total_revenue) / barber.total_appointments).toFixed(2) }}</div>
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
    
    if (!response.ok) {
      const errorText = await response.text()
      throw new Error(`Error al exportar: ${errorText}`)
    }
    
    // Crear blob con tipo CSV
    const blob = await response.blob()
    const url = window.URL.createObjectURL(new Blob([blob], { type: 'text/csv;charset=utf-8;' }))
    const a = document.createElement('a')
    a.href = url
    a.download = `reporte_barberia_${filters.value.startDate}_${filters.value.endDate}.csv`
    document.body.appendChild(a)
    a.click()
    
    // Limpiar después de un pequeño delay
    setTimeout(() => {
      window.URL.revokeObjectURL(url)
      document.body.removeChild(a)
    }, 100)
    
    alert('Reporte exportado exitosamente')
  } catch (error) {
    console.error('Error exportando:', error)
    alert('Error al exportar el reporte: ' + error.message)
  } finally {
    exporting.value = false
  }
}

onMounted(loadReports)
</script>
