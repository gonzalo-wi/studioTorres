import { ref, computed } from 'vue'
import { barberApiClient } from '@/services/apiClient'

const barberToken = ref(localStorage.getItem('barber_token') || null)
const barber = ref(null)
const loading = ref(false)
const error = ref(null)

export function useBarberAuth() {
  const isAuthenticated = computed(() => !!barberToken.value)

  const login = async (email, password) => {
    loading.value = true
    error.value = null

    try {
      const response = await barberApiClient.post('/barber/login', {
        email,
        password
      })

      barberToken.value = response.token
      barber.value = response.barber
      localStorage.setItem('barber_token', response.token)
      
      return true
    } catch (err) {
      error.value = err.message || 'Error al iniciar sesión'
      return false
    } finally {
      loading.value = false
    }
  }

  const logout = async () => {
    loading.value = true

    try {
      if (barberToken.value) {
        await barberApiClient.post('/barber/logout')
      }
    } catch (err) {
      console.error('Error al cerrar sesión:', err)
    } finally {
      barberToken.value = null
      barber.value = null
      localStorage.removeItem('barber_token')
      loading.value = false
    }
  }

  const fetchBarberProfile = async () => {
    if (!barberToken.value) return

    loading.value = true
    error.value = null

    try {
      const response = await barberApiClient.get('/barber/me')
      barber.value = response.barber
    } catch (err) {
      error.value = err.message
      // Si falla la autenticación, cerrar sesión
      if (err.message?.includes('Unauthenticated') || err.message?.includes('401')) {
        await logout()
      }
    } finally {
      loading.value = false
    }
  }

  return {
    barberToken,
    barber,
    loading,
    error,
    isAuthenticated,
    login,
    logout,
    fetchBarberProfile
  }
}
