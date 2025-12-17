import { ref } from 'vue'
import * as authService from '@/services/authService'

const isAuthenticated = ref(false)
const user = ref(null)

export function useAuth() {
  const login = async (email, password) => {
    try {
      const response = await authService.login(email, password)
      
      if (response.ok) {
        user.value = response.data.user
        isAuthenticated.value = true
        localStorage.setItem('isAuthenticated', 'true')
        // Persist token if provided
        if (response?.data?.token) {
          try { localStorage.setItem('admin_token', response.data.token) } catch {}
        }
        return { success: true }
      }
      
      return { success: false, error: 'Credenciales incorrectas' }
    } catch (error) {
      console.error('Error en login:', error)
      return { success: false, error: error.message || 'Error al iniciar sesión' }
    }
  }

  const logout = async () => {
    try {
      await authService.logout()
    } catch (error) {
      console.error('Error en logout:', error)
    } finally {
      user.value = null
      isAuthenticated.value = false
      localStorage.removeItem('isAuthenticated')
      try { localStorage.removeItem('admin_token') } catch {}
    }
  }

  const checkAuth = async () => {
    const savedAuth = localStorage.getItem('isAuthenticated')
    
    if (!savedAuth) {
      isAuthenticated.value = false
      return false
    }

    // For now, trust localStorage - Sanctum maintains session via cookies
    isAuthenticated.value = true
    return true
  }

  return {
    isAuthenticated,
    user,
    login,
    logout,
    checkAuth
  }
}
