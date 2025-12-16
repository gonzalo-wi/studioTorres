<script setup>
import Card from '@/components/Card.vue'
import { RouterLink } from 'vue-router'
import BaseButton from '@/components/BaseButton.vue'

const services = [
  {
    id: 'corte',
    name: 'Corte de Cabello',
    duration: 30,
    price: '$3500',
    description: 'Corte personalizado según tu estilo. Incluye lavado, corte y terminación con secador.',
    features: ['Corte personalizado', 'Terminación', 'Productos premium']
  },
  {
    id: 'corte-barba',
    name: 'Corte + Barba',
    duration: 60,
    price: '$5500',
    description: 'Servicio completo: corte de cabello + perfilado y arreglo de barba profesional.',
    features: ['Corte personalizado', 'Perfilado de barba', 'Productos premium'],
    featured: true
  },
  {
    id: 'barba',
    name: 'Arreglo de Barba',
    duration: 30,
    price: '$2500',
    description: 'Perfilado, recorte y arreglo profesional de barba con productos de calidad.',
    features: ['Perfilado profesional', 'Recorte', 'Productos premium']
  },
  {
    id: 'afeitado',
    name: 'Afeitado Tradicional',
    duration: 30,
    price: '$3000',
    description: 'Experiencia de barbería clásica con toalla caliente y afeitado con navaja.',
    features: ['Toalla caliente', 'Afeitado con navaja', 'Tratamiento post-afeitado']
  }
]
</script>

<template>
  <div class="py-12">
    <div class="container mx-auto px-4">
      <!-- Header -->
      <div class="text-center mb-16">
        <h1 class="text-5xl font-display font-bold text-white mb-4">
          Nuestros Servicios
        </h1>
        <p class="text-gray-400 text-lg max-w-2xl mx-auto">
          Servicios profesionales diseñados para realzar tu estilo personal. 
          Calidad garantizada en cada detalle.
        </p>
      </div>

      <!-- Services Grid -->
      <div class="grid grid-cols-1 md:grid-cols-2 gap-8 max-w-6xl mx-auto mb-12">
        <div
          v-for="service in services"
          :key="service.id"
          class="relative"
        >
          <!-- Featured Badge - Fuera de la tarjeta -->
          <div v-if="service.featured" class="absolute -top-4 left-1/2 transform -translate-x-1/2 z-20">
            <span class="bg-primary-600 text-white text-xs font-bold px-3 py-1 rounded-full shadow-lg">
              MÁS POPULAR
            </span>
          </div>

          <Card
            hover
            :class="[
              service.featured && 'ring-2 ring-primary-600'
            ]"
          >
            <div class="p-6">
              <!-- Header -->
              <div class="flex justify-between items-start mb-4">
                <div class="flex-1">
                  <h3 class="text-2xl font-display font-bold text-white mb-1">
                    {{ service.name }}
                  </h3>
                  <p class="text-gray-400 text-sm">{{ service.duration }} minutos</p>
                </div>
                <div class="text-right ml-4">
                  <p class="text-3xl font-bold text-primary-500">{{ service.price }}</p>
                </div>
              </div>

            <!-- Description -->
            <p class="text-gray-300 mb-4">
              {{ service.description }}
            </p>

            <!-- Features -->
            <ul class="space-y-2 mb-6">
              <li
                v-for="(feature, index) in service.features"
                :key="index"
                class="flex items-center text-gray-300 text-sm"
              >
                <svg class="w-5 h-5 text-primary-500 mr-2 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                  <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                </svg>
                {{ feature }}
              </li>
            </ul>

            <!-- CTA -->
            <RouterLink to="/book" class="block">
              <BaseButton full-width>
                Reservar este servicio
              </BaseButton>
            </RouterLink>
          </div>
        </Card>
        </div>
      </div>

      <!-- Info Section -->
      <div class="max-w-4xl mx-auto">
        <Card class="p-8 text-center">
          <h2 class="text-2xl font-display font-bold text-white mb-4">
            ¿Necesitás ayuda para elegir?
          </h2>
          <p class="text-gray-300 mb-6">
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
