import { get } from './apiClient'

/**
 * Get all clients
 */
export async function fetchClients(params = {}) {
  const response = await get('/admin/clients', params)
  return response.data
}

/**
 * Get client by ID with appointment history
 */
export async function fetchClientById(id) {
  const response = await get(`/admin/clients/${id}`)
  return response.data
}

/**
 * Search clients by appointment data (phone, name, email)
 */
export async function searchClientsByAppointments(search) {
  const response = await get('/admin/clients/search/appointments', { search })
  return response.data || []
}
