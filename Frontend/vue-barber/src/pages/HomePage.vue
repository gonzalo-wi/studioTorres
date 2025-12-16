<script setup>
import { RouterLink } from 'vue-router'
import BaseButton from '@/components/BaseButton.vue'
import Card from '@/components/Card.vue'

const features = [
  {
    icon: '✂️',
    title: 'Cortes Profesionales',
    description: 'Técnicas modernas y clásicas adaptadas a tu estilo'
  },
  {
    icon: '🪒',
    title: 'Afeitado Tradicional',
    description: 'Experiencia premium con productos de primera calidad'
  },
  {
    icon: '⭐',
    title: 'Atención Personalizada',
    description: 'Cada cliente es único y merece un trato especial'
  },
  {
    icon: '🕐',
    title: 'Horarios Flexibles',
    description: 'Abierto de lunes a sábado para tu comodidad'
  }
]

const testimonials = [
  {
    name: 'Gonzalo Wiñazki',
    rating: 5,
    text: 'Excelente servicio, siempre salgo satisfecho. Hernán es un profesional de verdad.'
  },
  {
    name: 'Martín González',
    rating: 5,
    text: 'El mejor corte que me hice en años. Ambiente agradable y muy prolijo.'
  },
  {
    name: 'Diego Rodríguez',
    rating: 5,
    text: 'Impecable atención y resultados. Ya es mi barbería de confianza.'
  }
]
</script>

<template>
  <div>
    <!-- Hero Section -->
    <section class="relative min-h-[90vh] flex items-center justify-center overflow-hidden">
      <!-- Background gradient -->
      <div class="absolute inset-0 bg-gradient-to-br from-dark-950 via-dark-900 to-dark-950"></div>
      <div class="absolute inset-0 bg-[url('/grid.svg')] opacity-10"></div>
      
      <!-- Content -->
      <div class="container mx-auto px-4 relative z-10">
        <div class="max-w-4xl mx-auto text-center">
          <div class="mb-8 flex justify-center">
            <img 
              src="@/assets/LogoBarber.png" 
              alt="Hernán Barber Logo" 
              class="w-auto animate-pulse"
              style="height: 25rem;"
            />
          </div>
          
          <div class="mb-6 inline-block">
            <span class="px-4 py-2 bg-primary-600/10 border border-primary-600/20 rounded-full text-primary-500 text-sm font-semibold">
              Barbería Profesional en Ciudadela
            </span>
          </div>
          
          <h1 class="text-5xl md:text-7xl font-display font-extrabold text-white mb-6 leading-tight">
            Tu Estilo,<br>
            <span class="text-transparent bg-clip-text bg-gradient-to-r from-primary-500 to-primary-700">
              Nuestra Pasión
            </span>
          </h1>
          
          <p class="text-xl text-gray-400 mb-12 max-w-2xl mx-auto">
            Cortes modernos y clásicos con técnicas profesionales. 
            Experiencia única en un ambiente masculino y elegante.
          </p>
          
          <div class="flex flex-col sm:flex-row gap-4 justify-center">
            <RouterLink to="/book">
              <BaseButton size="lg" class="w-full sm:w-auto">
                Reservá tu Turno 🪒
              </BaseButton>
            </RouterLink>
            <RouterLink to="/services">
              <BaseButton variant="outline" size="lg" class="w-full sm:w-auto">
                Ver Servicios
              </BaseButton>
            </RouterLink>
          </div>
        </div>
      </div>
      
      <!-- Scroll indicator -->
      <div class="absolute bottom-8 left-1/2 -translate-x-1/2 animate-bounce">
        <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3" />
        </svg>
      </div>
    </section>

    <!-- Features Section -->
    <section class="py-20 bg-dark-900">
      <div class="container mx-auto px-4">
        <div class="text-center mb-16">
          <h2 class="text-4xl font-display font-bold text-white mb-4">
            ¿Por qué elegirnos?
          </h2>
          <p class="text-gray-400 text-lg">
            Calidad, profesionalismo y atención al detalle en cada servicio
          </p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
          <Card
            v-for="(feature, index) in features"
            :key="index"
            hover
            class="p-6 text-center"
          >
            <div class="text-5xl mb-4">{{ feature.icon }}</div>
            <h3 class="text-xl font-display font-bold text-white mb-2">
              {{ feature.title }}
            </h3>
            <p class="text-gray-400">{{ feature.description }}</p>
          </Card>
        </div>
      </div>
    </section>

    <!-- Testimonials Section -->
    <section class="py-20">
      <div class="container mx-auto px-4">
        <div class="text-center mb-16">
          <h2 class="text-4xl font-display font-bold text-white mb-4">
            Lo que dicen nuestros clientes
          </h2>
          <p class="text-gray-400 text-lg">
            Tu satisfacción es nuestra mejor recompensa
          </p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 max-w-6xl mx-auto">
          <Card
            v-for="(testimonial, index) in testimonials"
            :key="index"
            class="p-6"
          >
            <div class="flex mb-3">
              <span v-for="star in testimonial.rating" :key="star" class="text-yellow-500 text-xl">⭐</span>
            </div>
            <p class="text-gray-300 mb-4 italic">
              "{{ testimonial.text }}"
            </p>
            <p class="text-white font-semibold">
              - {{ testimonial.name }}
            </p>
          </Card>
        </div>
      </div>
    </section>

    <!-- CTA Section -->
    <section class="py-20 bg-gradient-to-r from-primary-600 to-primary-700">
      <div class="container mx-auto px-4 text-center">
        <h2 class="text-4xl font-display font-bold text-white mb-6">
          ¿Listo para tu nuevo look?
        </h2>
        <p class="text-white/90 text-lg mb-8 max-w-2xl mx-auto">
          Reservá tu turno online y asegurá tu horario preferido. 
          Es rápido, fácil y sin vueltas.
        </p>
        <RouterLink to="/book">
          <BaseButton size="lg" variant="secondary">
            Reservar Turno Ahora
          </BaseButton>
        </RouterLink>
      </div>
    </section>
  </div>
</template>
