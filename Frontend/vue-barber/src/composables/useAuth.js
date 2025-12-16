import { ref } from 'vue'

const isAuthenticated = ref(false)
const token = ref(null)

export function useAuth() {
  const login = (password) => {
    const adminPassword = import.meta.env.VITE_ADMIN_PASSWORD || 'admin123'
    
    if (password === adminPassword) {
      const fakeToken = `token_${Date.now()}`
      localStorage.setItem('auth_token', fakeToken)
      token.value = fakeToken
      isAuthenticated.value = true
      return { success: true }
    }
    
    return { success: false, error: 'Contraseña incorrecta' }
  }

  const logout = () => {
    localStorage.removeItem('auth_token')
    token.value = null
    isAuthenticated.value = false
  }

  const checkAuth = () => {
    const savedToken = localStorage.getItem('auth_token')
    if (savedToken) {
      token.value = savedToken
      isAuthenticated.value = true
      return true
    }
    return false
  }

  return {
    isAuthenticated,
    token,
    login,
    logout,
    checkAuth
  }
}
