import { get, post, put, del } from './apiClient'

/**
 * Get all public services (for clients)
 */
export async function fetchServices() {
  const response = await get('/services')
  return response.data || []
}

/**
 * Get single service by ID (for clients)
 */
export async function fetchService(id) {
  const response = await get(`/services/${id}`)
  return response.data
}

/**
 * Get all services (admin)
 */
export async function fetchAdminServices() {
  const response = await get('/admin/services')
  return response.data || []
}

/**
 * Create new service
 */
export async function createService(serviceData) {
  const response = await post('/admin/services', serviceData)
  return response.data
}

/**
 * Update service
 */
export async function updateService(id, serviceData) {
  const response = await put(`/admin/services/${id}`, serviceData)
  return response.data
}

/**
 * Delete service
 */
export async function deleteService(id) {
  const response = await del(`/admin/services/${id}`)
  return response.data
}
