<script setup>
import { ref } from 'vue'
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
const { logout } = useAuth()
const sidebarOpen = ref(false)

const handleLogout = () => {
  logout()
  router.push('/admin/login')
}

const navLinks = [
  { to: '/admin', label: 'Dashboard', icon: ChartBarIcon, exact: true },
  { to: '/admin/bookings', label: 'Turnos', icon: CalendarDaysIcon },
  { to: '/admin/barbers', label: 'Barberos', icon: UsersIcon },
  { to: '/admin/services', label: 'Servicios', icon: Squares2X2Icon },
  { to: '/admin/reports', label: 'Reportes', icon: DocumentChartBarIcon }
]

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
  <div class="min-h-screen bg-black flex">
    <!-- Sidebar Desktop -->
    <aside class="hidden lg:flex lg:flex-col lg:w-64 bg-gradient-to-b from-gray-900 to-black border-r border-gray-800 fixed left-0 top-0 bottom-0">
      <!-- Logo -->
      <div class="p-6 border-b border-gray-800 flex-shrink-0">
        <RouterLink to="/admin" class="flex items-center space-x-3">
          <img 
            src="@/assets/LogoBarber.png" 
            alt="Studio Torres Logo" 
            class="h-10 w-auto"
          />
          <div>
            <div class="text-white font-bold text-lg">Studio Torres</div>
            <div class="text-gray-400 text-xs">Panel Admin</div>
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
              ? 'bg-red-600 text-white shadow-lg shadow-red-600/50'
              : 'text-gray-400 hover:text-white hover:bg-gray-800'
          ]"
        >
          <component :is="link.icon" class="w-5 h-5 flex-shrink-0" />
          <span class="font-semibold">{{ link.label }}</span>
        </RouterLink>
      </nav>

      <!-- User Section -->
      <div class="p-4 border-t border-gray-800 flex-shrink-0">
        <button
          @click="handleLogout"
          class="flex items-center space-x-3 w-full px-4 py-3 rounded-lg text-gray-400 hover:text-white hover:bg-gray-800 transition-all duration-200"
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
        'fixed top-0 left-0 bottom-0 w-64 bg-gradient-to-b from-gray-900 to-black border-r border-gray-800 z-50 transform transition-transform duration-300 lg:hidden',
        sidebarOpen ? 'translate-x-0' : '-translate-x-full'
      ]"
    >
      <!-- Mobile Header -->
      <div class="p-4 border-b border-gray-800 flex items-center justify-between">
        <RouterLink to="/admin" @click="closeSidebar" class="flex items-center space-x-3">
          <img 
            src="@/assets/LogoBarber.png" 
            alt="Studio Torres Logo" 
            class="h-8 w-auto"
          />
          <div>
            <div class="text-white font-bold">Studio Torres</div>
            <div class="text-gray-400 text-xs">Panel Admin</div>
          </div>
        </RouterLink>
        <button @click="closeSidebar" class="text-gray-400 hover:text-white">
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
              ? 'bg-red-600 text-white shadow-lg shadow-red-600/50'
              : 'text-gray-400 hover:text-white hover:bg-gray-800'
          ]"
        >
          <component :is="link.icon" class="w-5 h-5 flex-shrink-0" />
          <span class="font-semibold">{{ link.label }}</span>
        </RouterLink>
      </nav>

      <!-- Mobile User Section -->
      <div class="p-4 border-t border-gray-800">
        <button
          @click="handleLogout"
          class="flex items-center space-x-3 w-full px-4 py-3 rounded-lg text-gray-400 hover:text-white hover:bg-gray-800 transition-all duration-200"
        >
          <ArrowRightOnRectangleIcon class="w-5 h-5" />
          <span class="font-semibold">Cerrar Sesión</span>
        </button>
      </div>
    </aside>

    <!-- Main Content Area -->
    <div class="flex-1 flex flex-col min-h-screen lg:ml-64">
      <!-- Mobile Header -->
      <header class="lg:hidden bg-gray-900 border-b border-gray-800 sticky top-0 z-30">
        <div class="flex items-center justify-between p-4">
          <button
            @click="sidebarOpen = true"
            class="text-gray-400 hover:text-white"
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
      <main class="flex-1 p-6 lg:p-8">
        <router-view />
      </main>
    </div>
  </div>
</template>
