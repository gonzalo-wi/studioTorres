<script setup>
import { ref, onMounted } from 'vue'
import { useRoute, RouterLink } from 'vue-router'
import BaseButton from '@/components/BaseButton.vue'
import Card from '@/components/Card.vue'
import { fetchBookingById } from '@/services/bookingsService'
import { formatDate } from '@/utils/dateHelpers'

const route = useRoute()
const booking = ref(null)
const loading = ref(true)
const error = ref(null)

onMounted(async () => {
  const bookingId = route.query.id

  if (!bookingId) {
    error.value = 'No se encontró el ID de la reserva'
    loading.value = false
    return
  }

  try {
    booking.value = await fetchBookingById(bookingId)
  } catch (err) {
    error.value = 'No se pudo cargar la información de la reserva'
    console.error(err)
  } finally {
    loading.value = false
  }
})
</script>

<template>
  <div class="py-12 min-h-[80vh] flex items-center justify-center">
    <div class="container mx-auto px-4 max-w-2xl">
      <!-- Loading -->
      <div v-if="loading" class="text-center">
        <div class="inline-block animate-spin text-6xl mb-4">⏳</div>
        <p class="text-gray-400 text-lg">Cargando información...</p>
      </div>

      <!-- Error -->
      <Card v-else-if="error" class="p-8 text-center">
        <div class="text-6xl mb-4">❌</div>
        <h1 class="text-3xl font-display font-bold text-white mb-4">
          Error
        </h1>
        <p class="text-gray-400 mb-8">{{ error }}</p>
        <RouterLink to="/">
          <BaseButton>Volver al inicio</BaseButton>
        </RouterLink>
      </Card>

      <!-- Success -->
      <Card v-else-if="booking" class="p-8">
        <div class="text-center mb-8">
          <div class="inline-flex items-center justify-center w-20 h-20 bg-green-500/10 rounded-full mb-6">
            <svg class="w-10 h-10 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
            </svg>
          </div>
          
          <h1 class="text-4xl font-display font-bold text-white mb-4">
            ¡Reserva Confirmada!
          </h1>
          
          <p class="text-gray-400 text-lg mb-2">
            Tu turno fue registrado exitosamente
          </p>
          <p class="text-sm text-gray-500">
            ID de reserva: #{{ booking.id }}
          </p>
        </div>

        <!-- Booking Details -->
        <div class="bg-dark-800 rounded-lg p-6 mb-8 space-y-4">
          <div class="flex justify-between items-center py-3 border-b border-dark-700">
            <span class="text-gray-400">Servicio:</span>
            <span class="text-white font-semibold">{{ booking.serviceName }}</span>
          </div>
          
          <div class="flex justify-between items-center py-3 border-b border-dark-700">
            <span class="text-gray-400">Fecha:</span>
            <span class="text-white font-semibold">{{ formatDate(booking.date) }}</span>
          </div>
          
          <div class="flex justify-between items-center py-3 border-b border-dark-700">
            <span class="text-gray-400">Hora:</span>
            <span class="text-white font-semibold">{{ booking.time }}</span>
          </div>
          
          <div class="flex justify-between items-center py-3 border-b border-dark-700">
            <span class="text-gray-400">Nombre:</span>
            <span class="text-white font-semibold">{{ booking.name }}</span>
          </div>
          
          <div class="flex justify-between items-center py-3">
            <span class="text-gray-400">Teléfono:</span>
            <span class="text-white font-semibold">{{ booking.phone }}</span>
          </div>

          <div v-if="booking.observations" class="pt-3 border-t border-dark-700">
            <span class="text-gray-400 block mb-2">Observaciones:</span>
            <span class="text-white">{{ booking.observations }}</span>
          </div>
        </div>

        <!-- Important Info -->
        <div class="bg-primary-600/10 border border-primary-600/20 rounded-lg p-6 mb-8">
          <h3 class="text-white font-semibold mb-3 flex items-center">
            <svg class="w-5 h-5 mr-2 text-primary-500" fill="currentColor" viewBox="0 0 20 20">
              <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
            </svg>
            Información importante
          </h3>
          <ul class="space-y-2 text-sm text-gray-300">
            <li>• Llegá 5 minutos antes de tu horario</li>
            <li>• Si no podés asistir, avisá con anticipación</li>
            <li>• Guardá el ID de tu reserva: <strong>#{{ booking.id }}</strong></li>
          </ul>
        </div>

        <!-- Actions -->
        <div class="flex flex-col sm:flex-row gap-4">
          <RouterLink to="/" class="flex-1">
            <BaseButton variant="secondary" full-width>
              Volver al inicio
            </BaseButton>
          </RouterLink>
          
          <a
            href="https://wa.me/5491234567890?text=Hola! Tengo una reserva (ID: #{{ booking.id }})"
            target="_blank"
            rel="noopener noreferrer"
            class="flex-1"
          >
            <BaseButton full-width>
              💬 Contactar por WhatsApp
            </BaseButton>
          </a>
        </div>
      </Card>
    </div>
  </div>
</template>
