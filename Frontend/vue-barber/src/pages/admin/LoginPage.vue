<script setup>
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import BaseButton from '@/components/BaseButton.vue'
import BaseInput from '@/components/BaseInput.vue'
import Card from '@/components/Card.vue'
import { useAuth } from '@/composables/useAuth'

const router = useRouter()
const { login } = useAuth()

const password = ref('')
const error = ref('')
const isLoading = ref(false)

const handleLogin = () => {
  error.value = ''
  
  if (!password.value) {
    error.value = 'Ingresá la contraseña'
    return
  }

  isLoading.value = true

  setTimeout(() => {
    const result = login(password.value)
    
    if (result.success) {
      router.push('/admin')
    } else {
      error.value = result.error
      password.value = ''
    }
    
    isLoading.value = false
  }, 500)
}
</script>

<template>
  <div class="min-h-screen flex items-center justify-center bg-dark-950 px-4">
    <div class="w-full max-w-md">
      <!-- Logo -->
      <div class="text-center mb-8">
        <div class="inline-flex items-center justify-center mb-4">
          <img 
            src="@/assets/LogoBarber.png" 
            alt="Hernán Barber Logo" 
            class="h-20 w-auto"
          />
        </div>
        <h1 class="text-3xl font-display font-bold text-white mb-2">
          Panel de Administración
        </h1>
        <p class="text-gray-400">
          Ingresá para gestionar las reservas
        </p>
      </div>

      <!-- Login Card -->
      <Card class="p-8">
        <form @submit.prevent="handleLogin">
          <BaseInput
            v-model="password"
            type="password"
            label="Contraseña"
            placeholder="Ingresá tu contraseña"
            :error="error"
            required
            @keyup.enter="handleLogin"
          />

          <div class="mt-6">
            <BaseButton
              type="submit"
              full-width
              :loading="isLoading"
              :disabled="isLoading"
            >
              {{ isLoading ? 'Verificando...' : 'Ingresar' }}
            </BaseButton>
          </div>
        </form>
      </Card>

      <!-- Back to home -->
      <div class="text-center mt-6">
        <router-link
          to="/"
          class="text-gray-400 hover:text-white transition-colors text-sm"
        >
          ← Volver al sitio público
        </router-link>
      </div>
    </div>
  </div>
</template>
