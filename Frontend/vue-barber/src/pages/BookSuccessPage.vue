<script setup>
import { ref, onMounted } from 'vue'
import { useRoute, RouterLink } from 'vue-router'
import BaseButton from '@/components/BaseButton.vue'
import Card from '@/components/Card.vue'
import LoadingSpinner from '@/components/LoadingSpinner.vue'
import { fetchBookingByCode } from '@/services/bookingsService'
import { formatDate } from '@/utils/dateHelpers'

const route = useRoute()
const booking = ref(null)
const loading = ref(true)
const error = ref(null)

onMounted(async () => {
  const bookingCode = route.query.code

  if (!bookingCode) {
    error.value = 'No se encontró el código de la reserva'
    loading.value = false
    return
  }

  try {
    booking.value = await fetchBookingByCode(bookingCode)
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
      <div v-if="loading" class="text-center py-12">
        <LoadingSpinner size="lg" text="Cargando información..." />
      </div>

      <!-- Error -->
      <Card v-else-if="error" class="p-8 text-center">
        <div class="text-6xl mb-4">❌</div>
        <h1 class="text-3xl font-display font-bold text-dark-900 mb-4">
          Error
        </h1>
        <p class="text-dark-600 mb-8">{{ error }}</p>
        <RouterLink to="/">
          <BaseButton>Volver al inicio</BaseButton>
        </RouterLink>
      </Card>

      <!-- Success -->
      <div v-else-if="booking">
        <!-- Hero Success Card -->
        <Card class="p-10 mb-6 bg-gradient-to-br from-gold-50 via-white to-gold-50 relative overflow-hidden">
          <!-- Decorative elements -->
          <div class="absolute top-0 right-0 w-64 h-64 bg-gold-400 rounded-full opacity-5 blur-3xl"></div>
          <div class="absolute bottom-0 left-0 w-64 h-64 bg-gold-500 rounded-full opacity-5 blur-3xl"></div>
          
          <div class="text-center relative z-10">
            <!-- Check icon with animation -->
            <div class="inline-flex items-center justify-center w-24 h-24 bg-gradient-to-br from-green-400 to-green-600 rounded-full mb-6 shadow-2xl shadow-green-500/40 animate-bounce">
              <svg class="w-12 h-12 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="3">
                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
              </svg>
            </div>
            
            <h1 class="text-5xl font-display font-bold text-dark-900 mb-4">
              ¡Reserva Confirmada!
            </h1>
            
            <p class="text-dark-700 text-xl mb-3 font-medium">
              Tu turno fue registrado exitosamente
            </p>
            <div class="inline-block px-4 py-2 bg-gold-100 border-2 border-gold-300 rounded-lg">
              <p class="text-sm text-gold-800 font-semibold">
                ID de reserva: <span class="text-lg">#{{ booking.id }}</span>
              </p>
            </div>
          </div>
        </Card>
        
        <!-- Booking Details Card -->
        <Card class="p-8 mb-6">
          <h2 class="text-2xl font-display font-bold text-dark-900 mb-6 flex items-center">
            <svg class="w-6 h-6 mr-2 text-gold-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
            </svg>
            Detalles de tu turno
          </h2>
          
          <div class="space-y-4">
            <div class="flex justify-between items-center py-4 border-b-2 border-gold-100">
              <span class="text-dark-600 font-medium flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                Servicio:
              </span>
              <span class="text-dark-900 font-bold text-lg">{{ booking.serviceName }}</span>
            </div>
            
            <div class="flex justify-between items-center py-4 border-b-2 border-gold-100">
              <span class="text-dark-600 font-medium flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
                Fecha:
              </span>
              <span class="text-dark-900 font-bold text-lg">{{ booking.date ? formatDate(booking.date) : 'N/A' }}</span>
            </div>
            
            <div class="flex justify-between items-center py-4 border-b-2 border-gold-100">
              <span class="text-dark-600 font-medium flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                Hora:
              </span>
              <span class="text-dark-900 font-bold text-lg">{{ booking.time || 'N/A' }}</span>
            </div>
            
            <div class="flex justify-between items-center py-4 border-b-2 border-gold-100">
              <span class="text-dark-600 font-medium flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                </svg>
                Nombre:
              </span>
              <span class="text-dark-900 font-bold text-lg">{{ booking.name }}</span>
            </div>
            
            <div class="flex justify-between items-center py-4">
              <span class="text-dark-600 font-medium flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                </svg>
                Teléfono:
              </span>
              <span class="text-dark-900 font-bold text-lg">{{ booking.phone }}</span>
            </div>

            <div v-if="booking.observations" class="pt-4 border-t-2 border-gold-100">
              <span class="text-dark-600 font-medium block mb-2">Observaciones:</span>
              <span class="text-dark-800 bg-gold-50 p-3 rounded-lg block">{{ booking.observations }}</span>
            </div>
          </div>
        </Card>

        <!-- Important Info Card -->
        <Card class="p-6 mb-6 bg-gradient-to-br from-gold-50 to-white border-2 border-gold-300">
          <h3 class="text-dark-900 font-bold text-lg mb-4 flex items-center">
            <svg class="w-6 h-6 mr-2 text-gold-600" fill="currentColor" viewBox="0 0 20 20">
              <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
            </svg>
            Información importante
          </h3>
          <ul class="space-y-3 text-dark-700">
            <li class="flex items-start gap-3">
              <span class="text-gold-600 text-xl font-bold">•</span>
              <span>Llegá <strong>5 minutos antes</strong> de tu horario</span>
            </li>
            <li class="flex items-start gap-3">
              <span class="text-gold-600 text-xl font-bold">•</span>
              <span>Si no podés asistir, <strong>avisá con anticipación</strong></span>
            </li>
            <li class="flex items-start gap-3">
              <span class="text-gold-600 text-xl font-bold">•</span>
              <span>Guardá el ID de tu reserva: <strong class="text-gold-700">#{{ booking.id }}</strong></span>
            </li>
          </ul>
        </Card>

        <!-- Actions -->
        <div class="flex flex-col sm:flex-row gap-4">
          <RouterLink to="/" class="flex-1">
            <BaseButton variant="outline" full-width>
              Volver al inicio
            </BaseButton>
          </RouterLink>
          
          <a
            href="https://wa.me/5491151046978?text=Hola! Tengo una reserva (ID: #{{ booking.id }})"
            target="_blank"
            rel="noopener noreferrer"
            class="flex-1"
          >
            <BaseButton full-width>
              💬 Contactar por WhatsApp
            </BaseButton>
          </a>
        </div>
      </div>
    </div>
  </div>
</template>
