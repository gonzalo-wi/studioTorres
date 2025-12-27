<script setup>
import { ref, computed } from 'vue'
import { RouterLink, useRouter, useRoute } from 'vue-router'
import { useAuth } from '@/composables/useAuth'
import { 
  ChartBarIcon, 
  CalendarDaysIcon,
  Squares2X2Icon,
  UsersIcon,
  XMarkIcon,
  Bars3Icon,
  ArrowRightOnRectangleIcon,
  DocumentChartBarIcon
} from '@heroicons/vue/24/outline'

const router = useRouter()
const route = useRoute()
const { logout, user } = useAuth()
const sidebarOpen = ref(false)

const handleLogout = () => {
  logout()
  router.push('/admin/login')
}

// Menú completo para ADMIN
const allNavLinks = [
  { to: '/admin', label: 'Dashboard', icon: ChartBarIcon, exact: true },
  { to: '/admin/bookings', label: 'Turnos', icon: CalendarDaysIcon },
  { to: '/admin/barbers', label: 'Barberos', icon: UsersIcon },
  { to: '/admin/services', label: 'Servicios', icon: Squares2X2Icon },
  { to: '/admin/reports', label: 'Reportes', icon: DocumentChartBarIcon }
]

// Menú limitado para BARBER
const barberNavLinks = [
  { to: '/admin', label: 'Dashboard', icon: ChartBarIcon, exact: true },
  { to: '/admin/bookings', label: 'Mis Turnos', icon: CalendarDaysIcon }
]

// Mostrar menú según rol
const navLinks = computed(() => {
  return user.value?.role === 'BARBER' ? barberNavLinks : allNavLinks
})

const closeSidebar = () => {
  sidebarOpen.value = false
}

const isActive = (link) => {
  if (link.exact) {
    return route.path === link.to
  }
  return route.path.startsWith(link.to)
}
</script>

<template>
  <div class="min-h-screen bg-gray-50 flex">
    <!-- Sidebar Desktop -->
    <aside class="hidden lg:flex lg:flex-col lg:w-64 bg-white border-r border-gold-200 shadow-xl fixed left-0 top-0 bottom-0">
      <!-- Logo -->
      <div class="p-6 border-b border-gold-200 flex-shrink-0 bg-gradient-to-br from-gold-50 to-white">
        <RouterLink to="/admin" class="flex items-center space-x-3">
          <img 
            src="@/assets/LogoBarber.png" 
            alt="Studio Torres Logo" 
            class="h-10 w-auto"
          />
          <div>
            <div class="text-dark-900 font-bold text-lg">Studio Torres</div>
            <div class="text-gold-700 text-xs font-semibold">Panel Admin</div>
          </div>
        </RouterLink>
      </div>

      <!-- Navigation -->
      <nav class="flex-1 p-4 space-y-2 overflow-y-auto">
        <RouterLink
          v-for="link in navLinks"
          :key="link.to"
          :to="link.to"
          :class="[
            'flex items-center space-x-3 px-4 py-3 rounded-lg transition-all duration-200',
            isActive(link)
              ? 'bg-gradient-to-r from-gold-500 to-gold-600 text-white shadow-lg shadow-gold-500/30'
              : 'text-dark-600 hover:text-gold-700 hover:bg-gold-50'
          ]"
        >
          <component :is="link.icon" class="w-5 h-5 flex-shrink-0" />
          <span class="font-semibold">{{ link.label }}</span>
        </RouterLink>
      </nav>

      <!-- User Section -->
      <div class="p-4 border-t border-gold-200 flex-shrink-0">
        <button
          @click="handleLogout"
          class="flex items-center space-x-3 w-full px-4 py-3 rounded-lg text-dark-600 hover:text-red-600 hover:bg-red-50 transition-all duration-200">
        >
          <ArrowRightOnRectangleIcon class="w-5 h-5" />
          <span class="font-semibold">Cerrar Sesión</span>
        </button>
      </div>
    </aside>

    <!-- Mobile Sidebar Overlay -->
    <div
      v-if="sidebarOpen"
      class="fixed inset-0 bg-black/50 backdrop-blur-sm z-40 lg:hidden"
      @click="closeSidebar"
    ></div>

    <!-- Mobile Sidebar -->
    <aside
      :class="[
        'fixed top-0 left-0 bottom-0 w-64 bg-white border-r border-gold-200 shadow-2xl z-50 transform transition-transform duration-300 lg:hidden',
        sidebarOpen ? 'translate-x-0' : '-translate-x-full'
      ]"
    >
      <!-- Mobile Header -->
      <div class="p-4 border-b border-gold-200 flex items-center justify-between bg-gradient-to-br from-gold-50 to-white">
        <RouterLink to="/admin" @click="closeSidebar" class="flex items-center space-x-3">
          <img 
            src="@/assets/LogoBarber.png" 
            alt="Studio Torres Logo" 
            class="h-8 w-auto"
          />
          <div>
            <div class="text-dark-900 font-bold">Studio Torres</div>
            <div class="text-gold-700 text-xs font-semibold">Panel Admin</div>
          </div>
        </RouterLink>
        <button @click="closeSidebar" class="text-dark-600 hover:text-gold-700">
          <XMarkIcon class="w-6 h-6" />
        </button>
      </div>

      <!-- Mobile Navigation -->
      <nav class="flex-1 p-4 space-y-2">
        <RouterLink
          v-for="link in navLinks"
          :key="link.to"
          :to="link.to"
          @click="closeSidebar"
          :class="[
            'flex items-center space-x-3 px-4 py-3 rounded-lg transition-all duration-200',
            isActive(link)
              ? 'bg-gradient-to-r from-gold-500 to-gold-600 text-white shadow-lg shadow-gold-500/30'
              : 'text-dark-600 hover:text-gold-700 hover:bg-gold-50'
          ]"
        >
          <component :is="link.icon" class="w-5 h-5 flex-shrink-0" />
          <span class="font-semibold">{{ link.label }}</span>
        </RouterLink>
      </nav>

      <!-- Mobile User Section -->
      <div class="p-4 border-t border-gold-200">
        <button
          @click="handleLogout"
          class="flex items-center space-x-3 w-full px-4 py-3 rounded-lg text-dark-600 hover:text-red-600 hover:bg-red-50 transition-all duration-200">
        >
          <ArrowRightOnRectangleIcon class="w-5 h-5" />
          <span class="font-semibold">Cerrar Sesión</span>
        </button>
      </div>
    </aside>

    <!-- Main Content Area -->
    <div class="flex-1 flex flex-col min-h-screen lg:ml-64">
      <!-- Mobile Header -->
      <header class="lg:hidden bg-white border-b border-gold-200 shadow-sm sticky top-0 z-30">
        <div class="flex items-center justify-between p-4">
          <button
            @click="sidebarOpen = true"
            class="text-dark-600 hover:text-gold-700">
          >
            <Bars3Icon class="w-6 h-6" />
          </button>
          <img 
            src="@/assets/LogoBarber.png" 
            alt="Studio Torres Logo" 
            class="h-8 w-auto"
          />
          <div class="w-6"></div>
        </div>
      </header>

      <!-- Main Content -->
      <main class="flex-1 p-6 lg:p-8 bg-gray-50 min-h-screen">
        <router-view />
      </main>
    </div>
  </div>
</template>
