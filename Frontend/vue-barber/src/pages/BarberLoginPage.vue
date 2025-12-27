<script setup>
import { ref, computed } from 'vue'
import { useRouter } from 'vue-router'
import { useBarberAuth } from '@/composables/useBarberAuth'
import BaseInput from '@/components/BaseInput.vue'
import BaseButton from '@/components/BaseButton.vue'

const router = useRouter()
const { login, loading, error: authError } = useBarberAuth()

const form = ref({
  email: '',
  password: ''
})

const errors = ref({})

const validateForm = () => {
  errors.value = {}
  
  if (!form.value.email) {
    errors.value.email = 'El email es requerido'
  } else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(form.value.email)) {
    errors.value.email = 'Email inválido'
  }
  
  if (!form.value.password) {
    errors.value.password = 'La contraseña es requerida'
  }
  
  return Object.keys(errors.value).length === 0
}

const handleSubmit = async () => {
  if (!validateForm()) return
  
  const success = await login(form.value.email, form.value.password)
  
  if (success) {
    router.push('/barber')
  }
}
</script>

<template>
  <div class="min-h-screen bg-gradient-to-br from-blue-900 via-blue-800 to-blue-900 flex items-center justify-center p-4">
    <div class="w-full max-w-md">
      <!-- Logo/Header -->
      <div class="text-center mb-8">
        <div class="inline-flex items-center justify-center w-20 h-20 bg-white rounded-full shadow-xl mb-4">
          <svg class="w-12 h-12 text-blue-900" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
          </svg>
        </div>
        <h1 class="text-3xl font-bold text-white mb-2">Panel de Barbero</h1>
        <p class="text-blue-200">Ingresá a tu cuenta</p>
      </div>

      <!-- Card de Login -->
      <div class="bg-white rounded-2xl shadow-2xl p-8">
        <form @submit.prevent="handleSubmit" class="space-y-6">
          <!-- Email -->
          <BaseInput
            v-model="form.email"
            label="Email"
            type="email"
            placeholder="tu@email.com"
            :error="errors.email"
            autocomplete="email"
          />

          <!-- Password -->
          <BaseInput
            v-model="form.password"
            label="Contraseña"
            type="password"
            placeholder="••••••••"
            :error="errors.password"
            autocomplete="current-password"
          />

          <!-- Error de autenticación -->
          <div v-if="authError" class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg text-sm">
            {{ authError }}
          </div>

          <!-- Submit Button -->
          <BaseButton
            type="submit"
            variant="primary"
            :loading="loading"
            class="w-full bg-blue-900 hover:bg-blue-800 text-white py-3 text-lg font-semibold"
          >
            Ingresar
          </BaseButton>
        </form>

        <!-- Divider -->
        <div class="mt-6 pt-6 border-t border-gray-200 text-center">
          <router-link 
            to="/" 
            class="text-sm text-blue-900 hover:text-blue-700 font-medium transition-colors"
          >
            ← Volver al inicio
          </router-link>
        </div>
      </div>
    </div>
  </div>
</template>
