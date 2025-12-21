<script setup>
import { ref, onMounted, onUnmounted } from 'vue'
import { RouterLink } from 'vue-router'

const isMenuOpen = ref(false)
const isScrolled = ref(false)

const toggleMenu = () => {
  isMenuOpen.value = !isMenuOpen.value
}

const closeMenu = () => {
  isMenuOpen.value = false
}

const handleScroll = () => {
  isScrolled.value = window.scrollY > 20
}

onMounted(() => {
  window.addEventListener('scroll', handleScroll)
})

onUnmounted(() => {
  window.removeEventListener('scroll', handleScroll)
})

const navLinks = [
  { to: '/', label: 'Inicio' },
  { to: '/services', label: 'Servicios' },
  { to: '/gallery', label: 'Catálogo' },
  { to: '/book', label: 'Reservar', highlight: true }
]
</script>

<template>
  <header 
    class="fixed top-0 left-0 right-0 z-50 transition-all duration-300"
    :class="isScrolled ? 'bg-white/95 backdrop-blur-sm shadow-lg' : 'bg-white/90'"
  >
    <nav class="container mx-auto px-4 py-4">
      <div class="flex items-center justify-between">
        <!-- Logo -->
        <RouterLink to="/" class="flex items-center space-x-3 group">
          <img 
            src="@/assets/LogoBarber.png" 
            alt="Hernán Barber Logo" 
            class="h-12 w-auto transition-transform group-hover:scale-105"
          />
          <span class="text-xl font-display font-bold text-dark-900 group-hover:text-gold-600 transition-colors hidden sm:inline">
            Studio Torres
          </span>
        </RouterLink>

        <!-- Desktop Navigation -->
        <div class="hidden md:flex items-center space-x-8">
          <RouterLink
            v-for="link in navLinks"
            :key="link.to"
            :to="link.to"
            :class="[
              'font-semibold transition-colors duration-200',
              link.highlight 
                ? 'bg-gold-500 hover:bg-gold-600 text-white px-6 py-3 rounded-lg shadow-md shadow-gold-500/30' 
                : 'text-dark-600 hover:text-gold-600'
            ]"
          >
            {{ link.label }}
          </RouterLink>
        </div>

        <!-- Mobile Menu Button -->
        <button
          @click="toggleMenu"
          class="md:hidden text-dark-900 p-2 hover:bg-gold-50 rounded-lg transition-colors"
          aria-label="Toggle menu"
        >
          <svg v-if="!isMenuOpen" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
          </svg>
          <svg v-else class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
          </svg>
        </button>
      </div>

      <!-- Mobile Menu -->
      <transition
        enter-active-class="transition duration-200 ease-out"
        enter-from-class="opacity-0 -translate-y-4"
        enter-to-class="opacity-100 translate-y-0"
        leave-active-class="transition duration-150 ease-in"
        leave-from-class="opacity-100 translate-y-0"
        leave-to-class="opacity-0 -translate-y-4"
      >
        <div v-if="isMenuOpen" class="md:hidden mt-4 pb-4 space-y-2">
          <RouterLink
            v-for="link in navLinks"
            :key="link.to"
            :to="link.to"
            @click="closeMenu"
            :class="[
              'block px-4 py-3 rounded-lg font-semibold transition-colors',
              link.highlight 
                ? 'bg-gold-500 hover:bg-gold-600 text-white text-center' 
                : 'text-dark-600 hover:text-gold-600 hover:bg-gold-50'
            ]"
          >
            {{ link.label }}
          </RouterLink>
        </div>
      </transition>
    </nav>
  </header>
</template>
