const BASE_URL = import.meta.env.VITE_API_BASE_URL || 'http://localhost:8000/api'

/**
 * Get CSRF token from cookie
 */
function getCsrfToken() {
  const cookies = document.cookie.split(';')
  const csrfCookie = cookies.find(cookie => cookie.trim().startsWith('XSRF-TOKEN='))
  if (csrfCookie) {
    return decodeURIComponent(csrfCookie.split('=')[1])
  }
  return null
}

/**
 * Main fetch wrapper for API calls
 */
export async function fetchAPI(endpoint, options = {}) {
  const url = `${BASE_URL}${endpoint}`
  
  const headers = {
    'Content-Type': 'application/json',
    'Accept': 'application/json',
    ...options.headers
  }

  // Add CSRF token for non-GET requests
  if (options.method && options.method !== 'GET') {
    const csrfToken = getCsrfToken()
    if (csrfToken) {
      headers['X-XSRF-TOKEN'] = csrfToken
    }
  }

  const config = {
    headers,
    ...options
  }

  // Include credentials for Sanctum auth
  if (options.withCredentials !== false) {
    config.credentials = 'include'
  }

  // If we have an admin bearer token, attach it for admin endpoints
  try {
    const isAdminEndpoint = endpoint.startsWith('/admin')
    const token = localStorage.getItem('admin_token')
    if (isAdminEndpoint && token) {
      config.headers = {
        ...config.headers,
        Authorization: `Bearer ${token}`
      }
    }
  } catch {}

  try {
    const response = await fetch(url, config)
    
    // Handle non-JSON responses
    const contentType = response.headers.get('content-type')
    if (!contentType || !contentType.includes('application/json')) {
      throw new Error(`API returned non-JSON response: ${response.statusText}`)
    }

    const data = await response.json()

    if (!response.ok) {
      // Laravel API error format
      throw {
        status: response.status,
        message: data.message || 'Error en la solicitud',
        errors: data.errors || {},
        data: data
      }
    }

    return data
  } catch (error) {
    // Network errors
    if (error.message === 'Failed to fetch') {
      throw new Error('No se pudo conectar con el servidor. Verifica tu conexión.')
    }
    
    throw error
  }
}

/**
 * GET request
 */
export async function get(endpoint, params = {}) {
  const queryString = new URLSearchParams(params).toString()
  const url = queryString ? `${endpoint}?${queryString}` : endpoint
  
  return fetchAPI(url, { method: 'GET' })
}

/**
 * POST request
 */
export async function post(endpoint, body = {}) {
  return fetchAPI(endpoint, {
    method: 'POST',
    body: JSON.stringify(body)
  })
}

/**
 * PUT request
 */
export async function put(endpoint, body = {}) {
  return fetchAPI(endpoint, {
    method: 'PUT',
    body: JSON.stringify(body)
  })
}

/**
 * DELETE request
 */
export async function del(endpoint) {
  return fetchAPI(endpoint, {
    method: 'DELETE'
  })
}

/**
 * Get CSRF cookie for Sanctum (call before authenticated requests)
 */
export async function getCsrfCookie() {
  const csrfUrl = import.meta.env.VITE_API_BASE_URL?.replace('/api', '') || 'http://localhost:8000'
  
  await fetch(`${csrfUrl}/sanctum/csrf-cookie`, {
    credentials: 'include'
  })
}
