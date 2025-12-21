<script setup>
const currentYear = new Date().getFullYear()

const socialLinks = [
  { icon: 'instagram', url: 'https://www.instagram.com/studiootorress?igsh=MWprNXVwbjQ5emx5eQ==', label: 'Instagram', svg: '/src/assets/Instagram_icon.svg' },
  { icon: 'facebook', url: 'https://www.facebook.com/share/1FpE7PQCyj/?mibextid=wwXIfr', label: 'Facebook', svg: '/src/assets/Facebook.svg' },
  { icon: 'whatsapp', url: 'https://wa.me/5491151046978?text=Hola!%20Quisiera%20consultar%20por%20los%20servicios%20de%20la%20barbería.', label: 'WhatsApp', svg: '/src/assets/Whatsapp_icon.svg' }
]

const schedules = [
  { days: 'Lunes a Viernes', hours: '10:00 - 19:00' },
  { days: 'Sábados', hours: '10:00 - 17:00' },
  { days: 'Domingos', hours: 'Cerrado' }
]
</script>

<template>
  <footer class="bg-gray-100 border-t border-gold-200 mt-20">
    <div class="container mx-auto px-4 py-12">
      <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
        <!-- Brand -->
        <div>
          <div class="flex items-center space-x-3 mb-4">
            <img 
              src="@/assets/LogoBarber.png" 
              alt="Hernán Barber Logo" 
              class="h-10 w-auto"
            />
            <span class="text-xl font-display font-bold text-dark-900">Hernán Barber</span>
          </div>
          <p class="text-dark-600 text-sm">
            Barbería profesional especializada en cortes modernos y clásicos. 
            Estilo, calidad y atención personalizada.
          </p>
        </div>

        <!-- Horarios -->
        <div>
          <h3 class="text-dark-900 font-display font-bold text-lg mb-4">Horarios</h3>
          <ul class="space-y-2">
            <li v-for="schedule in schedules" :key="schedule.days" class="text-sm">
              <span class="text-dark-600">{{ schedule.days }}:</span>
              <span class="text-dark-900 font-semibold ml-2">{{ schedule.hours }}</span>
            </li>
          </ul>
        </div>

        <!-- Contacto y Redes -->
        <div>
          <h3 class="text-dark-900 font-display font-bold text-lg mb-4">Contacto</h3>
          <p class="text-dark-600 text-sm mb-4">
            📍 Eduardo Comesaña 2588, Ciudadela<br>
            📞 +54 9 11 5104-6978<br>
            ✉️ info@studio.torres.com
          </p>
          <div class="flex space-x-4">
            <a
              v-for="social in socialLinks"
              :key="social.icon"
              :href="social.url"
              target="_blank"
              rel="noopener noreferrer"
              :aria-label="social.label"
              class="w-10 h-10 bg-gold-100 hover:bg-gold-200 rounded-lg flex items-center justify-center transition-all hover:scale-110 border border-gold-300"
            >
              <img 
                :src="social.svg" 
                :alt="social.label"
                class="w-6 h-6 object-contain"
              />
            </a>
          </div>
        </div>
      </div>

      <!-- Copyright -->
      <div class="border-t border-gold-200 mt-8 pt-8 text-center">
        <p class="text-dark-600 text-sm">
          © {{ currentYear }} Studio Torres. Todos los derechos reservados.
        </p>
      </div>
    </div>
  </footer>
</template>
