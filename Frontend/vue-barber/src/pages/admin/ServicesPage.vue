<template>
  <div class="min-h-screen bg-gray-50 py-8 px-4">
    <div class="mx-auto">
      <!-- Header -->
      <div class="flex justify-between items-center mb-8">
        <div>
          <h1 class="text-3xl font-display font-bold text-dark-800 mb-2">
            Gestión de Servicios
          </h1>
          <p class="text-dark-600">
            Administrá los servicios disponibles en la barbería
          </p>
        </div>
        <BaseButton @click="openCreateModal">
          <PlusIcon class="w-5 h-5 mr-2" />
          Nuevo Servicio
        </BaseButton>
      </div>

      <!-- Services Grid -->
      <div v-if="!loading && services.length > 0" class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
        <div
          v-for="service in services"
          :key="service.id"
          class="bg-white border border-gold-200 rounded-xl overflow-hidden hover:border-gold-500 transition-all duration-300 hover:shadow-xl group"
        >
          <!-- Service Header -->
          <div class="bg-gradient-to-r from-gold-500/10 to-gold-600/10 border-b border-gold-200 p-6">
            <div class="flex items-start justify-between mb-3">
              <h3 class="text-xl font-bold text-dark-800">{{ service.title }}</h3>
              <span
                :class="[
                  'px-3 py-1 rounded-full text-xs font-semibold',
                  service.active 
                    ? 'bg-green-100 text-green-700 border border-green-300'
                    : 'bg-gray-100 text-gray-600 border border-gray-300'
                ]"
              >
                {{ service.active ? 'Activo' : 'Inactivo' }}
              </span>
            </div>
            <p class="text-dark-600 text-sm line-clamp-2">
              {{ service.description || 'Sin descripción' }}
            </p>
          </div>

          <!-- Service Details -->
          <div class="p-6 space-y-4">
            <!-- Duration -->
            <div class="flex items-center justify-between bg-gray-50 rounded-lg p-3 border border-gold-200">
              <div class="flex items-center gap-2 text-dark-600">
                <ClockIcon class="w-5 h-5" />
                <span class="text-sm font-medium">Duración</span>
              </div>
              <span class="text-dark-800 font-bold">{{ service.duration_minutes }} min</span>
            </div>

            <!-- Price -->
            <div class="flex items-center justify-between bg-gradient-to-r from-gold-50 to-gold-100/50 rounded-lg p-3 border border-gold-300">
              <div class="flex items-center gap-2 text-gold-700">
                <CurrencyDollarIcon class="w-5 h-5" />
                <span class="text-sm font-medium">Precio</span>
              </div>
              <span class="text-dark-800 font-bold text-lg">${{ service.price }}</span>
            </div>

            <!-- Actions -->
            <div class="flex gap-2 pt-2">
              <button
                @click="openEditModal(service)"
                class="flex-1 bg-gold-500 hover:bg-gold-600 text-white font-semibold py-2.5 px-4 rounded-lg transition-all duration-200 flex items-center justify-center gap-2"
              >
                <PencilIcon class="w-4 h-4" />
                Editar
              </button>
              <button
                @click="confirmDelete(service)"
                class="bg-red-100 hover:bg-red-200 text-red-600 hover:text-red-700 p-2.5 rounded-lg transition-colors"
                title="Eliminar"
              >
                <TrashIcon class="w-5 h-5" />
              </button>
            </div>
          </div>
        </div>
      </div>

      <div v-if="!loading && services.length === 0" class="bg-white border border-gold-200 rounded-xl p-12 text-center shadow-lg">
        <div class="w-16 h-16 bg-gradient-to-br from-gold-500 to-gold-600 rounded-full flex items-center justify-center mx-auto mb-4 shadow-lg">
          <PlusIcon class="w-8 h-8 text-white" />
        </div>
        <p class="text-dark-600 text-lg mb-6">No hay servicios creados todavía</p>
        <BaseButton @click="openCreateModal">
          Crear primer servicio
        </BaseButton>
      </div>

      <!-- Loading -->
      <div v-if="loading" class="text-center py-12">
        <p class="text-dark-600">Cargando servicios...</p>
      </div>

      <!-- Service Modal -->
      <Modal
        :show="showModal"
        :title="isEditing ? 'Editar Servicio' : 'Nuevo Servicio'"
        @close="closeModal"
      >
        <form @submit.prevent="saveService" class="space-y-6">
          <BaseInput
            v-model="formData.title"
            label="Nombre del servicio"
            placeholder="Corte de Cabello"
            :error="errors.title"
            required
          />

          <BaseInput
            v-model="formData.description"
            label="Descripción"
            type="textarea"
            placeholder="Descripción del servicio..."
            :error="errors.description"
          />

          <div class="grid grid-cols-2 gap-4">
            <BaseSelect
              v-model.number="formData.duration_minutes"
              label="Duración"
              :options="durationOptions"
              :error="errors.duration_minutes"
              required
            />

            <BaseInput
              v-model.number="formData.price"
              label="Precio"
              type="number"
              step="0.01"
              placeholder="3500"
              :error="errors.price"
              required
            />
          </div>

          <div class="flex items-center gap-3">
            <input
              id="active"
              v-model="formData.active"
              type="checkbox"
              class="w-5 h-5 rounded bg-white border-gold-300 text-gold-600 focus:ring-gold-500"
            />
            <label for="active" class="text-dark-800">
              Servicio activo (visible para clientes)
            </label>
          </div>

          <div class="flex gap-4">
            <BaseButton
              type="button"
              variant="secondary"
              full-width
              @click="closeModal"
            >
              Cancelar
            </BaseButton>
            <BaseButton
              type="submit"
              full-width
              :loading="saving"
              :disabled="saving"
            >
              {{ isEditing ? 'Actualizar' : 'Crear' }} Servicio
            </BaseButton>
          </div>
        </form>
      </Modal>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import Card from '@/components/Card.vue'
import BaseButton from '@/components/BaseButton.vue'
import BaseInput from '@/components/BaseInput.vue'
import BaseSelect from '@/components/BaseSelect.vue'
import Modal from '@/components/Modal.vue'
import { 
  PlusIcon, 
  PencilIcon, 
  TrashIcon, 
  ClockIcon, 
  CurrencyDollarIcon 
} from '@heroicons/vue/24/outline'
import { 
  fetchAdminServices, 
  createService, 
  updateService, 
  deleteService 
} from '@/services/servicesService'

const services = ref([])
const loading = ref(true)
const showModal = ref(false)
const isEditing = ref(false)
const saving = ref(false)

const formData = ref({
  title: '',
  description: '',
  duration_minutes: 30,
  price: 0,
  active: true
})

const errors = ref({
  title: '',
  description: '',
  duration_minutes: '',
  price: ''
})

const durationOptions = [
  { value: 30, label: '30 minutos' },
  { value: 60, label: '60 minutos' }
]

const loadServices = async () => {
  try {
    loading.value = true
    services.value = await fetchAdminServices()
  } catch (error) {
    console.error('Error cargando servicios:', error)
    alert('Error al cargar los servicios')
  } finally {
    loading.value = false
  }
}

const openCreateModal = () => {
  isEditing.value = false
  formData.value = {
    title: '',
    description: '',
    duration_minutes: 30,
    price: 0,
    active: true
  }
  errors.value = {}
  showModal.value = true
}

const openEditModal = (service) => {
  isEditing.value = true
  formData.value = {
    id: service.id,
    title: service.title,
    description: service.description || '',
    duration_minutes: service.duration_minutes,
    price: parseFloat(service.price),
    active: service.active
  }
  errors.value = {}
  showModal.value = true
}

const closeModal = () => {
  showModal.value = false
  formData.value = {
    title: '',
    description: '',
    duration_minutes: 30,
    price: 0,
    active: true
  }
  errors.value = {}
}

const validateForm = () => {
  errors.value = {}
  let isValid = true

  if (!formData.value.title.trim()) {
    errors.value.title = 'El nombre es obligatorio'
    isValid = false
  }

  if (!formData.value.duration_minutes) {
    errors.value.duration_minutes = 'Seleccioná una duración'
    isValid = false
  }

  if (!formData.value.price || formData.value.price <= 0) {
    errors.value.price = 'El precio debe ser mayor a 0'
    isValid = false
  }

  return isValid
}

const saveService = async () => {
  if (!validateForm()) return

  try {
    saving.value = true

    if (isEditing.value) {
      await updateService(formData.value.id, formData.value)
    } else {
      await createService(formData.value)
    }

    await loadServices()
    closeModal()
  } catch (error) {
    console.error('Error guardando servicio:', error)
    if (error.errors) {
      errors.value = error.errors
    } else {
      alert(error.message || 'Error al guardar el servicio')
    }
  } finally {
    saving.value = false
  }
}

const confirmDelete = async (service) => {
  if (!confirm(`¿Estás seguro de eliminar "${service.title}"?`)) {
    return
  }

  try {
    await deleteService(service.id)
    await loadServices()
  } catch (error) {
    console.error('Error eliminando servicio:', error)
    alert(error.message || 'Error al eliminar el servicio')
  }
}

onMounted(() => {
  loadServices()
})
</script>
