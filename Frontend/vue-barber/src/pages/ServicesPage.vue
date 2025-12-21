<script setup>
import { ref, onMounted } from 'vue'
import Card from '@/components/Card.vue'
import { RouterLink } from 'vue-router'
import BaseButton from '@/components/BaseButton.vue'
import LoadingSpinner from '@/components/LoadingSpinner.vue'
import { fetchServices } from '@/services/servicesService'

const services = ref([])
const loading = ref(true)
const error = ref(null)

onMounted(async () => {
  try {
    loading.value = true
    const data = await fetchServices()
    services.value = data
  } catch (err) {
    console.error('Error loading services:', err)
    error.value = 'No se pudieron cargar los servicios. Por favor, intentá nuevamente.'
  } finally {
    loading.value = false
  }
})

// Helper function to format price
const formatPrice = (price) => {
  return new Intl.NumberFormat('es-AR', {
    style: 'currency',
    currency: 'ARS',
    minimumFractionDigits: 0
  }).format(price)
}
</script>

<template>
  <div class="py-12">
    <div class="container mx-auto px-4">
      <!-- Header -->
      <div class="text-center mb-16">
        <h1 class="text-5xl font-display font-bold text-dark-900 mb-4">
          Nuestros Servicios
        </h1>
        <p class="text-dark-600 text-lg max-w-2xl mx-auto">
          Servicios profesionales diseñados para realzar tu estilo personal. 
          Calidad garantizada en cada detalle.
        </p>
      </div>

      <!-- Loading State -->
      <div v-if="loading" class="text-center py-12">
        <LoadingSpinner size="lg" text="Cargando servicios..." />
      </div>

      <!-- Error State -->
      <div v-else-if="error" class="max-w-2xl mx-auto">
        <Card class="p-8 text-center">
          <p class="text-red-400 mb-4">{{ error }}</p>
          <BaseButton @click="() => window.location.reload()">
            Reintentar
          </BaseButton>
        </Card>
      </div>

      <!-- Services Grid -->
      <div v-else-if="services.length > 0" class="grid grid-cols-1 md:grid-cols-2 gap-8 max-w-6xl mx-auto mb-12">
        <div
          v-for="service in services"
          :key="service.id"
          class="relative"
        >
          <!-- Featured Badge -->
          <div v-if="service.is_featured" class="absolute -top-4 left-1/2 transform -translate-x-1/2 z-20">
            <span class="bg-gradient-to-r from-gold-500 to-gold-600 text-white text-xs font-bold px-4 py-1.5 rounded-full shadow-lg border-2 border-white">
              ⭐ MÁS POPULAR
            </span>
          </div>

          <Card
            hover
            :class="[
              'relative overflow-hidden',
              service.is_featured && 'ring-2 ring-gold-400 shadow-xl shadow-gold-500/20'
            ]"
          >
            <!-- Gradient background for featured -->
            <div v-if="service.is_featured" class="absolute inset-0 bg-gradient-to-br from-gold-50 via-white to-gold-50 opacity-50"></div>
            
            <div class="p-8 relative z-10">
              <!-- Header -->
              <div class="flex justify-between items-start mb-6">
                <div class="flex-1">
                  <h3 class="text-2xl font-display font-bold text-dark-900 mb-2">
                    {{ service.title }}
                  </h3>
                  <p class="text-dark-600 text-sm flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    {{ service.duration_minutes }} minutos
                  </p>
                </div>
                <div class="text-right ml-4">
                  <p class="text-sm text-dark-600 font-semibold mb-1">Precio</p>
                  <p class="text-3xl font-bold bg-gradient-to-r from-gold-600 to-gold-700 bg-clip-text text-transparent">{{ formatPrice(service.price) }}</p>
                </div>
              </div>

            <!-- Description -->
            <p class="text-dark-700 mb-6 leading-relaxed">
              {{ service.description }}
            </p>

            <!-- CTA -->
            <RouterLink :to="{ name: 'Book', query: { service: service.id } }" class="block">
              <BaseButton full-width>
                Reservar este servicio
              </BaseButton>
            </RouterLink>
          </div>
        </Card>
        </div>
      </div>

      <!-- Empty State -->
      <div v-else class="max-w-2xl mx-auto">
        <Card class="p-8 text-center">
          <p class="text-gray-400">No hay servicios disponibles en este momento.</p>
        </Card>
      </div>

      <!-- Info Section -->
      <div class="max-w-4xl mx-auto">
        <Card class="p-8 text-center bg-gradient-to-br from-gold-50 to-white">
          <h2 class="text-2xl font-display font-bold text-dark-900 mb-4">
            ¿Necesitás ayuda para elegir?
          </h2>
          <p class="text-dark-700 mb-6">
            Si no estás seguro qué servicio elegir, podés consultarnos por WhatsApp 
            o venir directamente. Te asesoramos sin compromiso.
          </p>
          <div class="flex flex-col sm:flex-row gap-4 justify-center">
            <a href="https://wa.me/5491151046978?text=Hola!%20Necesito%20ayuda%20para%20elegir%20un%20servicio." target="_blank" rel="noopener noreferrer">
              <BaseButton variant="outline">
                💬 Consultar por WhatsApp
              </BaseButton>
            </a>
            <RouterLink to="/book">
              <BaseButton>
                Reservar Turno
              </BaseButton>
            </RouterLink>
          </div>
        </Card>
      </div>
    </div>
  </div>
</template>
