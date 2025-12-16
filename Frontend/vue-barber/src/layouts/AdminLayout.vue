<script setup>
import { ref } from 'vue'
import { RouterLink, useRouter } from 'vue-router'
import { useAuth } from '@/composables/useAuth'
import { ChartBarIcon, CalendarDaysIcon } from '@heroicons/vue/24/outline'

const router = useRouter()
const { logout } = useAuth()

const handleLogout = () => {
  logout()
  router.push('/admin/login')
}

const navLinks = [
  { to: '/admin', label: 'Dashboard', icon: ChartBarIcon },
  { to: '/admin/bookings', label: 'Turnos', icon: CalendarDaysIcon }
]
</script>

<template>
  <div class="min-h-screen bg-dark-950">
    <!-- Admin Navbar -->
    <header class="bg-dark-900 border-b border-dark-800 sticky top-0 z-50">
      <nav class="container mx-auto px-4 py-4">
        <div class="flex items-center justify-between">
          <RouterLink to="/admin" class="flex items-center space-x-2">
            <img 
              src="@/assets/LogoBarber.png" 
              alt="Hernán Barber Logo" 
              class="h-8 w-auto"
            />
            <span class="text-lg font-display font-bold text-white hidden sm:inline">Panel Admin</span>
          </RouterLink>

          <div class="flex items-center space-x-6">
            <RouterLink
              v-for="link in navLinks"
              :key="link.to"
              :to="link.to"
              class="text-gray-300 hover:text-white font-semibold transition-colors flex items-center space-x-2"
            >
              <component :is="link.icon" class="w-5 h-5" />
              <span class="hidden sm:inline">{{ link.label }}</span>
            </RouterLink>
            
            <button
              @click="handleLogout"
              class="text-gray-300 hover:text-primary-500 font-semibold transition-colors"
            >
              Salir
            </button>
          </div>
        </div>
      </nav>
    </header>

    <!-- Admin Content -->
    <main class="container mx-auto px-4 py-8">
      <router-view />
    </main>
  </div>
</template>
