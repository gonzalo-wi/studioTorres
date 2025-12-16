// Mock data para desarrollo - reemplazar con API real
const mockBookings = ref([
  {
    id: '1',
    service: 'corte',
    serviceName: 'Corte de Cabello',
    date: '2025-12-16',
    time: '10:00',
    name: 'Juan Pérez',
    phone: '11 2345 6789',
    observations: '',
    status: 'CONFIRMED',
    createdAt: '2025-12-15T10:00:00'
  },
  {
    id: '2',
    service: 'corte-barba',
    serviceName: 'Corte + Barba',
    date: '2025-12-16',
    time: '14:00',
    name: 'Martín González',
    phone: '11 8765 4321',
    observations: 'Preferencia por fade bajo',
    status: 'PENDING',
    createdAt: '2025-12-15T11:30:00'
  }
])

import { ref } from 'vue'

const SERVICES = {
  'corte': { name: 'Corte de Cabello', duration: 30, price: 3500 },
  'corte-barba': { name: 'Corte + Barba', duration: 60, price: 5500 },
  'barba': { name: 'Arreglo de Barba', duration: 30, price: 2500 },
  'afeitado': { name: 'Afeitado Tradicional', duration: 30, price: 3000 }
}

/**
 * Obtiene todas las reservas
 */
export async function fetchBookings(filters = {}) {
  // Simular delay de API
  await new Promise(resolve => setTimeout(resolve, 500))
  
  let filtered = mockBookings.value

  if (filters.date) {
    filtered = filtered.filter(b => b.date === filters.date)
  }

  if (filters.status) {
    filtered = filtered.filter(b => b.status === filters.status)
  }

  if (filters.service) {
    filtered = filtered.filter(b => b.service === filters.service)
  }

  return [...filtered]
}

/**
 * Obtiene una reserva por ID
 */
export async function fetchBookingById(id) {
  await new Promise(resolve => setTimeout(resolve, 300))
  
  const booking = mockBookings.value.find(b => b.id === id)
  if (!booking) {
    throw new Error('Reserva no encontrada')
  }
  
  return { ...booking }
}

/**
 * Crea una nueva reserva
 */
export async function createBooking(bookingData) {
  await new Promise(resolve => setTimeout(resolve, 800))
  
  const service = SERVICES[bookingData.service]
  if (!service) {
    throw new Error('Servicio inválido')
  }

  const newBooking = {
    id: (mockBookings.value.length + 1).toString(),
    ...bookingData,
    serviceName: service.name,
    status: 'PENDING',
    createdAt: new Date().toISOString()
  }

  mockBookings.value.push(newBooking)
  
  return { ...newBooking }
}

/**
 * Actualiza una reserva
 */
export async function updateBooking(id, updates) {
  await new Promise(resolve => setTimeout(resolve, 500))
  
  const index = mockBookings.value.findIndex(b => b.id === id)
  if (index === -1) {
    throw new Error('Reserva no encontrada')
  }

  mockBookings.value[index] = {
    ...mockBookings.value[index],
    ...updates,
    updatedAt: new Date().toISOString()
  }

  return { ...mockBookings.value[index] }
}

/**
 * Cancela una reserva
 */
export async function cancelBooking(id) {
  return updateBooking(id, { status: 'CANCELLED' })
}

/**
 * Confirma una reserva
 */
export async function confirmBooking(id) {
  return updateBooking(id, { status: 'CONFIRMED' })
}

/**
 * Obtiene estadísticas de reservas
 */
export async function getBookingStats() {
  await new Promise(resolve => setTimeout(resolve, 300))
  
  const today = new Date().toISOString().split('T')[0]
  
  const stats = {
    today: mockBookings.value.filter(b => b.date === today && b.status !== 'CANCELLED').length,
    pending: mockBookings.value.filter(b => b.status === 'PENDING').length,
    confirmed: mockBookings.value.filter(b => b.status === 'CONFIRMED').length,
    cancelled: mockBookings.value.filter(b => b.status === 'CANCELLED').length,
    total: mockBookings.value.length
  }

  return stats
}

/**
 * Obtiene info de servicios
 */
export function getServiceInfo(serviceId) {
  return SERVICES[serviceId] || null
}

/**
 * Obtiene todos los servicios
 */
export function getAllServices() {
  return Object.entries(SERVICES).map(([id, info]) => ({
    id,
    ...info
  }))
}
