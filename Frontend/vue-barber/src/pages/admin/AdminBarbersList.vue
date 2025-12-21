<template>
  <div class="min-h-screen bg-gray-50 py-8 px-4">
    <div class="mx-auto">
      <!-- Header -->
      <div class="flex justify-between items-center mb-8">
        <div>
          <h1 class="text-3xl font-bold text-dark-800 mb-2">
            Gestión de Barberos
          </h1>
          <p class="text-dark-600">
            Administrá el equipo de profesionales
          </p>
        </div>
        <button 
          @click="openCreate"
          class="flex items-center gap-2 bg-gradient-to-r from-gold-500 to-gold-600 hover:from-gold-600 hover:to-gold-700 text-white font-semibold py-3 px-6 rounded-lg shadow-lg transition-all duration-200"
        >
          <UserPlusIcon class="w-5 h-5" />
          Nuevo Barbero
        </button>
      </div>

      <!-- Loading State -->
      <div v-if="loading" class="flex items-center justify-center py-20">
        <div class="text-dark-600 text-lg">Cargando barberos...</div>
      </div>

      <!-- Empty State -->
      <div v-else-if="!barbers.length" class="bg-white border border-gold-200 rounded-xl p-12 text-center shadow-lg">
        <UserGroupIcon class="w-16 h-16 text-gold-500 mx-auto mb-4" />
        <p class="text-dark-600 text-lg mb-6">No hay barberos registrados</p>
        <button 
          @click="openCreate"
          class="bg-gradient-to-r from-gold-500 to-gold-600 hover:from-gold-600 hover:to-gold-700 text-white font-semibold py-3 px-6 rounded-lg transition-colors"
        >
          Crear primer barbero
        </button>
      </div>

      <!-- Barbers Grid -->
      <div v-else class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
        <div 
          v-for="b in barbers" 
          :key="b.id" 
          class="bg-white border border-gold-200 rounded-xl p-6 hover:border-gold-500 transition-all duration-300 hover:shadow-xl group"
        >
          <!-- Barber Info -->
          <div class="flex items-start justify-between mb-6">
            <div class="flex items-center gap-4">
              <div class="w-12 h-12 bg-gradient-to-br from-gold-500 to-gold-600 rounded-full flex items-center justify-center text-white font-bold text-xl shadow-lg">
                {{ b.name.charAt(0).toUpperCase() }}
              </div>
              <div>
                <div class="text-dark-800 font-bold text-lg">{{ b.name }}</div>
                <div class="text-dark-600 text-sm flex items-center gap-2 mt-1">
                  <PhoneIcon class="w-4 h-4" />
                  {{ b.phone || b.email || 'Sin contacto' }}
                </div>
              </div>
            </div>
          </div>

          <!-- Earnings Info -->
          <div class="bg-gray-50 rounded-lg p-4 mb-4 border border-gold-200">
            <div class="text-dark-600 text-xs uppercase tracking-wider mb-2">Ganancias</div>
            <div class="flex items-center justify-between">
              <span class="text-dark-800 font-semibold">
                {{ b.earnings_type === 'FIJO' ? `$${b.earnings_value}` : `${b.earnings_value}%` }}
              </span>
              <span :class="[
                'px-3 py-1 rounded-full text-xs font-semibold',
                b.earnings_type === 'FIJO' 
                  ? 'bg-blue-500/20 text-blue-400'
                  : 'bg-green-500/20 text-green-400'
              ]">
                {{ b.earnings_type }}
              </span>
            </div>
          </div>

          <!-- Actions -->
          <div class="flex gap-2">
            <router-link 
              :to="{ name: 'admin-barber-detail', params: { id: b.id } }" 
              class="flex-1 bg-gold-500 hover:bg-gold-600 text-white font-semibold py-2.5 px-4 rounded-lg transition-colors text-center flex items-center justify-center gap-2"
            >
              <EyeIcon class="w-4 h-4" />
              Ver Detalle
            </router-link>
            <button 
              @click="remove(b.id)" 
              class="bg-red-100 hover:bg-red-200 text-red-600 hover:text-red-700 p-2.5 rounded-lg transition-colors"
              title="Eliminar"
            >
              <TrashIcon class="w-5 h-5" />
            </button>
          </div>
        </div>
      </div>

      <!-- Create Modal -->
      <div v-if="showCreate" class="fixed inset-0 bg-black/60 backdrop-blur-sm flex items-center justify-center z-50 p-4">
        <div class="bg-white border border-gold-200 rounded-xl w-full max-w-lg shadow-2xl">
          <!-- Modal Header -->
          <div class="border-b border-gold-200 p-6">
            <div class="flex items-center justify-between">
              <h2 class="text-2xl font-bold text-dark-800">Nuevo Barbero</h2>
              <button 
                @click="showCreate=false"
                class="text-dark-600 hover:text-dark-800 transition-colors"
              >
                <XMarkIcon class="w-6 h-6" />
              </button>
            </div>
          </div>

          <!-- Modal Body -->
          <form @submit.prevent="submitCreate" class="p-6 space-y-5">
            <div>
              <label class="block text-dark-700 text-sm font-semibold mb-2">
                Nombre Completo *
              </label>
              <input 
                v-model="form.name" 
                placeholder="Juan Pérez" 
                class="w-full bg-white border border-gold-300 focus:border-gold-500 text-dark-800 py-3 px-4 rounded-lg focus:outline-none focus:ring-2 focus:ring-gold-500/50 transition-all" 
                required 
              />
            </div>

            <div>
              <label class="block text-dark-700 text-sm font-semibold mb-2">
                Teléfono
              </label>
              <input 
                v-model="form.phone" 
                placeholder="+54 9 11 1234-5678" 
                class="w-full bg-white border border-gold-300 focus:border-gold-500 text-dark-800 py-3 px-4 rounded-lg focus:outline-none focus:ring-2 focus:ring-gold-500/50 transition-all" 
              />
            </div>

            <div>
              <label class="block text-dark-700 text-sm font-semibold mb-2">
                Email
              </label>
              <input 
                v-model="form.email" 
                type="email"
                placeholder="juan@example.com" 
                class="w-full bg-white border border-gold-300 focus:border-gold-500 text-dark-800 py-3 px-4 rounded-lg focus:outline-none focus:ring-2 focus:ring-gold-500/50 transition-all" 
              />
            </div>

            <div class="pt-4 border-t border-gray-800">
              <label class="block text-gray-300 text-sm font-semibold mb-3">
                Configuración de Ganancias
              </label>
              <div class="grid grid-cols-2 gap-4">
                <select 
                  v-model="form.earnings_type" 
                  class="bg-gray-950 border border-gray-700 focus:border-red-600 text-white py-3 px-4 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-600/50 transition-all"
                >
                  <option value="FIJO">Monto Fijo</option>
                  <option value="PORCENTAJE">Porcentaje</option>
                </select>
                <input 
                  v-model.number="form.earnings_value" 
                  type="number" 
                  min="0" 
                  step="0.01" 
                  placeholder="Valor" 
                  class="bg-gray-950 border border-gray-700 focus:border-red-600 text-white py-3 px-4 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-600/50 transition-all" 
                />
              </div>
            </div>

            <!-- Modal Actions -->
            <div class="flex justify-end gap-3 pt-4">
              <button 
                type="button" 
                class="bg-gray-800 hover:bg-gray-700 text-white font-semibold py-3 px-6 rounded-lg transition-colors" 
                @click="showCreate=false"
              >
                Cancelar
              </button>
              <button 
                type="submit" 
                class="bg-gradient-to-r from-red-600 to-red-700 hover:from-red-700 hover:to-red-800 text-white font-semibold py-3 px-6 rounded-lg shadow-lg shadow-red-600/30 transition-all duration-200"
              >
                Guardar Barbero
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { onMounted, reactive, ref } from 'vue'
import { useBarbers } from '@/composables/useBarbers'
import { 
  UserPlusIcon, 
  UserGroupIcon, 
  PhoneIcon, 
  EyeIcon, 
  TrashIcon,
  XMarkIcon 
} from '@heroicons/vue/24/outline'

const { barbers, loading, loadBarbers, saveBarber, removeBarber } = useBarbers()

const showCreate = ref(false)
const form = reactive({ name: '', phone: '', email: '', earnings_type: 'FIJO', earnings_value: 0 })

function openCreate() { showCreate.value = true }

async function submitCreate() {
  await saveBarber({ ...form, active: true })
  showCreate.value = false
  form.name = ''; form.phone=''; form.email=''; form.earnings_type='FIJO'; form.earnings_value=0
  await loadBarbers()
}

async function remove(id) {
  if (confirm('¿Estás seguro de eliminar este barbero?')) {
    await removeBarber(id)
    await loadBarbers()
  }
}

onMounted(loadBarbers)
</script>

<style scoped></style>
