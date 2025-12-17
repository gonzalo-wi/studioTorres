import { get, post, put, del } from './apiClient'

export async function fetchBarbers() {
  const response = await get('/barbers')
  return response.data || []
}

export async function fetchBarber(id) {
  const response = await get(`/admin/barbers/${id}`)
  return response.data
}

export async function createBarber(payload) {
  const response = await post('/admin/barbers', payload)
  return response.data
}

export async function updateBarber(id, payload) {
  const response = await put(`/admin/barbers/${id}`, payload)
  return response.data
}

export async function deleteBarber(id) {
  const response = await del(`/admin/barbers/${id}`)
  return response.data
}

export async function updateSchedules(id, schedules) {
  const response = await post(`/admin/barbers/${id}/schedules`, { schedules })
  return response.data
}

export async function fetchBarberAppointments(id, params = {}) {
  const q = new URLSearchParams(params).toString()
  const response = await get(`/admin/barbers/${id}/appointments${q ? `?${q}` : ''}`)
  return response.data
}

export async function fetchBarberEarnings(id, params = {}) {
  const q = new URLSearchParams(params).toString()
  const response = await get(`/admin/barbers/${id}/earnings${q ? `?${q}` : ''}`)
  return response.data
}
