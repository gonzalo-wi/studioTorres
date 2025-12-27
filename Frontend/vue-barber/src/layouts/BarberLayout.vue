<script setup>
import { ref, computed, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { useBarberAuth } from '@/composables/useBarberAuth'

const router = useRouter()
const { barber, logout } = useBarberAuth()

const sidebarOpen = ref(false)

const navigation = [
  { name: 'Inicio', path: '/barber', icon: 'home' },
  { name: 'Mis Turnos', path: '/barber/appointments', icon: 'calendar' },
]

const isActivePath = (path) => {
  return router.currentRoute.value.path === path
}

const handleLogout = async () => {
  await logout()
  router.push('/barber/login')
}
</script>

<template>
  <div class="min-h-screen bg-gray-50">
    <!-- Mobile sidebar backdrop -->
    <div
      v-if="sidebarOpen"
      class="fixed inset-0 bg-gray-900/50 z-40 md:hidden"
      @click="sidebarOpen = false"
    ></div>

    <!-- Sidebar -->
    <aside
      :class="[
        'fixed top-0 left-0 z-50 h-screen w-64 bg-blue-900 text-white transition-transform duration-300 ease-in-out',
        sidebarOpen ? 'translate-x-0' : '-translate-x-full md:translate-x-0'
      ]"
    >
      <!-- Logo/Header -->
      <div class="flex items-center justify-between px-6 py-5 border-b border-blue-800">
        <div>
          <h1 class="text-xl font-bold">Panel Barbero</h1>
          <p class="text-sm text-blue-200 mt-1">{{ barber?.name }}</p>
        </div>
        <button
          @click="sidebarOpen = false"
          class="md:hidden text-blue-200 hover:text-white"
        >
          <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
          </svg>
        </button>
      </div>

      <!-- Navigation -->
      <nav class="px-4 py-6 space-y-2">
        <router-link
          v-for="item in navigation"
          :key="item.path"
          :to="item.path"
          :class="[
            'flex items-center px-4 py-3 rounded-lg text-sm font-medium transition-colors',
            isActivePath(item.path)
              ? 'bg-blue-800 text-white'
              : 'text-blue-100 hover:bg-blue-800/50 hover:text-white'
          ]"
          @click="sidebarOpen = false"
        >
          <svg v-if="item.icon === 'home'" class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
          </svg>
          <svg v-if="item.icon === 'calendar'" class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
          </svg>
          {{ item.name }}
        </router-link>
      </nav>

      <!-- Logout Button -->
      <div class="absolute bottom-0 left-0 right-0 p-4 border-t border-blue-800">
        <button
          @click="handleLogout"
          class="flex items-center w-full px-4 py-3 text-sm font-medium text-blue-100 hover:bg-blue-800/50 hover:text-white rounded-lg transition-colors"
        >
          <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
          </svg>
          Cerrar Sesión
        </button>
      </div>
    </aside>

    <!-- Main Content -->
    <div class="md:ml-64">
      <!-- Top Bar (Mobile) -->
      <header class="sticky top-0 z-30 bg-white border-b border-gray-200 md:hidden">
        <div class="flex items-center justify-between px-4 py-3">
          <button
            @click="sidebarOpen = true"
            class="text-gray-600 hover:text-gray-900"
          >
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
            </svg>
          </button>
          <h2 class="text-lg font-semibold text-gray-900">{{ barber?.name }}</h2>
          <div class="w-6"></div>
        </div>
      </header>

      <!-- Page Content -->
      <main class="p-4 md:p-8">
        <slot />
      </main>
    </div>
  </div>
</template>
