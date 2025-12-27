<script setup>
import { ref, onMounted } from 'vue'
import { RouterLink } from 'vue-router'
import BaseButton from '@/components/BaseButton.vue'
import Card from '@/components/Card.vue'
import { 
  ScissorsIcon, 
  SparklesIcon, 
  UserIcon, 
  ClockIcon, 
  CalendarIcon,
  MapPinIcon,
  PhoneIcon,
  TrophyIcon,
  UsersIcon,
  StarIcon
} from '@heroicons/vue/24/outline'

const features = [
  {
    icon: ScissorsIcon,
    title: 'Cortes Profesionales',
    description: 'Técnicas modernas y clásicas adaptadas a tu estilo'
  },
  {
    icon: SparklesIcon,
    title: 'Afeitado Tradicional',
    description: 'Experiencia premium con productos de primera calidad'
  },
  {
    icon: UserIcon,
    title: 'Atención Personalizada',
    description: 'Cada cliente es único y merece un trato especial'
  },
  {
    icon: ClockIcon,
    title: 'Horarios Flexibles',
    description: 'Abierto de lunes a sábado para tu comodidad'
  }
]

const stats = [
  { value: '500+', label: 'Clientes Satisfechos', icon: UsersIcon },
  { value: '5', label: 'Años de Experiencia', icon: TrophyIcon },
  { value: '4.9', label: 'Calificación Promedio', icon: StarIcon },
  { value: '100%', label: 'Profesionalismo', icon: SparklesIcon }
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

const schedule = [
  { day: 'Lunes a Viernes', hours: '09:00 - 20:00' },
  { day: 'Sábado', hours: '09:00 - 18:00' },
  { day: 'Domingo', hours: 'Cerrado', closed: true }
]

// Animations
const isVisible = ref(false)
const whatsappNumber = '5491234567890' // Cambiar por el número real

onMounted(() => {
  isVisible.value = true
})
</script>

<template>
  <div>
    <!-- Hero Section -->
    <section class="relative min-h-[90vh] flex items-center justify-center overflow-hidden bg-gradient-to-br from-white via-gold-50 to-white">
      <!-- Background pattern -->
      <div class="absolute inset-0 bg-[url('/grid.svg')] opacity-5"></div>
      
      <!-- Content -->
      <div class="container mx-auto px-4 relative z-10">
        <div class="max-w-4xl mx-auto text-center">
          <div class="mb-8 flex justify-center transition-all duration-1000" 
               :class="isVisible ? 'opacity-100 translate-y-0' : 'opacity-0 -translate-y-10'">
            <img 
              src="@/assets/LogoBarber.png" 
              alt="Hernán Barber Logo" 
              class="w-auto hover:scale-105 transition-transform duration-300"
              style="height: 25rem;"
            />
          </div>
          
          <div class="mb-6 inline-block transition-all duration-1000 delay-200"
               :class="isVisible ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-10'">
            <span class="px-4 py-2 bg-gold-100 border border-gold-300 rounded-full text-gold-800 text-sm font-semibold">
              Barbería Profesional en Ciudadela
            </span>
          </div>
          
          <h1 class="text-5xl md:text-7xl font-display font-extrabold text-dark-900 mb-6 leading-tight transition-all duration-1000 delay-300"
              :class="isVisible ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-10'">
            Tu Estilo,<br>
            <span class="text-transparent bg-clip-text bg-gradient-to-r from-gold-500 to-gold-700">
              Nuestra Pasión
            </span>
          </h1>
          
          <p class="text-xl text-dark-600 mb-12 max-w-2xl mx-auto transition-all duration-1000 delay-500"
             :class="isVisible ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-10'">
            Cortes modernos y clásicos con técnicas profesionales. 
            Experiencia única en un ambiente masculino y elegante.
          </p>
          
          <div class="flex flex-col sm:flex-row gap-4 justify-center transition-all duration-1000 delay-700"
               :class="isVisible ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-10'">
            <RouterLink to="/book">
              <BaseButton size="lg" class="w-full sm:w-auto flex items-center justify-center gap-2 hover:scale-105 transition-transform">
                <CalendarIcon class="w-5 h-5" />
                Reservá tu Turno
              </BaseButton>
            </RouterLink>
            <RouterLink to="/services">
              <BaseButton variant="outline" size="lg" class="w-full sm:w-auto hover:scale-105 transition-transform">
                Ver Servicios
              </BaseButton>
            </RouterLink>
          </div>
        </div>
      </div>
      
      <!-- Scroll indicator -->
      <div class="absolute bottom-8 left-1/2 -translate-x-1/2 animate-bounce">
        <svg class="w-6 h-6 text-gold-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3" />
        </svg>
      </div>
    </section>

    <!-- Features Section -->
    <section class="py-20 bg-gray-50">
      <div class="container mx-auto px-4">
        <div class="text-center mb-16">
          <h2 class="text-4xl font-display font-bold text-dark-900 mb-4">
            ¿Por qué elegirnos?
          </h2>
          <p class="text-dark-600 text-lg">
            Calidad, profesionalismo y atención al detalle en cada servicio
          </p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
          <Card
            v-for="(feature, index) in features"
            :key="index"
            hover
            class="p-6 text-center transform hover:scale-105 transition-all duration-300 hover:shadow-xl"
          >
            <div class="mb-4 flex justify-center">
              <component :is="feature.icon" class="w-12 h-12 text-gold-600 transition-transform hover:rotate-12 duration-300" />
            </div>
            <h3 class="text-xl font-display font-bold text-dark-900 mb-2">
              {{ feature.title }}
            </h3>
            <p class="text-dark-600">{{ feature.description }}</p>
          </Card>
        </div>
      </div>
    </section>

    <!-- Testimonials Section -->
    <section class="py-20 bg-white">
      <div class="container mx-auto px-4">
        <div class="text-center mb-16">
          <h2 class="text-4xl font-display font-bold text-dark-900 mb-4">
            Lo que dicen nuestros clientes
          </h2>
          <p class="text-dark-600 text-lg">
            Tu satisfacción es nuestra mejor recompensa
          </p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 max-w-6xl mx-auto">
          <Card
            v-for="(testimonial, index) in testimonials"
            :key="index"
            class="p-6 transform hover:-translate-y-2 transition-all duration-300 hover:shadow-xl"
          >
            <div class="flex mb-3">
              <span v-for="star in testimonial.rating" :key="star" class="text-gold-500 text-xl">⭐</span>
            </div>
            <p class="text-dark-700 mb-4 italic">
              "{{ testimonial.text }}"
            </p>
            <p class="text-dark-900 font-semibold">
              - {{ testimonial.name }}
            </p>
          </Card>
        </div>
      </div>
    </section>

    <!-- Location Section -->
    <section class="py-20 bg-gray-50">
      <div class="container mx-auto px-4">
        <div class="text-center mb-16">
          <h2 class="text-4xl font-display font-bold text-dark-900 mb-4">
            Visitanos
          </h2>
          <p class="text-dark-600 text-lg">
            Eduardo Comesaña 2588, Ciudadela
          </p>
        </div>

        <div class="max-w-4xl mx-auto">
          <Card class="overflow-hidden h-[450px] hover:shadow-2xl transition-shadow duration-300">
            <iframe 
              src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3282.947!2d-58.533936811257306!3d-34.619698029002606!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x0%3A0x0!2zMzTCsDM3JzEwLjkiUyA1OMKwMzInMDIuMiJX!5e0!3m2!1ses-419!2sar!4v1734890000000"
              width="100%" 
              height="100%" 
              style="border:0;" 
              allowfullscreen="" 
              loading="lazy" 
              referrerpolicy="no-referrer-when-downgrade"
            ></iframe>
          </Card>
        </div>
      </div>
    </section>

    <!-- CTA Section -->
    <section class="py-20 bg-gradient-to-r from-gold-500 to-gold-600">
      <div class="container mx-auto px-4 text-center">
        <h2 class="text-4xl font-display font-bold text-white mb-6">
          ¿Listo para tu nuevo look?
        </h2>
        <p class="text-white/90 text-lg mb-8 max-w-2xl mx-auto">
          Reservá tu turno online y asegurá tu horario preferido. 
          Es rápido, fácil y sin vueltas.
        </p>
        <RouterLink to="/book">
          <BaseButton size="lg" variant="secondary" class="hover:scale-105 transition-transform">
            Reservar Turno Ahora
          </BaseButton>
        </RouterLink>
      </div>
    </section>

    <!-- WhatsApp Floating Button -->
    <a 
      :href="`https://wa.me/${whatsappNumber}?text=Hola!%20Quiero%20consultar%20por%20un%20turno`"
      target="_blank"
      rel="noopener noreferrer"
      class="fixed bottom-6 right-6 bg-green-500 text-white p-4 rounded-full shadow-2xl hover:bg-green-600 hover:scale-110 transition-all duration-300 z-50 group"
      aria-label="Contactar por WhatsApp"
    >
      <svg class="w-8 h-8" fill="currentColor" viewBox="0 0 24 24">
        <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
      </svg>
      <span class="absolute right-full mr-3 top-1/2 -translate-y-1/2 bg-gray-900 text-white px-3 py-2 rounded-lg text-sm whitespace-nowrap opacity-0 group-hover:opacity-100 transition-opacity pointer-events-none">
        ¡Contactanos por WhatsApp!
      </span>
    </a>
  </div>
</template>
