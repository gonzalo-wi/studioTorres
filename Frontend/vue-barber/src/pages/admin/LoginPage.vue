<script setup>
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import BaseButton from '@/components/BaseButton.vue'
import BaseInput from '@/components/BaseInput.vue'
import Card from '@/components/Card.vue'
import { useAuth } from '@/composables/useAuth'

const router = useRouter()
const { login } = useAuth()

const email = ref('')
const password = ref('')
const error = ref('')
const isLoading = ref(false)

const handleLogin = async () => {
  error.value = ''
  
  if (!email.value || !password.value) {
    error.value = 'Ingresá email y contraseña'
    return
  }

  isLoading.value = true

  try {
    const result = await login(email.value, password.value)
    
    if (result.success) {
      router.push('/admin')
    } else {
      error.value = result.error
      password.value = ''
    }
  } catch (err) {
    error.value = 'Error al iniciar sesión'
  } finally {
    isLoading.value = false
  }
}
</script>

<template>
  <div class="min-h-screen flex">
    <!-- Left Side - Branding -->
    <div class="hidden lg:flex lg:w-1/2 bg-gradient-to-br from-gray-900 via-black to-gray-900 relative overflow-hidden">
      <!-- Background Pattern -->
      <div class="absolute inset-0 opacity-10">
        <div class="absolute inset-0" style="background-image: radial-gradient(circle at 2px 2px, white 1px, transparent 0); background-size: 40px 40px;"></div>
      </div>
      
      <!-- Gradient Overlay -->
      <div class="absolute inset-0 bg-gradient-to-br from-red-600/20 to-transparent"></div>
      
      <!-- Content -->
      <div class="relative z-10 flex flex-col items-center justify-center w-full p-12 text-center">
        <!-- Logo -->
        <div class="mb-12 transform hover:scale-105 transition-transform duration-300">
          <img 
            src="@/assets/LogoBarber.png" 
            alt="Studio Torres Logo" 
            class="h-48 w-auto drop-shadow-2xl"
          />
        </div>
        
        <!-- Branding Text -->
        <h1 class="text-6xl font-bold text-white mb-4 tracking-tight">
          Studio Torres
        </h1>
        <p class="text-xl text-gray-300 mb-8 max-w-md">
          Sistema de gestión profesional para tu barbería
        </p>
        
        <!-- Features -->
        <div class="space-y-4 text-left max-w-md">
          <div class="flex items-center gap-4 bg-white/5 backdrop-blur-sm rounded-lg p-4 border border-white/10">
            <div class="flex-shrink-0 w-12 h-12 bg-red-600 rounded-lg flex items-center justify-center">
              <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
              </svg>
            </div>
            <div>
              <h3 class="text-white font-semibold">Gestión de Turnos</h3>
              <p class="text-gray-400 text-sm">Administrá reservas fácilmente</p>
            </div>
          </div>
          
          <div class="flex items-center gap-4 bg-white/5 backdrop-blur-sm rounded-lg p-4 border border-white/10">
            <div class="flex-shrink-0 w-12 h-12 bg-red-600 rounded-lg flex items-center justify-center">
              <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
              </svg>
            </div>
            <div>
              <h3 class="text-white font-semibold">Control de Barberos</h3>
              <p class="text-gray-400 text-sm">Horarios y disponibilidad</p>
            </div>
          </div>
          
          <div class="flex items-center gap-4 bg-white/5 backdrop-blur-sm rounded-lg p-4 border border-white/10">
            <div class="flex-shrink-0 w-12 h-12 bg-red-600 rounded-lg flex items-center justify-center">
              <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
              </svg>
            </div>
            <div>
              <h3 class="text-white font-semibold">Reportes y Estadísticas</h3>
              <p class="text-gray-400 text-sm">Analiza el rendimiento</p>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Right Side - Login Form -->
    <div class="w-full lg:w-1/2 flex items-center justify-center p-8 bg-black">
      <div class="w-full max-w-md">
        <!-- Mobile Logo -->
        <div class="lg:hidden text-center mb-8">
          <img 
            src="@/assets/LogoBarber.png" 
            alt="Studio Torres Logo" 
            class="h-24 w-auto mx-auto mb-4"
          />
        </div>

        <!-- Login Header -->
        <div class="mb-8">
          <h2 class="text-3xl font-bold text-white mb-2">
            Bienvenido
          </h2>
          <p class="text-gray-400">
            Ingresá tus credenciales para acceder al panel
          </p>
        </div>

        <!-- Login Form -->
        <Card class="p-8 bg-gradient-to-br from-gray-900 to-gray-800 border-gray-700">
          <form @submit.prevent="handleLogin" class="space-y-6">
            <!-- Email Input -->
            <div>
              <label class="block text-sm font-semibold text-gray-300 mb-2">
                Email
              </label>
              <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                  <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207" />
                  </svg>
                </div>
                <input
                  v-model="email"
                  type="email"
                  placeholder="admin@hernanbarber.com"
                  required
                  class="w-full pl-10 pr-4 py-3 bg-gray-800 border border-gray-600 text-white rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent transition placeholder-gray-500"
                />
              </div>
            </div>

            <!-- Password Input -->
            <div>
              <label class="block text-sm font-semibold text-gray-300 mb-2">
                Contraseña
              </label>
              <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                  <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                  </svg>
                </div>
                <input
                  v-model="password"
                  type="password"
                  placeholder="Ingresá tu contraseña"
                  required
                  @keyup.enter="handleLogin"
                  class="w-full pl-10 pr-4 py-3 bg-gray-800 border border-gray-600 text-white rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent transition placeholder-gray-500"
                />
              </div>
              <p v-if="error" class="mt-2 text-sm text-red-500 flex items-center gap-1">
                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                  <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                </svg>
                {{ error }}
              </p>
            </div>

            <!-- Submit Button -->
            <button
              type="submit"
              :disabled="isLoading"
              class="w-full bg-gradient-to-r from-red-600 to-red-700 hover:from-red-700 hover:to-red-800 text-white font-bold py-3 px-6 rounded-lg shadow-lg transform hover:scale-[1.02] active:scale-[0.98] transition-all duration-200 disabled:opacity-50 disabled:cursor-not-allowed disabled:transform-none flex items-center justify-center gap-2"
            >
              <svg v-if="isLoading" class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
              </svg>
              {{ isLoading ? 'Verificando...' : 'Ingresar al Panel' }}
            </button>
          </form>

          <!-- Dev Hint -->
          <div class="mt-6 pt-6 border-t border-gray-700">
            <p class="text-xs text-gray-500 text-center">
              💡 <strong>Demo:</strong> admin@studiotorres.com | Admin123!
            </p>
          </div>
        </Card>

        <!-- Footer -->
        <p class="text-center text-gray-500 text-sm mt-8">
          © 2025 Studio Torres. Todos los derechos reservados.
        </p>
      </div>
    </div>
  </div>
</template>
