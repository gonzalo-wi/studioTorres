<script setup>
import { ref, computed, onMounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import BaseButton from '@/components/BaseButton.vue'
import Card from '@/components/Card.vue'
import BadgeStatus from '@/components/BadgeStatus.vue'
import Modal from '@/components/Modal.vue'
import { fetchBookingById, updateBookingStatus, confirmBooking, cancelBooking } from '@/services/bookingsService'
import { formatDate } from '@/utils/dateHelpers'
import { CheckCircleIcon, XCircleIcon, PhoneIcon, ChatBubbleLeftIcon, ClockIcon } from '@heroicons/vue/24/outline'

const route = useRoute()
const router = useRouter()

const booking = ref(null)
const loading = ref(true)
const actionLoading = ref(false)
const showCancelModal = ref(false)

onMounted(async () => {
  try {
    booking.value = await fetchBookingById(route.params.id)
  } catch (error) {
    console.error('Error cargando turno:', error)
    alert('No se pudo cargar el turno')
    router.push('/admin/bookings')
  } finally {
    loading.value = false
  }
})

const handleConfirm = async () => {
  if (!confirm('¿Confirmar este turno?')) return

  actionLoading.value = true
  try {
    booking.value = await confirmBooking(booking.value.id)
    alert('Turno confirmado exitosamente')
  } catch (error) {
    alert('Error al confirmar el turno')
    console.error(error)
  } finally {
    actionLoading.value = false
  }
}

const handleCancel = async () => {
  showCancelModal.value = false
  actionLoading.value = true
  
  try {
    booking.value = await cancelBooking(booking.value.id)
    alert('Turno cancelado')
  } catch (error) {
    alert('Error al cancelar el turno')
    console.error(error)
  } finally {
    actionLoading.value = false
  }
}

const goBack = () => {
  router.push('/admin/bookings')
}

const canConfirm = computed(() => 
  booking.value && booking.value.status === 'PENDING'
)

const canCancel = computed(() => 
  booking.value && ['PENDING', 'CONFIRMED'].includes(booking.value.status)
)
</script>

<template>
  <div>
    <!-- Header -->
    <div class="mb-8 flex items-center justify-between">
      <div>
        <button
          @click="goBack"
          class="text-gray-400 hover:text-white mb-2 flex items-center text-sm"
        >
          <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
          </svg>
          Volver a turnos
        </button>
        <h1 class="text-3xl font-display font-bold text-white">
          Detalle del Turno
        </h1>
      </div>
    </div>

    <!-- Loading -->
    <div v-if="loading" class="text-center py-12">
      <ClockIcon class="w-12 h-12 mx-auto mb-4 text-gray-400 animate-pulse" />
      <p class="text-gray-400">Cargando información...</p>
    </div>

    <!-- Content -->
    <div v-else-if="booking" class="grid grid-cols-1 lg:grid-cols-3 gap-6">
      <!-- Main Info -->
      <div class="lg:col-span-2 space-y-6">
        <!-- Status & Actions -->
        <Card class="p-6">
          <div class="flex items-center justify-between mb-6">
            <div>
              <h2 class="text-xl font-display font-bold text-white mb-2">
                Estado de la Reserva
              </h2>
              <BadgeStatus :status="booking.status" />
            </div>
            <div class="text-gray-400 text-sm text-right">
              <p>ID: <span class="font-mono text-white">#{{ booking.id }}</span></p>
              <p v-if="booking.createdAt" class="mt-1">
                Creado: {{ new Date(booking.createdAt).toLocaleString('es-AR') }}
              </p>
            </div>
          </div>

          <div class="flex gap-3">
            <BaseButton
              v-if="canConfirm"
              @click="handleConfirm"
              :loading="actionLoading"
              :disabled="actionLoading"
              class="flex items-center gap-2"
            >
              <CheckCircleIcon class="w-5 h-5" />
              Confirmar Turno
            </BaseButton>

            <BaseButton
              v-if="canCancel"
              variant="danger"
              @click="showCancelModal = true"
              :disabled="actionLoading"
              class="flex items-center gap-2"
            >
              <XCircleIcon class="w-5 h-5" />
              Cancelar Turno
            </BaseButton>

            <BaseButton
              v-if="booking.status === 'CANCELLED'"
              variant="secondary"
              disabled
            >
              Turno Cancelado
            </BaseButton>
          </div>
        </Card>

        <!-- Booking Details -->
        <Card class="p-6">
          <h2 class="text-xl font-display font-bold text-white mb-6">
            Información del Turno
          </h2>

          <div class="space-y-4">
            <div class="flex py-3 border-b border-dark-800">
              <div class="w-1/3 text-gray-400">Servicio:</div>
              <div class="w-2/3 text-white font-semibold">{{ booking.serviceName }}</div>
            </div>

            <div class="flex py-3 border-b border-dark-800">
              <div class="w-1/3 text-gray-400">Fecha:</div>
              <div class="w-2/3 text-white font-semibold">{{ formatDate(booking.date) }}</div>
            </div>

            <div class="flex py-3 border-b border-dark-800">
              <div class="w-1/3 text-gray-400">Hora:</div>
              <div class="w-2/3 text-white font-semibold text-lg">{{ booking.time }}</div>
            </div>

            <div v-if="booking.observations" class="flex py-3">
              <div class="w-1/3 text-gray-400">Observaciones:</div>
              <div class="w-2/3 text-white">{{ booking.observations }}</div>
            </div>
          </div>
        </Card>
      </div>

      <!-- Client Info -->
      <div class="space-y-6">
        <Card class="p-6">
          <h2 class="text-xl font-display font-bold text-white mb-6">
            Datos del Cliente
          </h2>

          <div class="space-y-4">
            <div>
              <p class="text-gray-400 text-sm mb-1">Nombre:</p>
              <p class="text-white font-semibold text-lg">{{ booking.name }}</p>
            </div>

            <div>
              <p class="text-gray-400 text-sm mb-1">Teléfono:</p>
              <p class="text-white font-semibold">{{ booking.phone }}</p>
              <a
                :href="`https://wa.me/${booking.phone.replace(/\D/g, '')}`"
                target="_blank"
                rel="noopener noreferrer"
                class="text-primary-500 hover:text-primary-400 text-sm mt-2 inline-flex items-center gap-1"
              >
                <ChatBubbleLeftIcon class="w-4 h-4" />
                Contactar por WhatsApp →
              </a>
            </div>
          </div>
        </Card>

        <!-- Quick Actions -->
        <Card class="p-6 bg-dark-800 border-dark-700">
          <h3 class="text-white font-semibold mb-4">Acciones Rápidas</h3>
          <div class="space-y-3">
            <a
              :href="`tel:${booking.phone}`"
              class="flex items-center justify-center gap-2 w-full px-4 py-3 bg-dark-900 hover:bg-dark-700 text-white rounded-lg transition-colors"
            >
              <PhoneIcon class="w-5 h-5" />
              Llamar
            </a>
            <a
              :href="`https://wa.me/${booking.phone.replace(/\D/g, '')}?text=Hola ${booking.name}, te contacto desde Hernán Barber respecto a tu turno del ${booking.date} a las ${booking.time}.`"
              target="_blank"
              rel="noopener noreferrer"
              class="flex items-center justify-center gap-2 w-full px-4 py-3 bg-dark-900 hover:bg-dark-700 text-white rounded-lg transition-colors"
            >
              <ChatBubbleLeftIcon class="w-5 h-5" />
              WhatsApp
            </a>
          </div>
        </Card>
      </div>
    </div>

    <!-- Cancel Modal -->
    <Modal
      :show="showCancelModal"
      @close="showCancelModal = false"
      title="Cancelar Turno"
    >
      <p class="text-gray-300 mb-6">
        ¿Estás seguro que querés cancelar este turno? Esta acción no se puede deshacer.
      </p>

      <div class="flex gap-3 justify-end">
        <BaseButton
          variant="secondary"
          @click="showCancelModal = false"
        >
          No, mantener turno
        </BaseButton>
        <BaseButton
          variant="danger"
          @click="handleCancel"
          :loading="actionLoading"
        >
          Sí, cancelar
        </BaseButton>
      </div>
    </Modal>
  </div>
</template>
