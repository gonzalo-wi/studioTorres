import * as apiClient from './apiClient'

const BACKEND_URL = import.meta.env.VITE_API_BASE_URL?.replace('/api', '') || 'http://localhost:8000'

/**
 * Admin authentication service
 */
export async function login(email, password) {
  // Get CSRF cookie first from Laravel
  await fetch(`${BACKEND_URL}/sanctum/csrf-cookie`, {
    credentials: 'include',
    headers: {
      'Accept': 'application/json'
    }
  })
  
  // Then login
  const response = await apiClient.post('/admin/login', { email, password })
  // If token-based auth is returned, store token for subsequent admin calls
  if (response?.ok && response?.data?.token) {
    try {
      localStorage.setItem('admin_token', response.data.token)
    } catch {}
  }
  return response
}

export async function logout() {
  const response = await apiClient.post('/admin/logout')
  try { localStorage.removeItem('admin_token') } catch {}
  return response
}

export async function checkAuth() {
  try {
    // Try to get services list to verify if authenticated
    const response = await apiClient.get('/admin/services')
    return { authenticated: true, data: response }
  } catch (error) {
    return { authenticated: false }
  }
}
