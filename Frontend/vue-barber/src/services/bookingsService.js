import { get, post, put } from './apiClient'

/**
 * Get all services
 */
export async function fetchServices() {
  const response = await get('/services')
  return response.data || []
}

/**
 * Get available slots for a date and service
 */
export async function fetchAvailableSlots(date, serviceId, barberId = null) {
  const params = { date, service_id: serviceId }
  if (barberId) {
    params.barber_id = barberId
  }
  const response = await get('/availability', params)
  return response.data?.slots || response.data || []
}

/**
 * Create a new booking
 */
export async function createBooking(bookingData) {
  const response = await post('/appointments', bookingData)
  return response.data
}

/**
 * Get booking by public code
 */
export async function fetchBookingByCode(publicCode) {
  const response = await get(`/appointments/${publicCode}`)
  const a = response.data
  
  // Parse date and time from starts_at
  // Laravel serializes datetime as ISO 8601: "2025-12-18T10:30:00.000000Z"
  let date = ''
  let time = ''
  if (a.starts_at) {
    const dateObj = new Date(a.starts_at)
    // Format as YYYY-MM-DD
    date = dateObj.toISOString().split('T')[0]
    // Format as HH:MM
    time = dateObj.toTimeString().substring(0, 5)
  }
  
  // Map backend structure to frontend format
  return {
    id: a.id,
    publicCode: a.public_code,
    name: a.client_name,
    phone: a.client_phone,
    email: a.client_email,
    date,
    time,
    service: a.service_id,
    serviceName: a.service?.title || '',
    barber_id: a.barber_id,
    barberName: a.barber?.name || '',
    notes: a.notes,
    status: a.status
  }
}

// ============================================
// ADMIN ENDPOINTS (require authentication)
// ============================================

/**
 * Fetch all bookings (admin o barber según rol)
 */
export async function fetchBookings(filters = {}) {
  const userRole = localStorage.getItem('user_role')
  
  let response
  if (userRole === 'BARBER') {
    response = await get('/admin/barber-panel/appointments', filters)
  } else {
    response = await get('/admin/appointments', filters)
  }
  
  // Handle both array and paginated responses
  const payload = Array.isArray(response.data)
    ? response.data
    : (response?.data?.data || [])

  // Normalize backend fields to UI expectations
  return payload.map(a => {
    // Parse date and time from starts_at (Laravel serializes datetime as ISO 8601)
    let date = ''
    let time = ''
    if (a.starts_at) {
      const dateObj = new Date(a.starts_at)
      date = dateObj.toISOString().split('T')[0]
      time = dateObj.toTimeString().substring(0, 5)
    }
    
    return {
      id: a.id,
      name: a.client_name || a.name || '',
      phone: a.client_phone || a.phone || '',
      date,
      time,
      service: a.service_id || (a.service ? a.service.id : undefined),
      serviceName: (a.service && a.service.title) || a.service_name || '',
      barber_id: a.barber_id,
      barberName: a.barber?.name || 'Sin asignar',
      status: a.status || 'PENDING'
    }
  })
}

/**
 * Fetch booking by ID (admin)
 */
export async function fetchBookingById(id) {
  const response = await get(`/admin/appointments/${id}`)
  const a = response.data
  
  // Parse date and time from starts_at
  let date = ''
  let time = ''
  if (a.starts_at) {
    const dateObj = new Date(a.starts_at)
    date = dateObj.toISOString().split('T')[0]
    time = dateObj.toTimeString().substring(0, 5)
  }
  
  // Map backend structure to frontend format
  return {
    id: a.id,
    publicCode: a.public_code,
    name: a.client_name,
    phone: a.client_phone,
    email: a.client_email,
    date,
    time,
    service: a.service_id,
    serviceName: a.service?.title || '',
    barber_id: a.barber_id,
    barberName: a.barber?.name || '',
    notes: a.notes,
    status: a.status
  }
}

/**
 * Update booking status (admin o barber según rol)
 */
export async function updateBookingStatus(id, status, date = null, time = null) {
  const userRole = localStorage.getItem('user_role')
  const body = { status }
  
  if (date) body.date = date
  if (time) body.time = time
  
  let response
  if (userRole === 'BARBER') {
    response = await put(`/admin/barber-panel/appointments/${id}/status`, body)
  } else {
    response = await put(`/admin/appointments/${id}/status`, body)
  }
  
  const a = response.data.appointment || response.data
  
  // Parse ISO 8601 datetime to separate date/time
  const dateObj = new Date(a.starts_at)
  const parsedDate = dateObj.toISOString().split('T')[0]
  const parsedTime = dateObj.toTimeString().substring(0, 5)
  
  return {
    id: a.id,
    publicCode: a.public_code,
    name: a.client_name,
    phone: a.client_phone,
    email: a.client_email,
    date: parsedDate,
    time: parsedTime,
    service: a.service_id,
    serviceName: a.service?.name || 'Servicio no disponible',
    barber_id: a.barber_id,
    barberName: a.barber?.name || 'Barbero no disponible',
    notes: a.notes,
    status: a.status
  }
}

/**
 * Confirm booking (admin)
 */
export async function confirmBooking(id) {
  return updateBookingStatus(id, 'CONFIRMED')
}

/**
 * Cancel booking (admin)
 */
export async function cancelBooking(id) {
  return updateBookingStatus(id, 'CANCELLED')
}

/**
 * Get dashboard stats (admin o barber según rol)
 */
export async function getBookingStats() {
  const userRole = localStorage.getItem('user_role')
  
  if (userRole === 'BARBER') {
    const response = await get('/admin/barber-panel/stats')
    return response.data
  }
  
  const response = await get('/admin/dashboard/stats')
  return response.data
}

/**
 * Get monthly stats (admin)
 */
export async function getMonthlyStats() {
  const response = await get('/admin/dashboard/monthly-stats')
  return response.data
}

// ============================================
// HELPER FUNCTIONS (for backward compatibility)
// ============================================

/**
 * Get all services (synchronous wrapper - use fetchServices for async)
 * Returns empty array, call fetchServices() to get actual data
 */
export function getAllServices() {
  // For backward compatibility - components should use fetchServices() instead
  console.warn('getAllServices is deprecated, use fetchServices() instead')
  return []
}

/**
 * Get service info by ID
 */
export function getServiceInfo(serviceId) {
  // This needs to be called after services are loaded
  console.warn('getServiceInfo is deprecated, use fetchServices() and filter')
  return null
}
